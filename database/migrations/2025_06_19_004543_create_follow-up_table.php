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
        Schema::create('friendship_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('friendship_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que hace el seguimiento
            
            // Evaluaciones (1-5)
            $table->tinyInteger('buddy_progress')->unsigned();
            $table->tinyInteger('peer_buddy_progress')->unsigned();
            $table->tinyInteger('relationship_quality')->unsigned();
            
            // Observaciones
            $table->text('goals_achieved')->nullable();
            $table->text('challenges_faced')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['friendship_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendship_follow_ups');
    }
};