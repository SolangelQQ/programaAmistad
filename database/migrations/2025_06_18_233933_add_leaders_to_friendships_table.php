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
        Schema::table('friendships', function (Blueprint $table) {
            $table->unsignedBigInteger('buddy_leader_id')->nullable()->after('peer_buddy_id');
            $table->unsignedBigInteger('peer_buddy_leader_id')->nullable()->after('buddy_leader_id');
            
            // Agregar las claves foráneas
            $table->foreign('buddy_leader_id')->references('id')->on('buddies')->onDelete('set null');
            $table->foreign('peer_buddy_leader_id')->references('id')->on('buddies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('friendships', function (Blueprint $table) {
            $table->dropForeign(['buddy_leader_id']);
            $table->dropForeign(['peer_buddy_leader_id']);
            $table->dropColumn(['buddy_leader_id', 'peer_buddy_leader_id']);
        });
    }
};