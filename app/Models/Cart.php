<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'external_reference',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
