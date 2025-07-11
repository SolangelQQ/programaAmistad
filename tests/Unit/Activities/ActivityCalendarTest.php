<?php

namespace Tests\Unit\Activities;

use Tests\TestCase; 
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $activity;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Crear actividad de prueba
        $this->activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2024-12-15',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Test Location',
            'max_participants' => 20,
            'current_participants' => 0,
            'description' => 'Test description',
            'created_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_get_activities_by_month_and_year()
    {
        // Crear actividades en diciembre 2024
        Activity::create([
            'title' => 'December Activity 1',
            'date' => '2024-12-01',
            'start_time' => '09:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Location 1',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'December Activity 2',
            'date' => '2024-12-15',
            'start_time' => '14:00:00',
            'type' => 'cultural',
            'status' => 'scheduled',
            'location' => 'Location 2',
            'created_by' => $this->user->id,
        ]);

        // Actividad en enero (no debe aparecer)
        Activity::create([
            'title' => 'January Activity',
            'date' => '2024-01-15',
            'start_time' => '10:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Location 3',
            'created_by' => $this->user->id,
        ]);

        $activities = Activity::whereMonth('date', 12)
                             ->whereYear('date', 2024)
                             ->get();

        $this->assertCount(3, $activities); // 2 nuevas + 1 del setUp
        $this->assertTrue($activities->contains('title', 'December Activity 1'));
        $this->assertTrue($activities->contains('title', 'December Activity 2'));
        $this->assertFalse($activities->contains('title', 'January Activity'));
    }

    /** @test */
    public function it_can_get_activities_by_specific_date()
    {
        // Crear múltiples actividades en la misma fecha
        Activity::create([
            'title' => 'Morning Activity',
            'date' => '2024-12-15',
            'start_time' => '09:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'Morning Location',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Afternoon Activity',
            'date' => '2024-12-15',
            'start_time' => '15:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Afternoon Location',
            'created_by' => $this->user->id,
        ]);

        // Actividad en fecha diferente
        Activity::create([
            'title' => 'Different Date Activity',
            'date' => '2024-12-16',
            'start_time' => '10:00:00',
            'type' => 'cultural',
            'status' => 'scheduled',
            'location' => 'Different Location',
            'created_by' => $this->user->id,
        ]);

        $activities = Activity::whereDate('date', '2024-12-15')->get();

        $this->assertCount(3, $activities); // 2 nuevas + 1 del setUp
        $this->assertTrue($activities->contains('title', 'Morning Activity'));
        $this->assertTrue($activities->contains('title', 'Afternoon Activity'));
        $this->assertFalse($activities->contains('title', 'Different Date Activity'));
    }

    /** @test */
    public function it_can_filter_activities_by_type()
    {
        // Crear actividades de diferentes tipos
        Activity::create([
            'title' => 'Educational Activity',
            'date' => '2024-12-20',
            'start_time' => '10:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'School',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Sports Activity',
            'date' => '2024-12-21',
            'start_time' => '16:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Stadium',
            'created_by' => $this->user->id,
        ]);

        $recreationalActivities = Activity::where('type', 'recreational')->get();
        $educationalActivities = Activity::where('type', 'educational')->get();
        $sportsActivities = Activity::where('type', 'sports')->get();

        $this->assertCount(1, $recreationalActivities);
        $this->assertCount(1, $educationalActivities);
        $this->assertCount(1, $sportsActivities);
        
        $this->assertEquals('Test Activity', $recreationalActivities->first()->title);
        $this->assertEquals('Educational Activity', $educationalActivities->first()->title);
        $this->assertEquals('Sports Activity', $sportsActivities->first()->title);
    }

    /** @test */
    public function it_can_filter_activities_by_status()
    {
        // Crear actividades con diferentes estados
        Activity::create([
            'title' => 'Completed Activity',
            'date' => '2024-12-10',
            'start_time' => '10:00:00',
            'type' => 'cultural',
            'status' => 'completed',
            'location' => 'Museum',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Cancelled Activity',
            'date' => '2024-12-25',
            'start_time' => '14:00:00',
            'type' => 'social',
            'status' => 'cancelled',
            'location' => 'Community Center',
            'created_by' => $this->user->id,
        ]);

        $scheduledActivities = Activity::where('status', 'scheduled')->get();
        $completedActivities = Activity::where('status', 'completed')->get();
        $cancelledActivities = Activity::where('status', 'cancelled')->get();

        $this->assertCount(1, $scheduledActivities);
        $this->assertCount(1, $completedActivities);
        $this->assertCount(1, $cancelledActivities);
        
        $this->assertEquals('Test Activity', $scheduledActivities->first()->title);
        $this->assertEquals('Completed Activity', $completedActivities->first()->title);
        $this->assertEquals('Cancelled Activity', $cancelledActivities->first()->title);
    }

    /** @test */
    public function it_can_get_upcoming_activities()
    {
        $today = Carbon::now();
        $tomorrow = $today->copy()->addDay();
        $yesterday = $today->copy()->subDay();

        // Actividad futura
        Activity::create([
            'title' => 'Future Activity',
            'date' => $tomorrow->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'recreational',
            'status' => 'scheduled',
            'location' => 'Future Location',
            'created_by' => $this->user->id,
        ]);

        // Actividad pasada
        Activity::create([
            'title' => 'Past Activity',
            'date' => $yesterday->format('Y-m-d'),
            'start_time' => '10:00:00',
            'type' => 'educational',
            'status' => 'completed',
            'location' => 'Past Location',
            'created_by' => $this->user->id,
        ]);

        $upcomingActivities = Activity::where('date', '>=', $today->format('Y-m-d'))
                                    ->where('status', '!=', 'cancelled')
                                    ->orderBy('date', 'asc')
                                    ->orderBy('start_time', 'asc')
                                    ->get();

        $this->assertGreaterThanOrEqual(1, $upcomingActivities->count());
        $this->assertTrue($upcomingActivities->contains('title', 'Future Activity'));
        $this->assertFalse($upcomingActivities->contains('title', 'Past Activity'));
    }

    /** @test */
    public function it_can_get_activity_types_for_calendar_display()
    {
        // Crear actividades con diferentes tipos en la misma fecha
        Activity::create([
            'title' => 'Educational Activity',
            'date' => '2024-12-15',
            'start_time' => '09:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'School',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Sports Activity',
            'date' => '2024-12-15',
            'start_time' => '16:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Stadium',
            'created_by' => $this->user->id,
        ]);

        $activitiesOnDate = Activity::whereDate('date', '2024-12-15')->get();
        $types = $activitiesOnDate->pluck('type')->unique()->values();

        $this->assertCount(3, $types); // recreational (setUp), educational, sports
        $this->assertContains('recreational', $types);
        $this->assertContains('educational', $types);
        $this->assertContains('sports', $types);
    }

  

    /** @test */
    public function it_can_count_participants_correctly()
    {
        $activity = Activity::create([
            'title' => 'Participant Test Activity',
            'date' => '2024-12-30',
            'start_time' => '10:00:00',
            'type' => 'social',
            'status' => 'scheduled',
            'location' => 'Social Center',
            'max_participants' => 15,
            'current_participants' => 8,
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals(15, $activity->max_participants);
        $this->assertEquals(8, $activity->current_participants);
        $this->assertEquals(7, $activity->max_participants - $activity->current_participants);
    }

    /** @test */
    public function it_handles_activities_without_end_time()
    {
        $activity = Activity::create([
            'title' => 'No End Time Activity',
            'date' => '2024-12-28',
            'start_time' => '14:00:00',
            'end_time' => null,
            'type' => 'cultural',
            'status' => 'scheduled',
            'location' => 'Cultural Center',
            'created_by' => $this->user->id,
        ]);

        $this->assertNull($activity->end_time);
        
        $this->assertEquals('No End Time Activity', $activity->title);
    }

    /** @test */
    public function it_can_filter_by_multiple_criteria()
    {
        // Crear actividades con diferentes combinaciones
        Activity::create([
            'title' => 'Educational Scheduled',
            'date' => '2024-12-18',
            'start_time' => '10:00:00',
            'type' => 'educational',
            'status' => 'scheduled',
            'location' => 'School',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Educational Completed',
            'date' => '2024-12-19',
            'start_time' => '10:00:00',
            'type' => 'educational',
            'status' => 'completed',
            'location' => 'School',
            'created_by' => $this->user->id,
        ]);

        Activity::create([
            'title' => 'Sports Scheduled',
            'date' => '2024-12-20',
            'start_time' => '16:00:00',
            'type' => 'sports',
            'status' => 'scheduled',
            'location' => 'Stadium',
            'created_by' => $this->user->id,
        ]);

        // Filtrar por tipo educativo Y estado programado
        $filteredActivities = Activity::where('type', 'educational')
                                    ->where('status', 'scheduled')
                                    ->get();

        $this->assertCount(1, $filteredActivities);
        $this->assertEquals('Educational Scheduled', $filteredActivities->first()->title);

        // Filtrar por mes Y tipo
        $monthAndTypeFiltered = Activity::whereMonth('date', 12)
                                      ->where('type', 'sports')
                                      ->get();

        $this->assertCount(1, $monthAndTypeFiltered);
        $this->assertEquals('Sports Scheduled', $monthAndTypeFiltered->first()->title);
    }
}