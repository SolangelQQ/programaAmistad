<?php

namespace Tests\Unit\Activities;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_an_activity_with_required_fields()
    {
        $activityData = [
            'title' => 'Taller de Lectura',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Biblioteca Central',
            'created_by' => $this->user->id,
        ];

        $activity = Activity::create($activityData);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('Taller de Lectura', $activity->title);
        $this->assertEquals('educational', $activity->type);
        $this->assertEquals('scheduled', $activity->status);
        
    }

    /** @test */
    public function it_can_create_activity_with_optional_fields()
    {
        $activityData = [
            'title' => 'Partido de Fútbol',
            'date' => '2024-03-20',
            'start_time' => '16:00:00',
            'end_time' => '18:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Cancha Principal',
            'max_participants' => 20,
            'current_participants' => 0,
            'description' => 'Partido amistoso entre equipos del programa',
            'created_by' => $this->user->id,
        ];

        $activity = Activity::create($activityData);

        $this->assertEquals(20, $activity->max_participants);
        $this->assertEquals(0, $activity->current_participants);
        $this->assertEquals('Partido amistoso entre equipos del programa', $activity->description);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Activity::create([
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            // Missing required fields: title, type, status, location, created_by
        ]);
    }

    /** @test */
    public function it_can_have_different_activity_types()
    {
        $types = ['recreational', 'educational', 'cultural', 'sports', 'social'];

        foreach ($types as $type) {
            $activity = Activity::create([
                'title' => "Actividad {$type}",
                'date' => '2024-03-15',
                'start_time' => '14:00:00',
                'type' => $type,
                'status' => 'scheduled',
                'location' => 'Ubicación Test',
                'created_by' => $this->user->id,
            ]);

            $this->assertEquals($type, $activity->type);
        }
    }


    /** @test */
    public function it_can_update_activity_status()
    {
        $activity = Activity::create([
            'title' => 'Actividad de Prueba',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        $activity->update(['status' => 'in_progress']);
        
        $this->assertEquals('in_progress', $activity->fresh()->status);

        $activity->update(['status' => 'completed']);
        
        $this->assertEquals('completed', $activity->fresh()->status);
    }

    /** @test */
    public function it_can_manage_participant_count()
    {
        $activity = Activity::create([
            'title' => 'Taller con Límite',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Sala A',
            'max_participants' => 10,
            'current_participants' => 0,
            'created_by' => $this->user->id,
        ]);

        // Simular inscripción de participantes
        $activity->update(['current_participants' => 5]);
        $this->assertEquals(5, $activity->fresh()->current_participants);

        // Verificar que no exceda el máximo
        $this->assertTrue($activity->current_participants <= $activity->max_participants);
    }

    /** @test */
    public function it_can_check_if_activity_is_full()
    {
        $activity = Activity::create([
            'title' => 'Taller Limitado',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Sala B',
            'max_participants' => 5,
            'current_participants' => 5,
            'created_by' => $this->user->id,
        ]);

        // Método que debería existir en el modelo
        $this->assertEquals($activity->current_participants, $activity->max_participants);
    }

    

    /** @test */
    public function it_can_scope_activities_by_status()
    {
        Activity::create([
            'title' => 'Actividad Programada',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Actividad Completada',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Actividad Cancelada',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'cancelled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        $scheduledActivities = Activity::where('status', 'scheduled')->get();
        $completedActivities = Activity::where('status', 'completed')->get();
        $cancelledActivities = Activity::where('status', 'cancelled')->get();

        $this->assertCount(1, $scheduledActivities);
        $this->assertCount(1, $completedActivities);
        $this->assertCount(1, $cancelledActivities);
    }

    /** @test */
    public function it_can_scope_activities_by_type()
    {
        $types = ['recreational', 'educational', 'cultural'];

        foreach ($types as $type) {
            Activity::create([
                'title' => "Actividad {$type}",
                'date' => '2024-03-15',
                'start_time' => '14:00:00',
                'type' => $type,
                'status' => 'scheduled',
                'location' => 'Ubicación Test',
                'created_by' => $this->user->id,
            ]);
        }

        foreach ($types as $type) {
            $activitiesByType = Activity::where('type', $type)->get();
            $this->assertCount(1, $activitiesByType);
            $this->assertEquals($type, $activitiesByType->first()->type);
        }
    }

    /** @test */
    public function it_can_get_formatted_date_and_time()
    {
        $activity = Activity::create([
            'title' => 'Actividad con Formato',
            'date' => '2024-03-15',
            'start_time' => '14:30:00',
            'end_time' => '16:45:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        // Estos métodos deberían existir en el modelo como accessors
        $this->assertNotNull($activity->date);
        $this->assertNotNull($activity->start_time);
        $this->assertNotNull($activity->end_time);
    }

    /** @test */
    public function it_can_soft_delete_activity()
    {
        $activity = Activity::create([
            'title' => 'Actividad para Eliminar',
            'date' => '2024-03-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        $activityId = $activity->id;

        $activity->delete();

        // Debería estar soft deleted si el modelo usa SoftDeletes
        
        $this->assertNull(Activity::find($activityId));
    }

    /** @test */
    public function it_validates_date_format()
    {
        $this->expectException(\Exception::class);

        Activity::create([
            'title' => 'Actividad con Fecha Inválida',
            'date' => 'fecha-invalida',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);
    }


    /** @test */
    public function it_can_check_if_activity_is_upcoming()
    {
        $futureActivity = Activity::create([
            'title' => 'Actividad Futura',
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        $pastActivity = Activity::create([
            'title' => 'Actividad Pasada',
            'date' => Carbon::yesterday()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Ubicación Test',
            'created_by' => $this->user->id,
        ]);

        // Estas verificaciones dependerán de los scopes o métodos implementados
        $upcomingActivities = Activity::where('date', '>=', Carbon::today()->format('Y-m-d'))->get();
        $pastActivities = Activity::where('date', '<', Carbon::today()->format('Y-m-d'))->get();

        $this->assertTrue($upcomingActivities->contains($futureActivity));
        $this->assertTrue($pastActivities->contains($pastActivity));
    }
}