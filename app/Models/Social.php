<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Social extends Model
{
    use SoftDeletes;

    public const TYPES = [
        'phone' => [
            'label' => 'Telefone',
            'icon' => 'fa fa-phone',
            'placeholder' => '(11) 99999-9999',
        ],
        'whatsapp' => [
            'label' => 'WhatsApp',
            'icon' => 'fa fa-whatsapp',
            'placeholder' => '(11) 99999-9999',
        ],
        'email' => [
            'label' => 'Email',
            'icon' => 'fa fa-envelope',
            'placeholder' => 'contato@empresa.com',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'icon' => 'fa fa-facebook',
            'placeholder' => 'https://facebook.com/suaempresa',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'icon' => 'fa fa-instagram',
            'placeholder' => 'https://instagram.com/suaempresa',
        ],
    ];

    protected $fillable = [
        'company_id',
        'name',
        'title',
        'icon',
        'link',
        'header',
        'sidebar',
        'footer',
    ];

    protected $casts = [
        'header' => 'boolean',
        'sidebar' => 'boolean',
        'footer' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
