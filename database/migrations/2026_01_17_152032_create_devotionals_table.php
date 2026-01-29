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
        Schema::create('devotionals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subheading')->nullable();
            $table->longText('content');
            $table->string('author')->nullable();
            $table->longText('key_verse')->nullable();
            $table->longText('verses')->nullable();
            $table->date('date')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('draft'); // draft, pending, published
            $table->longText('application_note')->nullable();
            $table->longText('prayer_note')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devotionals');
    }
};
