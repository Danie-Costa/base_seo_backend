<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'external_reference',
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
