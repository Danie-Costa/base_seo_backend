<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    protected $fillable = [
        'company_id',
        'method',
        'discount_percent',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
