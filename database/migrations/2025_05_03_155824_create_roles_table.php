<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->enum('city', ['La Paz', 'Cochabamba'])->nullable()->after('name');
        });

        DB::table('roles')->insert([
            ['name' => 'Encargado del Programa Amistad', 'description' => 'Administrador general del programa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coordinador de Gestión Humana', 'description' => 'Coordina recursos humanos', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Líder de Actividades', 'description' => 'Gestiona actividades del programa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Líder de Buddies', 'description' => 'Coordina a los Buddies', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Líder de PeerBuddies', 'description' => 'Coordina a los PeerBuddies', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Líder de Tutores', 'description' => 'Coordina a los tutores', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // tabla users para usar role_id en lugar de role
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('password');
            
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('city');
        });
        
        Schema::dropIfExists('roles');
    }
};