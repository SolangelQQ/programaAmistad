<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buddy_id')->constrained('buddies')->onDelete('cascade');
            $table->foreignId('peer_buddy_id')->constrained('buddies')->onDelete('cascade');
            $table->string('status')->default('Emparejado');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Asegurar relaciones únicas
            $table->unique(['buddy_id', 'peer_buddy_id']);
            
            // Índices para mejor performance
            $table->index('status');
            $table->index('start_date');

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};