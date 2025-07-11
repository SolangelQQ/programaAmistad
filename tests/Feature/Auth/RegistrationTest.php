<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        // Crear rol para asignarlo al registrar
        $role = Role::factory()->create([
            'name' => 'Voluntario'
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $role->id, // Usa role_id en lugar de role, según el formulario
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Verifica si hay errores de validación
        if ($response->exception) {
            $this->fail('Registration failed: ' . $response->exception->getMessage());
        }


        // Verifica que el usuario existe en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        // Obtiene el usuario recién creado
        $user = User::where('email', 'test@example.com')->first();
        
        // Verifica que el usuario fue creado
        $this->assertNotNull($user);
        
        // Verifica que el usuario está autenticado
        $this->assertAuthenticatedAs($user);
    }
}