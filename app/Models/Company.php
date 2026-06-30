<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'address',
        'primary_phone',
        'primary_email',
    ];

    public function users()
    {
        return $this->hasMany(User::class)->orderBy('name');
    }

    public function socials()
    {
        return $this->hasMany(Social::class)->orderBy('name');
    }
}
