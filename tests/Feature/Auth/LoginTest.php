<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->validUser = User::factory()->create([
        'email' => 'admin@gmail.com',
        'password' => Hash::make('admin'),
    ]);
});

// Pruebas de renderizado
test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
             ->assertViewIs('auth.login');
});

test('login screen contains required elements', function () {
    $response = $this->get('/login');

    $response->assertSee('Iniciar sesión')
             ->assertSee('Correo electrónico')
             ->assertSee('Contraseña')
             ->assertSee('Iniciar Sesión');
});

// Pruebas de autenticación
test('users can authenticate using valid credentials', function () {
    $response = $this->post('/login', [
        'email' => 'admin@gmail.com',
        'password' => 'admin',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/dashboard');
});

test('users cannot authenticate with invalid password', function () {
    $response = $this->post('/login', [
        'email' => 'admin@gmail.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('validation errors shown for empty fields', function () {
    $response = $this->post('/login', [
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

// Pruebas de funcionalidad
test('remember me functionality works', function () {
    $response = $this->post('/login', [
        'email' => 'admin@gmail.com',
        'password' => 'admin',
        'remember' => 'on',
    ]);

    $response->assertRedirect('/dashboard');
    
    $cookie = collect($response->headers->getCookies())
        ->first(fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_'));
    
    expect($cookie)->not->toBeNull();
});

// Pruebas de UI específicas
test('password field has toggle functionality', function () {
    $response = $this->get('/login');

    $response->assertSee('name="password"', false)
             ->assertSee('type="password"', false)
             ->assertSee('Mostrar', false); 
});

test('google login button is present', function () {
    $response = $this->get('/login');
    
    $response->assertSee('href="'.route('login.google.redirect').'"', false)
             ->assertSee('Continuar con Google', false)
             ->assertSee('svg class="h-5 w-5 mr-2"', false);
});