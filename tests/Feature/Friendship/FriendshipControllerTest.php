<?php

namespace Tests\Feature\Friendship;

use Tests\TestCase;
use App\Models\Friendship;
use App\Models\Buddy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class FriendshipControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createBuddy($data = [])
    {
        static $counter = 0;
        $counter++;
        
        $defaults = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'ci' => "1234567{$counter}",
            'email' => "john{$counter}@example.com",
            'phone' => "123456789{$counter}",
            'age' => 30,
            'address' => '123 Main St',
            'type' => 'buddy',
            'disability' => 'Physical'
        ];
        
        return Buddy::create(array_merge($defaults, $data));
    }

    protected function createPeerBuddy($data = [])
    {
        static $counter = 0;
        $counter++;
        
        $defaults = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'ci' => "9876543{$counter}",
            'email' => "jane{$counter}@example.com",
            'phone' => "234567890{$counter}",
            'age' => 25,
            'address' => '456 Oak Ave',
            'type' => 'peer_buddy'
        ];
        
        return Buddy::create(array_merge($defaults, $data));
    }

    protected function createUser()
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_index_displays_friendships()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();
        
        $friendship = Friendship::create([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        $response = $this->get(route('friendships.index'));

        $response->assertOk()
                ->assertViewIs('friendships.index')
                ->assertViewHas('friendships')
                ->assertSee('A'.str_pad($friendship->id, 3, '0', STR_PAD_LEFT))
                ->assertSee($buddy->full_name)
                ->assertSee($peerBuddy->full_name);
    }

    public function test_can_create_friendship()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();

        $response = $this->post(route('friendships.store'), [
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'start_date' => '2024-01-01',
            'status' => 'Emparejado',
            'notes' => 'Test friendship'
        ]);

        $response->assertRedirect(route('friendships.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseCount('friendships', 1);
        $this->assertDatabaseHas('friendships', [
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'notes' => 'Test friendship'
        ]);
    }

    public function test_cannot_create_friendship_with_same_person()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();

        $response = $this->post(route('friendships.store'), [
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $buddy->id,
            'start_date' => '2024-01-01',
            'status' => 'Emparejado'
        ]);

        $response->assertSessionHasErrors(['peer_buddy_id']);
    }

    public function test_cannot_create_friendship_with_wrong_types()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Crear dos buddies (no deberían poder emparejarse)
        $buddy1 = $this->createBuddy();
        $buddy2 = $this->createBuddy(['email' => 'john2@example.com', 'ci' => '12345672']);

        $response = $this->post(route('friendships.store'), [
            'buddy_id' => $buddy1->id,
            'peer_buddy_id' => $buddy2->id,
            'start_date' => '2024-01-01',
            'status' => 'Emparejado'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('friendships', 0);
    }

    public function test_can_show_friendship_details()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();
        
        $friendship = Friendship::create([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        $response = $this->getJson(route('friendships.show', $friendship));

        $response->assertOk()
                ->assertJson([
                    'friendship' => [
                        'id' => $friendship->id,
                        'status' => 'Emparejado'
                    ],
                    'buddy' => [
                        'id' => $buddy->id,
                        'first_name' => $buddy->first_name,
                        'last_name' => $buddy->last_name
                    ],
                    'peerBuddy' => [
                        'id' => $peerBuddy->id,
                        'first_name' => $peerBuddy->first_name,
                        'last_name' => $peerBuddy->last_name
                    ]
                ]);
    }

    public function test_can_update_friendship()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();
        
        $friendship = Friendship::create([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        $newData = [
            'status' => 'Inactivo',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'notes' => 'Updated notes'
        ];

        $response = $this->put(route('friendships.update', $friendship), $newData);

        $response->assertRedirect(route('friendships.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('friendships', array_merge(
            ['id' => $friendship->id],
            $newData
        ));
    }

    public function test_can_delete_friendship()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();
        
        $friendship = Friendship::create([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        $response = $this->delete(route('friendships.destroy', $friendship));

        $response->assertRedirect(route('friendships.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseMissing('friendships', ['id' => $friendship->id]);
        $this->assertDatabaseCount('friendships', 0);
    }

    public function test_requires_authentication_for_sensitive_operations()
    {
        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();

        // Test creating friendship without auth
        $response = $this->post(route('friendships.store'), [
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'start_date' => '2024-01-01',
            'status' => 'Emparejado'
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_validates_required_fields_on_create()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->post(route('friendships.store'), []);

        $response->assertSessionHasErrors([
            'buddy_id', 
            'peer_buddy_id', 
            'start_date', 
            'status'
        ]);
    }


    public function test_can_filter_friendships_by_status()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Crear datos de prueba
        $activeBuddy = $this->createBuddy(['first_name' => 'Active', 'last_name' => 'Buddy']);
        $activePeer = $this->createPeerBuddy(['first_name' => 'Active', 'last_name' => 'Peer']);
        
        $inactiveBuddy = $this->createBuddy(['first_name' => 'Inactive', 'last_name' => 'Buddy']);
        $inactivePeer = $this->createPeerBuddy(['first_name' => 'Inactive', 'last_name' => 'Peer']);

        Friendship::create([
            'buddy_id' => $activeBuddy->id,
            'peer_buddy_id' => $activePeer->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        Friendship::create([
            'buddy_id' => $inactiveBuddy->id,
            'peer_buddy_id' => $inactivePeer->id,
            'status' => 'Inactivo',
            'start_date' => '2024-01-01'
        ]);

        // Filtrar por estado Emparejado
        $response = $this->get(route('friendships.index', ['status' => 'Emparejado']));

        // Verificar que solo se muestren los emparejamientos activos
        $response->assertOk()
                ->assertViewIs('friendships.index')
                ->assertSee('Active Buddy')
                ->assertSee('Active Peer');
                
    }
}