<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Social;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'address',
        'primary_phone',
        'primary_email',
        'logo',
        'plan_id',
        'plan_status',
        'plan_started_at',
        'plan_expires_at',
        'plan_canceled_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class)->orderBy('name');
    }

    public function socials()
    {
        return $this->hasMany(Social::class)->orderBy('name');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Project::class, 'id', 'project_id');
    }
}
