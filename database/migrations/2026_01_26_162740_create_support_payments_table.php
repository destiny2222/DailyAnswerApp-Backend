<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['one_time', 'recurring']);
            $table->enum('interval', ['monthly', 'yearly'])->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('subscription_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_payments');
    }
};
