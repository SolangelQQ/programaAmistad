<?php

namespace Tests\Unit\Activities;

use Tests\TestCase;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }



    /** @test */
    public function it_has_correct_table_name()
    {
        $activity = new Activity();
        $this->assertEquals('activities', $activity->getTable());
    }

    /** @test */
    public function it_has_timestamps()
    {
        $activity = new Activity();
        $this->assertTrue($activity->usesTimestamps());
    }

    /** @test */
    public function it_can_scope_upcoming_activities()
    {
        // Crear actividad pasada
        Activity::create([
            'title' => 'Actividad Pasada',
            'date' => '2024-01-01',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Test Location',
            'created_by' => $this->user->id
        ]);

        // Crear actividad futura
        Activity::create([
            'title' => 'Actividad Futura',
            'date' => '2025-12-31',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'created_by' => $this->user->id
        ]);

        $upcomingCount = Activity::where('date', '>=', now()->toDateString())->count();
        $this->assertGreaterThanOrEqual(1, $upcomingCount);
    }

    /** @test */
    public function it_can_get_activities_by_month_and_year()
    {
        Activity::create([
            'title' => 'Actividad Diciembre',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'created_by' => $this->user->id
        ]);

        Activity::create([
            'title' => 'Actividad Enero',
            'date' => '2024-01-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'created_by' => $this->user->id
        ]);

        $decemberActivities = Activity::whereMonth('date', 12)
                                    ->whereYear('date', 2024)
                                    ->count();
        
        $januaryActivities = Activity::whereMonth('date', 1)
                                   ->whereYear('date', 2024)
                                   ->count();

        $this->assertEquals(1, $decemberActivities);
        $this->assertEquals(1, $januaryActivities);
    }

    /** @test */
    public function it_can_filter_by_multiple_criteria()
    {
        Activity::create([
            'title' => 'Actividad Recreativa Programada',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Location 1',
            'created_by' => $this->user->id
        ]);

        Activity::create([
            'title' => 'Actividad Recreativa Completada',
            'date' => '2024-12-15',
            'start_time' => '14:00:00',
            'type' => 'recreational',
            'status' => 'completed',
            'location' => 'Location 2',
            'created_by' => $this->user->id
        ]);

        Activity::create([
            'title' => 'Actividad Educativa Programada',
            'date' => '2024-12-15',
            'start_time' => '16:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Location 3',
            'created_by' => $this->user->id
        ]);

        $recreationalScheduled = Activity::where('type', 'recreational')
                                        ->where('status', 'scheduled')
                                        ->count();

        $this->assertEquals(1, $recreationalScheduled);
    }

    /** @test */
    public function it_can_order_activities_by_date_and_time()
    {
        Activity::create([
            'title' => 'Actividad Tarde',
            'date' => '2024-12-15',
            'start_time' => '16:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Location 1',
            'created_by' => $this->user->id
        ]);

        Activity::create([
            'title' => 'Actividad Mañana',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Location 2',
            'created_by' => $this->user->id
        ]);

        Activity::create([
            'title' => 'Actividad Día Siguiente',
            'date' => '2024-12-16',
            'start_time' => '08:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Location 3',
            'created_by' => $this->user->id
        ]);

        $orderedActivities = Activity::orderBy('date', 'asc')
                                   ->orderBy('start_time', 'asc')
                                   ->get();

        $this->assertEquals('Actividad Mañana', $orderedActivities[0]->title);
        $this->assertEquals('Actividad Tarde', $orderedActivities[1]->title);
        $this->assertEquals('Actividad Día Siguiente', $orderedActivities[2]->title);
    }

    /** @test */
    public function it_handles_null_values_properly()
    {
        $activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'end_time' => null,
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'max_participants' => null,
            'current_participants' => 0,
            'description' => null,
            'created_by' => $this->user->id
        ]);

        $this->assertNull($activity->end_time);
        $this->assertNull($activity->max_participants);
        $this->assertNull($activity->description);
        $this->assertEquals(0, $activity->current_participants);
    }

    /** @test */
    public function it_can_calculate_available_spots()
    {
        $activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'max_participants' => 20,
            'current_participants' => 12,
            'created_by' => $this->user->id
        ]);

        $availableSpots = $activity->max_participants - $activity->current_participants;
        $this->assertEquals(8, $availableSpots);
    }

    /** @test */
    public function it_handles_unlimited_participants_scenario()
    {
        $activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'max_participants' => null,
            'current_participants' => 50,
            'created_by' => $this->user->id
        ]);

        $this->assertNull($activity->max_participants);
        $this->assertEquals(50, $activity->current_participants);
        
        // En caso de participantes ilimitados, siempre hay espacio
        $hasSpace = is_null($activity->max_participants) || 
                   $activity->current_participants < $activity->max_participants;
        
        $this->assertTrue($hasSpace);
    }
}