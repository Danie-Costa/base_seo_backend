<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    const MAX_SIZE = 204800; // 200KB

    /**
     * Converte imagem para WebP com no máximo 200KB
     * Retorna o path relativo ao disco 'public'
     */
    public static function convertToWebP(UploadedFile $file, string $dir): string
    {
        $source = $file->getPathname();
        $info = @getimagesize($source);
        $mime = $info['mime'] ?? $file->getMimeType();

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/gif' => @imagecreatefromgif($source),
            'image/webp' => @imagecreatefromwebp($source),
            default => throw new \RuntimeException('Formato não suportado: ' . $mime),
        };

        if (!$image) {
            throw new \RuntimeException('Falha ao processar imagem');
        }

        // Preserva transparência PNG
        if ($mime === 'image/png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        $filename = uniqid() . '.webp';
        $destPath = $dir . '/' . $filename;
        $destFull = Storage::disk('public')->path($destPath);

        // Garante que o diretório existe
        Storage::disk('public')->makeDirectory($dir);

        // Comprime com qualidade decrescente até caber em 200KB
        $quality = 85;
        do {
            ob_start();
            imagewebp($image, null, $quality);
            $data = ob_get_clean();
            $quality -= 5;
        } while (strlen($data) > self::MAX_SIZE && $quality >= 10);

        file_put_contents($destFull, $data);
        imagedestroy($image);

        return $destPath;
    }

    /**
     * Processa array de uploads e retorna array de paths
     */
    public static function uploadMultiple(array $files, string $dir, int $max = 10): array
    {
        $paths = [];
        $count = 0;

        foreach ($files as $file) {
            if ($count >= $max) break;
            if (!$file instanceof UploadedFile || !$file->isValid()) continue;

            $paths[] = self::convertToWebP($file, $dir);
            $count++;
        }

        return $paths;
    }
}
