<?php

namespace App\Http\Resources;

class Resource
{
    public array $filters = [];
    public array $includes = [];
    public array $sorts = ['created_at'];



     public static function rules($method = 'store')
    {
        return match ($method) {
            'store' => [
               
            ],
            'update' => [
               
            ],
            default => []
        };
    }

    public function beforeStore($data)
    {
        return $data;
    }
    public function beforeUpdate($item, $data)
    {
        return $data;
    }

    public function afterStore($item)
    {

    }

    public function afterUpdate($item, $data)
    {

    }
    public function beforeDelete($item)
    {
    }

    public function afterDelete($item)
    {
    }
}