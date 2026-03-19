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
        Schema::table('users', function (Blueprint $column) {
            $column->text('google2fa_secret')->nullable();
            $column->text('backup_codes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $column) {
            $column->dropColumn('google2fa_secret');
            $column->dropColumn('backup_codes');
        });
    }
};
