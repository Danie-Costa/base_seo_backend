<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['pix', 'credit_card', 'debit_card']);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_configs');
    }
};
