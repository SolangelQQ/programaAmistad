<?php

namespace Tests\Feature\Activities;

use Tests\TestCase;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActivityApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;

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
    public function it_can_get_calendar_activities()
    {
        $this->actingAs($this->user);

        // Crear actividades para el mes actual
        $currentDate = now();
        Activity::create([
            'title' => 'Actividad Recreativa',
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central',
          
           
            'description' => 'Una actividad divertida'
        ]);

        Activity::create([
            'title' => 'Taller Educativo',
            'date' => $currentDate->addDay()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Biblioteca',
           
           
            'description' => 'Taller de lectura'
        ]);

        $response = $this->getJson('/api/activities/calendar?month=' . $currentDate->month . '&year=' . $currentDate->year);

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
        
        // Verificar que las actividades estén agrupadas por día
        $day1 = $currentDate->day;
        $day2 = $currentDate->addDay()->day;
        
        $this->assertArrayHasKey($day1, $data);
       
        
        // Verificar estructura de datos
      
        $this->assertArrayHasKey('types', $data[$day1]);
    }

    /** @test */
    public function it_can_filter_calendar_activities_by_type()
    {
        $this->actingAs($this->user);

        $currentDate = now();
        
        // Crear actividades de diferentes tipos
        Activity::create([
            'title' => 'Actividad Recreativa',
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central'
        ]);

        Activity::create([
            'title' => 'Taller Educativo',
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => '14:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Biblioteca'
        ]);

        // Filtrar solo actividades recreativas
        $response = $this->getJson('/api/activities/calendar?month=' . $currentDate->month . '&year=' . $currentDate->year . '&type=recreational');

        $response->assertStatus(200);
        
        $data = $response->json();
        $day = $currentDate->day;
        
        $this->assertArrayHasKey($day, $data);
        
        $this->assertContains('recreational', $data[$day]['types']);
        $this->assertNotContains('educational', $data[$day]['types']);
    }

    /** @test */
    public function it_can_filter_calendar_activities_by_status()
    {
        $this->actingAs($this->user);

        $currentDate = now();
        
        Activity::create([
            'title' => 'Actividad Programada',
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central'
        ]);

        Activity::create([
            'title' => 'Actividad Cancelada',
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Parque Central'
        ]);

        // Filtrar solo actividades programadas
        $response = $this->getJson('/api/activities/calendar?month=' . $currentDate->month . '&year=' . $currentDate->year . '&status=scheduled');

        $response->assertStatus(200);
        
        $data = $response->json();
        $day = $currentDate->day;
        
        $this->assertArrayHasKey($day, $data);
       
    }

    /** @test */
    public function it_can_get_upcoming_activities()
    {
        $this->actingAs($this->user);

        // Actividad futura
        Activity::create([
            'title' => 'Actividad Futura',
            'date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central'
        ]);

        // Actividad pasada
        Activity::create([
            'title' => 'Actividad Pasada',
            'date' => now()->subDays(3)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Parque Central'
        ]);

        $response = $this->getJson('/api/activities/upcoming');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Actividad Futura', $data[0]['title']);
    }

    /** @test */
    public function it_includes_cancelled_activities_when_requested()
    {
        $this->actingAs($this->user);

        Activity::create([
            'title' => 'Actividad Programada',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Parque Central'
        ]);

        Activity::create([
            'title' => 'Actividad Cancelada',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Parque Central'
        ]);

        // Sin incluir canceladas
        $response = $this->getJson('/api/activities/upcoming');
        $response->assertStatus(200);
  

        // Incluyendo canceladas
        $response = $this->getJson('/api/activities/upcoming?include_cancelled=true');
        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    /** @test */
    public function it_can_create_activity()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Nueva Actividad',
            'date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Nuevo Lugar',
            'max_participants' => 25,
            'description' => 'Descripción de la nueva actividad'
        ];

        $response = $this->postJson('/activities', $activityData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Actividad creada exitosamente'
        ]);

        $this->assertDatabaseHas('activities', [
            'title' => 'Nueva Actividad',
            'location' => 'Nuevo Lugar',
            'type' => 'recreational'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_activity()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/activities', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'date', 'start_time', 'type', 'location']);
    }

    /** @test */
    public function it_validates_activity_type()
    {
        $this->actingAs($this->user);

        $activityData = [
            'title' => 'Actividad Inválida',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00',
            'type' => 'invalid_type',
            'location' => 'Lugar'
        ];

        $response = $this->postJson('/activities', $activityData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type']);
    }


    /** @test */
    public function it_can_get_single_activity()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad Test',
            'date' => now()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Lugar Test',
          
            'description' => 'Descripción test'
        ]);

        $response = $this->getJson("/activities/{$activity->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $activity->id,
            'title' => 'Actividad Test',
            'location' => 'Lugar Test',
            'type' => 'recreational',
            'status' => 'scheduled'
        ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_activity()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/activities/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_activity()
    {
        $this->actingAs($this->user);

        $activity = Activity::create([
            'title' => 'Actividad a Eliminar',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Lugar'
        ]);

        $response = $this->deleteJson("/activities/{$activity->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Actividad eliminada exitosamente'
        ]);

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        // Test calendar endpoint
        $response = $this->getJson('/api/activities/calendar?month=1&year=2024');
        $response->assertStatus(401);

        // Test by-date endpoint
        $response = $this->getJson('/api/activities/by-date?date=2024-01-01');
        $response->assertStatus(401);

        // Test upcoming endpoint
        $response = $this->getJson('/api/activities/upcoming');
        $response->assertStatus(401);

        // Test create endpoint
        $response = $this->postJson('/activities', []);
        $response->assertStatus(401);

        // Test single activity endpoint
        $response = $this->getJson('/activities/1');
        $response->assertStatus(401);

      

        // Test delete endpoint
        $response = $this->deleteJson('/activities/1');
        $response->assertStatus(401);
    }

   

    /** @test */
    public function it_handles_activities_without_end_time()
    {
        $this->actingAs($this->user);

        Activity::create([
            'title' => 'Actividad Sin Hora Fin',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => null,
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Lugar'
        ]);

        $response = $this->getJson('/api/activities/upcoming');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        
        $activityData = $data[0];
        $this->assertArrayHasKey('formatted_time', $activityData);
        $this->assertNotEmpty($activityData['formatted_time']);
    }

    /** @test */
    public function it_handles_activities_with_max_participants()
    {
        $this->actingAs($this->user);

        Activity::create([
            'title' => 'Actividad con Límite',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Lugar',
         
        ]);

        $response = $this->getJson('/api/activities/upcoming');

        $response->assertStatus(200);
        
        $data = $response->json();
        $activityData = $data[0];
        
        $this->assertEquals(20, $activityData['max_participants']);
       
    }
}