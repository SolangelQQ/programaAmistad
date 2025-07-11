<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buddies', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('type', ['buddy', 'peer_buddy']);
            $table->string('disability')->nullable(); // Solo para buddies
            $table->integer('age');
            $table->string('phone');
            $table->string('address');
            $table->string('email')->nullable();
            $table->text('interests')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
            $table->boolean('is_leader')->default(false)->after('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buddies');
    }
};