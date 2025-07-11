<?php

namespace Tests\Feature\Activities;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActivityFeatureTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $activity;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba sin factory
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);

        // Crear actividad de prueba
        $this->activity = Activity::create([
            'title' => 'Actividad de Prueba',
            'date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
            'max_participants' => 20,
            'current_participants' => 5,
            'description' => 'Una actividad recreativa para todos',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_view_activities_index()
    {
        $response = $this->actingAs($this->user)
                         ->get('/friendships');

        $response->assertStatus(200);
        $response->assertViewIs('friendships.index');
        $response->assertSee('Calendario de Actividades');
    }

    public function test_user_can_create_new_activity()
    {
        $activityData = [
            'title' => 'Nueva Actividad de Prueba',
            'date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Biblioteca Municipal',
            'max_participants' => 15,
            'description' => 'Actividad educativa sobre lectura'
        ];

        $response = $this->actingAs($this->user)
                         ->post('/activities', $activityData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('activities', [
            'title' => 'Nueva Actividad de Prueba',
            'type' => 'educational',
            'location' => 'Biblioteca Municipal'
        ]);
    }

    public function test_user_can_update_existing_activity()
    {
        $updateData = [
            'title' => 'Actividad Actualizada',
            'date' => $this->activity->date,
            'start_time' => '11:00',
            'end_time' => '13:00',
            'type' => 'cultural',
            'status' => 'scheduled',
            'location' => 'Centro Cultural',
            'max_participants' => 25,
            'description' => 'Actividad cultural actualizada',
            '_method' => 'PUT'
        ];

        $response = $this->actingAs($this->user)
                         ->post("/activities/{$this->activity->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('activities', [
            'id' => $this->activity->id,
            'title' => 'Actividad Actualizada',
            'type' => 'cultural',
            'location' => 'Centro Cultural'
        ]);
    }


    public function test_user_can_delete_activity()
    {
        $response = $this->actingAs($this->user)
                         ->delete("/activities/{$this->activity->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('activities', [
            'id' => $this->activity->id
        ]);
    }

    public function test_user_can_view_specific_activity()
    {
        $response = $this->actingAs($this->user)
                         ->get("/activities/{$this->activity->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->activity->id,
            'title' => $this->activity->title,
            'type' => $this->activity->type
        ]);
    }

    

    public function test_api_returns_activities_by_date()
    {
        $response = $this->actingAs($this->user)
                         ->get("/api/activities/by-date?date={$this->activity->date}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $this->activity->title,
            'location' => $this->activity->location
        ]);
    }

    public function test_api_returns_upcoming_activities()
    {
        $response = $this->actingAs($this->user)
                         ->get('/api/activities/upcoming');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $this->activity->title
        ]);
    }

    public function test_guest_cannot_access_activities()
    {
        $response = $this->get('/friendships');
        
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_create_activity()
    {
        $activityData = [
            'title' => 'Test Activity',
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location'
        ];

        $response = $this->post('/activities', $activityData);
        
        $response->assertRedirect('/login');
    }

    public function test_activity_can_be_filtered_by_type()
    {
        // Crear actividades de diferentes tipos
        Activity::create([
            'title' => 'Actividad Educativa',
            'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Escuela',
            'user_id' => $this->user->id
        ]);

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $response = $this->actingAs($this->user)
                         ->get("/api/activities/calendar?month={$month}&year={$year}&type=educational");

        $response->assertStatus(200);
    }

    public function test_activity_can_be_filtered_by_status()
    {
        // Actualizar actividad a completada
        $this->activity->update(['status' => 'completed']);

        $response = $this->actingAs($this->user)
                         ->get('/api/activities/upcoming?status=completed');

        $response->assertStatus(200);
    }

    public function test_activity_photos_modal_can_be_accessed()
    {
        $response = $this->actingAs($this->user)
                         ->get('/friendships');

        $response->assertStatus(200);
        $response->assertSee('photos-modal');
        $response->assertSee('Gestión de Fotos');
    }

    public function test_activity_can_handle_photo_upload()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test-photo.jpg');

        $response = $this->actingAs($this->user)
                         ->post("/activities/{$this->activity->id}/photos", [
                             'photos' => [$file]
                         ]);

        $response->assertStatus(200);
    }


    public function test_activity_upcoming_excludes_cancelled()
    {
        // Crear actividad cancelada
        Activity::create([
            'title' => 'Actividad Cancelada',
            'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Lugar',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
                         ->get('/api/activities/upcoming');

        $response->assertStatus(200);
        
    }

}