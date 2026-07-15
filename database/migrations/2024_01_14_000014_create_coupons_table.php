<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->decimal('discount', 10, 2);
            $table->boolean('is_percent')->default(false);
            $table->integer('max_uses');
            $table->integer('used_count')->default(0);
            $table->string('external_reference', 255)->nullable();
            $table->dateTime('valid_until');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
