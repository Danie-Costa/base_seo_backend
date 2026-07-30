<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'client';

    protected $fillable = [
        'company_id',
        'project_id',
        'name',
        'email',
        'cnpj',
        'password',
        'external_reference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
