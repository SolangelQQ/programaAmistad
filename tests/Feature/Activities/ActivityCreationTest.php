<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ActivityCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_requires_authentication_to_create_activity()
    {
        $activityData = [
            'title' => 'Test Activity',
            'date' => '2025-06-20',
            'start_time' => '19:00',
            'location' => 'Test Location',
            'type' => 'social'
        ];

        $response = $this->postJson('/activities', $activityData);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('activities', ['title' => 'Test Activity']);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/activities', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'date',
                'start_time',
                'location',
                'type'
            ]);
    }

    /** @test */
    public function it_validates_activity_type()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/activities', [
                'title' => 'Test Activity',
                'date' => '2025-06-20',
                'start_time' => '19:00',
                'location' => 'Test Location',
                'type' => 'invalid_type'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}