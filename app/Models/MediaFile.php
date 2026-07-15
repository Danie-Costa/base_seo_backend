<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'project_id',
        'title',
        'path',
        'external_reference',
    ];
}
