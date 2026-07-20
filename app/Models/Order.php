<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'project_id', 'external_reference',
        'total', 'total_discount', 'coupon_id', 'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
