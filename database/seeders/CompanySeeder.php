<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Tech Solutions Ltda',
            'cnpj' => '11222333000181',
            'address' => 'Av. Paulista, 1000, Sao Paulo - SP',
            'primary_phone' => '(11) 99999-8888',
            'primary_email' => 'contato@techsolutions.com.br',
        ]);

        Company::create([
            'name' => 'Digital Plus ME',
            'cnpj' => '44555666000199',
            'address' => 'Rua Augusta, 500, Sao Paulo - SP',
            'primary_phone' => '(11) 97777-6666',
            'primary_email' => 'admin@digitalplus.com.br',
        ]);
    }
}
