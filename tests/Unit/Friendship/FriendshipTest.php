<?php

namespace Tests\Unit\Friendship;

use Tests\TestCase;
use App\Models\Friendship;
use App\Models\Buddy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;

    private function createBuddy(array $attributes = []): Buddy
    {
        return Buddy::create(array_merge([
            'ci' => '12345678',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'birth_date' => '1990-01-01',
            'age' => 30, 
            'address' => '123 Main St',
            'emergency_contact' => 'Jane Doe',
            'emergency_phone' => '0987654321',
            'type' => 'buddy',
            'disability' => 'Physical'
        ], $attributes));
    }

    private function createPeerBuddy(array $attributes = []): Buddy
    {
        return Buddy::create(array_merge([
            'ci' => '87654321',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '2345678901',
            'birth_date' => '1992-01-01',
            'age' => 28, // Added required field
            'address' => '456 Oak Ave',
            'emergency_contact' => 'John Smith',
            'emergency_phone' => '1098765432',
            'type' => 'peer_buddy',
            'disability' => null
        ], $attributes));
    }

    private function createFriendship(array $attributes = []): Friendship
    {
        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();

        return Friendship::create(array_merge([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ], $attributes));
    }

    #[Test]
    public function friendship_has_correct_fillable_fields()
    {
        $expected = [
            'buddy_id',
            'peer_buddy_id',
            'status',
            'start_date',
            'end_date',
            'notes'
        ];
        
        $this->assertEquals($expected, (new Friendship())->getFillable());
    }

    #[Test]
    public function friendship_belongs_to_buddy_correctly()
    {
        $friendship = $this->createFriendship();

        // Verificar la relación
        $this->assertInstanceOf(BelongsTo::class, $friendship->buddy());
        $this->assertInstanceOf(Buddy::class, $friendship->buddy);
        $this->assertEquals('buddy', $friendship->buddy->type);
    }

    #[Test]
    public function friendship_belongs_to_peer_buddy_correctly()
    {
        $friendship = $this->createFriendship();

        // Verificar la relación
        $this->assertInstanceOf(BelongsTo::class, $friendship->peerBuddy());
        $this->assertInstanceOf(Buddy::class, $friendship->peerBuddy);
        $this->assertEquals('peer_buddy', $friendship->peerBuddy->type);
    }

    #[Test]
    public function friendship_requires_buddy_and_peer_buddy()
    {
        $buddy = $this->createBuddy();
        $peerBuddy = $this->createPeerBuddy();

        $friendship = Friendship::create([
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado',
            'start_date' => '2024-01-01'
        ]);

        $this->assertDatabaseHas('friendships', [
            'buddy_id' => $buddy->id,
            'peer_buddy_id' => $peerBuddy->id,
            'status' => 'Emparejado'
        ]);
    }

    #[Test]
    public function friendship_can_have_optional_fields()
    {
        $friendship = $this->createFriendship([
            'end_date' => '2024-12-31',
            'notes' => 'Notas de prueba'
        ]);

        $this->assertEquals('2024-12-31', $friendship->end_date);
        $this->assertEquals('Notas de prueba', $friendship->notes);
    }

}