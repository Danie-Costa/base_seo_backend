<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('password')->after('email')->nullable();
            $table->rememberToken();

            $table->dropUnique(['email']);
            $table->dropUnique(['cnpj']);

            $table->unique(['company_id', 'email']);
            $table->unique(['company_id', 'cnpj']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token']);

            $table->dropUnique(['company_id', 'email']);
            $table->dropUnique(['company_id', 'cnpj']);

            $table->unique('email');
            $table->unique('cnpj');
        });
    }
};
