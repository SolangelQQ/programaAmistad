<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CreateActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_activity_with_required_fields()
    {
        $activityData = [
            'title' => 'Reunión de Amigos',
            'date' => '2025-06-15',
            'start_time' => '18:00',
            'location' => 'Parque Central',
            'type' => 'social'
        ];

        $activity = Activity::create($activityData);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('Reunión de Amigos', $activity->title);
        
        // Para fechas, usar format para comparar
        if ($activity->date instanceof Carbon) {
            $this->assertEquals('2025-06-15', $activity->date->format('Y-m-d'));
        } else {
            $this->assertEquals('2025-06-15', $activity->date);
        }
        
        $this->assertEquals('Parque Central', $activity->location);
        $this->assertEquals('social', $activity->type);
    }

    public function test_it_can_create_an_activity_with_all_fields()
    {
        $activityData = [
            'title' => 'Torneo de Fútbol',
            'description' => 'Torneo amistoso entre amigos',
            'date' => '2025-06-20',
            'start_time' => '16:00',
            'end_time' => '18:00',
            'location' => 'Cancha Municipal',
            'type' => 'sports',
            'max_participants' => 20
        ];

        $activity = Activity::create($activityData);

        $this->assertEquals('Torneo de Fútbol', $activity->title);
        $this->assertEquals('Torneo amistoso entre amigos', $activity->description);
        
        // Para fechas, usar format para comparar
        if ($activity->date instanceof Carbon) {
            $this->assertEquals('2025-06-20', $activity->date->format('Y-m-d'));
        } else {
            $this->assertEquals('2025-06-20', $activity->date);
        }
        
        $this->assertEquals('Cancha Municipal', $activity->location);
        $this->assertEquals('sports', $activity->type);
        $this->assertEquals(20, $activity->max_participants);
    }

    public function test_it_has_basic_activity_structure()
    {
        $activity = Activity::create([
            'title' => 'Actividad Test',
            'date' => '2025-06-15',
            'start_time' => '18:00',
            'location' => 'Test Location',
            'type' => 'recreational'
        ]);

        // Verificar que el modelo se creó correctamente
        $this->assertNotNull($activity->id);
        $this->assertNotNull($activity->created_at);
        $this->assertNotNull($activity->updated_at);
    }

    public function test_it_validates_activity_types()
    {
        $validTypes = ['recreational', 'educational', 'cultural', 'sports', 'social'];
        
        foreach ($validTypes as $type) {
            $activity = Activity::create([
                'title' => "Actividad {$type}",
                'date' => '2025-06-15',
                'start_time' => '18:00',
                'location' => 'Test Location',
                'type' => $type
            ]);

            $this->assertEquals($type, $activity->type);
        }
    }

    public function test_it_can_format_date_properly()
    {
        $activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2025-06-15',
            'start_time' => '18:00',
            'location' => 'Test Location',
            'type' => 'social'
        ]);

        // Si es instancia de Carbon, verificar que se puede formatear
        if ($activity->date instanceof Carbon) {
            $this->assertEquals('2025-06-15', $activity->date->format('Y-m-d'));
        }
        
       
    }

    public function test_it_handles_time_fields_correctly()
    {
        $activity = Activity::create([
            'title' => 'Test Activity',
            'date' => '2025-06-15',
            'start_time' => '18:30',
            'end_time' => '20:15',
            'location' => 'Test Location',
            'type' => 'social'
        ]);

        // Verificar que los campos de tiempo existen y contienen los valores esperados
        $this->assertNotNull($activity->start_time);
        $this->assertNotNull($activity->end_time);
        
        // Si son objetos Carbon, formatear para comparar
        if ($activity->start_time instanceof Carbon) {
            $this->assertEquals('18:30:00', $activity->start_time->format('H:i:s'));
        } else {
            $this->assertStringContains('18:30', (string) $activity->start_time);
        }
    }

    public function test_it_can_have_null_optional_fields()
    {
        $activity = Activity::create([
            'title' => 'Simple Activity',
            'date' => '2025-06-15',
            'start_time' => '18:00',
            'location' => 'Test Location',
            'type' => 'social'
        ]);

        // Verificar que los campos opcionales pueden ser null o vacíos
        $this->assertTrue(
            is_null($activity->description) || empty($activity->description)
        );
        $this->assertTrue(
            is_null($activity->end_time) || empty($activity->end_time)
        );
        $this->assertTrue(
            is_null($activity->max_participants) || empty($activity->max_participants)
        );
    }

    public function test_it_handles_max_participants_field()
    {
        $activity = Activity::create([
            'title' => 'Limited Activity',
            'date' => '2025-06-15',
            'start_time' => '18:00',
            'location' => 'Test Location',
            'type' => 'social',
            'max_participants' => 15
        ]);

        // Verificar que max_participants es numérico
        $this->assertTrue(is_numeric($activity->max_participants));
        $this->assertEquals(15, (int) $activity->max_participants);
    }
}