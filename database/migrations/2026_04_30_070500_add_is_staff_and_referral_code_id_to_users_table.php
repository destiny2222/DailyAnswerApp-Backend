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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_staff')->default(false);
            $table->uuid('referral_code_id')->nullable();
            
            $table->foreign('referral_code_id')->references('id')->on('referral_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referral_code_id']);
            $table->dropColumn('referral_code_id');
            $table->dropColumn('is_staff');
        });
    }
};
