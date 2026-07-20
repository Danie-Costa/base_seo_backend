<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'company_id', 'project_id', 'vendor_id',
        'title', 'price', 'fee', 'price_fee',
        'status', 'return_type',
        'external_reference', 'internal_reference',
        'preference_id', 'payment_id',
        'payment_type', 'payment_method_id',
        'qr_code', 'qr_code_base64', 'ticket_url',
        'redirect_success_url', 'redirect_failure_url', 'redirect_pending_url',
        'webhook_url', 'payer',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:4',
            'price_fee' => 'decimal:4',
            'payer' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
