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
        Schema::create('refrigerators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bank_id')->constrained()->onDelete('cascade');
            $table->string('refrigerator_code')->unique();
            $table->string('location');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['blood_bank_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refrigerators');
    }
};
