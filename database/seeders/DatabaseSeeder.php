<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => '123456',
            'rule' => 'admin',
        ]);

        $this->call(CompanySeeder::class);
        $this->call(PlanSeeder::class);
    }
}
