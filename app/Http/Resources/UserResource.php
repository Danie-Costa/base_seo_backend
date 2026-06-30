<?php

namespace App\Http\Resources;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordBrevo;
use App\Http\Resources\Resource;


class UserResource extends Resource
{
    public array $filters = ['name', 'email'];
    public array $includes = [];
    public array $sorts = ['created_at', 'name'];

    public static function rules($method = 'store')
    {
        return match ($method) {
            'store' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ],
            'update' => [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email',
                'password' => 'nullable|min:6',
            ],
            default => []
        };
    }

    public function beforeStore($data)
    {
        $data['password'] = bcrypt($data['password']);
        return $data;
    }

    // 🔥 BEFORE UPDATE
    public function beforeUpdate($item, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }

    public function afterStore($item)
    {
    }

    public function afterUpdate($item, $data)
    {
    }
    public function beforeDelete($item)
    {
        if ($item->email === 'admin@admin.com') {
            abort(403, 'Cannot delete main admin');
        }
    }

    public function afterDelete($item)
    {
    }

}
