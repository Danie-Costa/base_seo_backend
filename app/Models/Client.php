<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_id',
        'name',
        'email',
        'cnpj',
        'external_reference',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
