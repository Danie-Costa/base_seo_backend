<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('price', 19, 4);
            $table->integer('fee');
            $table->decimal('price_fee', 19, 4);
            $table->enum('status', ['approved', 'pending', 'failure'])->default('pending');
            $table->enum('return_type', ['webhook', 'redirect'])->default('webhook');
            $table->string('external_reference')->nullable();
            $table->string('internal_reference')->nullable();
            $table->string('preference_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->text('qr_code')->nullable();
            $table->text('qr_code_base64')->nullable();
            $table->string('ticket_url', 255)->nullable();
            $table->string('redirect_success_url', 255)->nullable();
            $table->string('redirect_failure_url', 255)->nullable();
            $table->string('redirect_pending_url', 255)->nullable();
            $table->string('webhook_url', 255)->nullable();
            $table->json('payer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
