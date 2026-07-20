<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'name', 'email', 'external_reference',
        'fee', 'mp_user_id', 'mp_access_token', 'mp_refresh_token',
        'mp_public_key', 'mp_expires_in', 'mp_token_created_at',
    ];
}
