<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('name', 255);
            $table->string('title', 255);
            $table->string('link');
            $table->string('icon')->nullable();
            $table->boolean('header')->nullable()->default(false);
            $table->boolean('sidebar')->nullable()->default(false);
            $table->boolean('footer')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socials');
    }
};
