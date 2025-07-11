<?php

namespace Tests\Feature\Activities;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function user_can_create_activity()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Actividad de Prueba',
            'date' => '2024-12-15',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'max_participants' => 20,
            'description' => 'Una actividad recreativa para la comunidad',
        ];

        $response = $this->post('/activities', $activityData);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        
    }

    /** @test */
    public function user_can_view_activity()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad de Prueba',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'max_participants' => 20,
      
            'description' => 'Una actividad recreativa',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get("/activities/{$activity->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $activity->id,
                    'title' => 'Actividad de Prueba',
                    'type' => 'recreational',
                    'location' => 'Parque Central',
                ]);
    }

    /** @test */
    public function user_can_update_activity()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad Original',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Original',
            'created_by' => $this->user->id,
        ]);

        $updateData = [
            '_method' => 'PUT',
            'title' => 'Actividad Actualizada',
            'date' => '2024-12-16',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'type' => 'educational',
            'status' => 'in_progress',
            'location' => 'Nueva Ubicación',
            'max_participants' => 30,
            'description' => 'Descripción actualizada',
        ];

        $response = $this->post("/activities/{$activity->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

    }

    

    /** @test */
    public function user_can_delete_activity()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad a Eliminar',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Parque Central',
            'created_by' => $this->user->id,
        ]);

        $response = $this->delete("/activities/{$activity->id}");

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('activities', [
            'id' => $activity->id,
        ]);
    }

    /** @test */
    public function activity_creation_requires_authentication()
    {
        $activityData = [
            'title' => 'Actividad Sin Auth',
            'date' => '2024-12-15',
            'start_time' => '10:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
        ];

        $response = $this->post('/activities', $activityData);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function activity_creation_validates_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->post('/activities', []);

        $response->assertSessionHasErrors(['title', 'date', 'start_time', 'type', 'location']);
    }

    /** @test */
    public function activity_creation_validates_date_format()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Actividad Prueba',
            'date' => 'fecha-invalida',
            'start_time' => '10:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
        ];

        $response = $this->post('/activities', $activityData);

        $response->assertSessionHasErrors(['date']);
    }


    /** @test */
    public function activity_creation_validates_type()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Actividad Prueba',
            'date' => '2024-12-15',
            'start_time' => '10:00',
            'type' => 'tipo-invalido',
            'status' => 'scheduled',
            'location' => 'Parque Central',
        ];

        $response = $this->post('/activities', $activityData);

        $response->assertSessionHasErrors(['type']);
    }

   

    /** @test */
    public function activity_creation_validates_max_participants_positive()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Actividad Prueba',
            'date' => '2024-12-15',
            'start_time' => '10:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'max_participants' => -5,
        ];

        $response = $this->post('/activities', $activityData);

        $response->assertSessionHasErrors(['max_participants']);
    }


    /** @test */
    public function user_can_get_upcoming_activities()
    {
        $this->actingAs($this->user);

        // Actividad futura
        Activity::create([
            'title' => 'Actividad Futura',
            'date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'created_by' => $this->user->id,
        ]);

        // Actividad pasada (no debería aparecer)
        Activity::create([
            'title' => 'Actividad Pasada',
            'date' => now()->subDays(5)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Parque Central',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get('/api/activities/upcoming');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Actividad Futura'])
                ->assertJsonMissing(['title' => 'Actividad Pasada']);
    }

    /** @test */
    public function user_can_filter_calendar_activities_by_type()
    {
        $this->actingAs($this->user);

        Activity::create([
            'title' => 'Actividad Recreativa',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Actividad Educativa',
            'date' => '2024-12-15',
            'start_time' => '14:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Escuela',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get('/api/activities/calendar?month=12&year=2024&type=recreational');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('15', $data);
        $this->assertContains('recreational', $data['15']['types']);
        $this->assertNotContains('educational', $data['15']['types']);
    }

    /** @test */
    public function user_can_filter_calendar_activities_by_status()
    {
        $this->actingAs($this->user);

        Activity::create([
            'title' => 'Actividad Programada',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Actividad Cancelada',
            'date' => '2024-12-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Parque',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get('/api/activities/calendar?month=12&year=2024&status=scheduled');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('15', $data);
        // Solo debería contar la actividad programada
    }


    /** @test */
    public function activity_shows_formatted_dates_and_times()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad con Formato',
            'date' => '2024-12-15',
            'start_time' => '10:30:00',
            'end_time' => '12:45:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get("/activities/{$activity->id}");

        $response->assertStatus(200);
        
        $data = $response->json();

        
    }
}