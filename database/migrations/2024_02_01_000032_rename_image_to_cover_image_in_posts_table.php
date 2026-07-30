<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function ($table) {
            DB::statement('ALTER TABLE posts CHANGE image cover_image VARCHAR(255) DEFAULT NULL');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function ($table) {
            DB::statement('ALTER TABLE posts CHANGE cover_image image VARCHAR(255) DEFAULT NULL');
        });
    }
};
