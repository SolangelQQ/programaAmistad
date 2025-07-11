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
        Schema::create('friendship_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('friendship_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('buddy_attended')->default(false);
            $table->boolean('peer_buddy_attended')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índice único para evitar duplicados en misma fecha
            $table->unique(['friendship_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendship_attendances');
    }
};