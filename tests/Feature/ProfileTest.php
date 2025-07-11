<?php

use App\Models\User;
use App\Models\Role;

beforeEach(function () {
    // Crear rol si no existe
    Role::firstOrCreate(['name' => 'Usuario']);
    
    // Crear usuario de prueba
    $this->user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

test('profile page is displayed', function () {
    $response = $this->actingAs($this->user)
        ->get('/perfil'); // Ruta actual de tu perfil
    
    $response->assertStatus(200)
        ->assertSee('Mi Perfil')
        ->assertSee($this->user->name)
        ->assertSee($this->user->email)
        ->assertSee('Información Personal');
});

test('profile information can be updated', function () {
    $response = $this->actingAs($this->user)
        ->put('/perfil', [ // Ruta actual para actualizar perfil
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            '_method' => 'PUT', // Para simular el método PUT en formularios HTML
            '_token' => csrf_token(),
        ]);

    $response->assertRedirect('/perfil'); // Ajusta según tu ruta de redirección
    
    $this->user->refresh();
    $this->assertEquals('Updated Name', $this->user->name);
    $this->assertEquals('updated@example.com', $this->user->email);
});

test('email verification status is unchanged when email is not changed', function () {
    $this->user->email_verified_at = now();
    $this->user->save();

    $response = $this->actingAs($this->user)
        ->put('/perfil', [
            'name' => 'Same Email User',
            'email' => $this->user->email, // Mismo email
            '_method' => 'PUT',
            '_token' => csrf_token(),
        ]);

    $response->assertRedirect('/perfil');
    $this->assertNotNull($this->user->fresh()->email_verified_at);
});

test('password can be updated', function () {
    $response = $this->actingAs($this->user)
        ->put('/perfil/password', [ // Ruta para actualizar contraseña
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            '_method' => 'PUT',
            '_token' => csrf_token(),
        ]);

    $response->assertRedirect('/perfil');
    $this->assertTrue(Hash::check('new-password', $this->user->fresh()->password));
});

test('correct password is required to update password', function () {
    $response = $this->actingAs($this->user)
        ->put('/perfil/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            '_method' => 'PUT',
            '_token' => csrf_token(),
        ]);

    $response->assertSessionHasErrors('current_password');
});