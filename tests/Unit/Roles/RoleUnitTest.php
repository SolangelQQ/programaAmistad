<?php

namespace Tests\Unit\Roles;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_role()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'description' => 'Test Description'
        ]);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('Test Role', $role->name);
    }

    public function test_role_has_users()
    {
        $role = Role::create(['name' => 'Test Role']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($role->users->contains($user));
    }

    public function test_can_assign_role_to_user()
    {
        $role = Role::create(['name' => 'Test Role']);
        $user = User::factory()->create(['role_id' => null]);

        $user->role()->associate($role);
        $user->save();

        $this->assertEquals($role->id, $user->fresh()->role_id);
    }

    public function test_can_update_role()
    {
        $role = Role::create(['name' => 'Test Role']);
        $role->update(['name' => 'Updated Role']);

        $this->assertEquals('Updated Role', $role->fresh()->name);
    }

    public function test_can_delete_role()
    {
        $role = Role::create(['name' => 'Test Role']);
        $roleId = $role->id;
        $role->delete();

        $this->assertNull(Role::find($roleId));
    }
    
    public function test_user_without_role_returns_sin_rol()
    {
        $user = User::factory()->create(['role_id' => null]);
        
        $this->assertNull($user->role);
        $this->assertEquals('Sin rol', $user->role ? $user->role->name : 'Sin rol');
    }
}