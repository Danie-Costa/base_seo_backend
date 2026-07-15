<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\CompanyService;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $company = (new CompanyService)->myCompany();
        $dir = 'editor/' . $company->id;

        $path = ImageService::convertToWebP($request->file('image'), $dir);

        return response()->json([
            'url' => url('storage/' . $path),
            'path' => $path,
        ]);
    }
}
