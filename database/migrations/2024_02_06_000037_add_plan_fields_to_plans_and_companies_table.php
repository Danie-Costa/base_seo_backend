<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->enum('interval', ['monthly', 'semiannual', 'annual'])->default('monthly')->after('description');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('logo')->constrained()->nullOnDelete();
            $table->string('plan_status', 20)->default('none')->after('plan_id');
            $table->timestamp('plan_started_at')->nullable()->after('plan_status');
            $table->timestamp('plan_expires_at')->nullable()->after('plan_started_at');
            $table->timestamp('plan_canceled_at')->nullable()->after('plan_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['plan_status', 'plan_started_at', 'plan_expires_at', 'plan_canceled_at']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('interval');
        });
    }
};
