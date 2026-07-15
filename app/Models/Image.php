<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'gallery_id',
        'title',
        'path',
        'external_reference',
    ];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
