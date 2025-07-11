<?php

use App\Models\User;
use App\Models\Role;
use function Pest\Laravel\{get, actingAs};

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@test.com',
        'role_id' => Role::firstOrCreate(['name' => 'Administrador'])->id
    ]);
    
    $this->regularUser = User::factory()->create([
        'email' => 'user@test.com',
        'role_id' => Role::firstOrCreate(['name' => 'Usuario'])->id
    ]);
});

test('dashboard can be rendered for authenticated users', function () {
    actingAs($this->admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertViewIs('dashboard.index');
});

test('dashboard shows welcome card with user name', function () {
    actingAs($this->admin)
        ->get('/dashboard')
        ->assertSee('Bienvenido, '.$this->admin->name);
});

test('dashboard displays stats cards', function () {
    $response = actingAs($this->admin)
        ->get('/dashboard');
    
    // Verificar que existen los cards principales
    $response->assertSee('Total de Actividades')
             ->assertSee('Total Peerbuddies')
             ->assertSee('Amistades activas')
             ->assertSee('Actividades este mes');
});

test('dashboard shows calendar section', function () {
    actingAs($this->admin)
        ->get('/dashboard')
        ->assertSee('Calendario')
        ->assertSee('calendar.google.com');
});

test('dashboard includes required scripts', function () {
    $response = actingAs($this->admin)
        ->get('/dashboard');
    
    // Verificar scripts principales
    $response->assertSee('apis.google.com/js/api.js', false)
             ->assertSee('js/calendar.js', false);
});

test('dashboard has interactive elements', function () {
    $response = actingAs($this->admin)
        ->get('/dashboard');
    
    // Verificar elementos interactivos
    $response->assertSee('x-data', false) // AlpineJS
             ->assertSee('x-show', false);
});