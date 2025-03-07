<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the existing table
        Schema::dropIfExists('reservations');

        // Recreate the table with the new constraint
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('time_slot', ['AM', 'PM']);
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
            
            // Add the new unique constraint
            $table->unique(['date', 'time_slot', 'status']);
        });
    }

    public function down(): void
    {
        // Drop and recreate with original schema
        Schema::dropIfExists('reservations');
        
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('time_slot', ['AM', 'PM']);
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
            
            // Original constraint
            $table->unique(['date', 'time_slot']);
        });
    }
};
