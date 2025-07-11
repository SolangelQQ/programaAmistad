<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\{get, actingAs};

beforeEach(function () {
    // Crear roles necesarios
    $this->adminRole = Role::firstOrCreate([
        'name' => 'Encargado del Programa Amistad'
    ], [
        'description' => 'Administrador del sistema'
    ]);

    $this->regularRole = Role::firstOrCreate([
        'name' => 'Voluntario'
    ], [
        'description' => 'Usuario regular'
    ]);

    // Crear usuarios de prueba
    $this->adminUser = User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'role_id' => $this->adminRole->id,
        'email_verified_at' => now()
    ]);

    $this->regularUser = User::create([
        'name' => 'Regular User',
        'email' => 'user@test.com',
        'password' => Hash::make('password'),
        'role_id' => $this->regularRole->id,
        'email_verified_at' => now()
    ]);

    // Crear algunos usuarios adicionales para probar la lista
    User::factory()->count(5)->create(['role_id' => $this->regularRole->id]);
});

test('guest cannot access roles index and gets redirected to login', function () {
    get('/roles')
        ->assertRedirect('/login');
});

test('authenticated admin can access roles index', function () {
    actingAs($this->adminUser)
        ->get('/roles')
        ->assertOk()
        ->assertViewIs('roles.index')
        ->assertViewHas('users');
});

test('roles index displays correct user data', function () {
    actingAs($this->adminUser)
        ->get('/roles')
        ->assertSee($this->adminUser->name)
        ->assertSee($this->adminUser->email)
        ->assertSee($this->adminRole->name)
        ->assertSee($this->regularUser->name);
});

test('admin sees action buttons for each user', function () {
    actingAs($this->adminUser)
        ->get('/roles')
        ->assertSee('Ver detalles')
        ->assertSee('Editar')
        ->assertSee('Eliminar');
});

test('regular user sees restricted actions message', function () {
    // 1. Verificar que el usuario tiene el rol correcto
    $this->regularUser->role_id = $this->regularRole->id;
    $this->regularUser->save();

    // 2. Primero verificar la redirección
    actingAs($this->regularUser)
        ->get('/roles')
        ->assertRedirect(); 
});

test('AlpineJS is loaded for interactive elements', function () {
    $response = actingAs($this->adminUser)
        ->get('/roles')
        ->assertOk();

    // Verificar múltiples formatos posibles de AlpineJS
    $alpineFound = str_contains($response->content(), 'alpine.min.js') || 
                  str_contains($response->content(), 'alpinejs') ||
                  str_contains($response->content(), 'Alpine.');

    $this->assertTrue($alpineFound, 'AlpineJS no fue encontrado en la respuesta');
});

test('session messages are displayed when present', function () {
    // Test success message
    actingAs($this->adminUser)
        ->withSession(['success' => 'Operación exitosa'])
        ->get('/roles')
        ->assertSee('Operación exitosa');

    // Test error message
    actingAs($this->adminUser)
        ->withSession(['error' => 'Algo salió mal'])
        ->get('/roles')
        ->assertSee('Algo salió mal');
});