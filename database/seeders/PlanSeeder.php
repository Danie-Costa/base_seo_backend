<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Starter',
            'description' => 'Ideal para pequenos negócios que estão começando sua presença digital.',
            'price' => 97.00,
            'interval' => 'monthly',
        ]);

        Plan::create([
            'name' => 'Profissional',
            'description' => 'Para empresas que buscam crescimento com SEO e conteúdo estratégico.',
            'price' => 197.00,
            'interval' => 'monthly',
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'description' => 'Solução completa para empresas que querem dominar o mercado digital.',
            'price' => 397.00,
            'interval' => 'monthly',
        ]);
    }
}
