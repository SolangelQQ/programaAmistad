<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadershipReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Hoja de estadísticas
        $sheets[] = new LeadershipStatsSheet($this->data);
        
        // Hoja de líderes
        if (isset($this->data['leaders']) && count($this->data['leaders']) > 0) {
            $sheets[] = new LeadersListSheet($this->data['leaders']);
        }
        
        // Hoja de seguimientos
        if (isset($this->data['followups']) && count($this->data['followups']) > 0) {
            $sheets[] = new FollowUpsSheet($this->data['followups']);
        }
        
        // Hoja de reportes mensuales
        if (isset($this->data['monthly_reports']) && count($this->data['monthly_reports']) > 0) {
            $sheets[] = new MonthlyReportsSheet($this->data['monthly_reports']);
        }
        
        // Hoja de asistencia
        if (isset($this->data['attendance']) && count($this->data['attendance']) > 0) {
            $sheets[] = new AttendanceSheet($this->data['attendance']);
        }

        return $sheets;
    }
}

class LeadershipStatsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $stats = $this->data['statistics'] ?? [];
        $collection = collect();
        
        foreach ($stats as $key => $value) {
            $collection->push([
                'concepto' => ucfirst(str_replace('_', ' ', $key)),
                'valor' => $value
            ]);
        }
        
        return $collection;
    }

    public function headings(): array
    {
        return ['Concepto', 'Valor'];
    }

    public function title(): string
    {
        return 'Estadísticas Liderazgo';
    }
}

class LeadersListSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $leaders;

    public function __construct($leaders)
    {
        $this->leaders = $leaders;
    }

    public function collection()
    {
        return collect($this->leaders)->map(function($leader) {
            return [
                'nombre' => $leader['nombre'] ?? 'N/A',
                'email' => $leader['email'] ?? 'N/A',
                'rol' => $leader['rol'] ?? 'N/A',
                'ciudad' => $leader['ciudad'] ?? 'N/A',
                'amistades_activas' => $leader['amistades_activas'] ?? 0,
                'seguimientos_realizados' => $leader['seguimientos_realizados'] ?? 0,
                'reportes_mensuales' => $leader['reportes_mensuales'] ?? 0,
                'estado' => $leader['estado'] ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Email',
            'Rol',
            'Ciudad',
            'Amistades Activas',
            'Seguimientos Realizados',
            'Reportes Mensuales',
            'Estado'
        ];
    }

    public function title(): string
    {
        return 'Lista de Líderes';
    }
}

// Hojas compartidas
class FollowUpsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $followups;

    public function __construct($followups)
    {
        $this->followups = $followups;
    }

    public function collection()
    {
        return collect($this->followups)->map(function($followup) {
            return [
                'amistad' => $followup['friendship_name'] ?? 'N/A',
                'fecha' => $followup['date'] ?? 'N/A',
                'progreso_buddy' => $followup['buddy_progress'] ?? 'N/A',
                'progreso_peer_buddy' => $followup['peer_buddy_progress'] ?? 'N/A',
                'calidad_relacion' => $followup['relationship_quality'] ?? 'N/A',
                'promedio' => $followup['average_progress'] ?? 0,
                'logros' => $followup['goals_achieved'] ?? 'Sin especificar',
                'desafios' => $followup['challenges_faced'] ?? 'Sin especificar',
                'notas' => $followup['notes'] ?? 'Sin notas'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Amistad',
            'Fecha',
            'Progreso Buddy',
            'Progreso Peer Buddy',
            'Calidad Relación',
            'Promedio',
            'Logros Alcanzados',
            'Desafíos Enfrentados',
            'Notas'
        ];
    }

    public function title(): string
    {
        return 'Seguimientos';
    }
}

class MonthlyReportsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return collect($this->reports)->map(function($report) {
            return [
                'monitor' => $report['monitor_name'] ?? 'N/A',
                'periodo' => $report['monitoring_period'] ?? 'N/A',
                'amistad' => $report['friendship_name'] ?? 'N/A',
                'fecha' => $report['date'] ?? 'N/A',
                'evaluacion_general' => $report['general_evaluation'] ?? 'N/A',
                'frecuencia_reuniones' => $report['meeting_frequency'] ?? 'N/A',
                'participacion_tutor' => $report['tutor_participation'] ?? 'N/A',
                'participacion_lider' => $report['leader_participation'] ?? 'N/A',
                'satisfaccion_tutor' => $report['tutor_satisfaction'] ?? 'N/A',
                'satisfaccion_lider' => $report['leader_satisfaction'] ?? 'N/A',
                'requiere_atencion' => $report['requires_attention'] ?? 'N/A',
                'observaciones' => $report['specific_observations'] ?? 'Sin observaciones'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Monitor',
            'Período',
            'Amistad',
            'Fecha',
            'Evaluación General',
            'Frecuencia Reuniones',
            'Participación Tutor',
            'Participación Líder',
            'Satisfacción Tutor',
            'Satisfacción Líder',
            'Requiere Atención',
            'Observaciones Específicas'
        ];
    }

    public function title(): string
    {
        return 'Reportes Mensuales';
    }
}

class AttendanceSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $attendance;

    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    public function collection()
    {
        return collect($this->attendance)->map(function($attend) {
            return [
                'amistad' => $attend['friendship_name'] ?? 'N/A',
                'fecha' => $attend['date'] ?? 'N/A',
                'buddy_asistio' => $attend['buddy_attended'] ? 'Sí' : 'No',
                'peer_buddy_asistio' => $attend['peer_buddy_attended'] ? 'Sí' : 'No',
                'notas' => $attend['notes'] ?? 'Sin notas'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Amistad',
            'Fecha',
            'Buddy Asistió',
            'Peer Buddy Asistió',
            'Notas'
        ];
    }

    public function title(): string
    {
        return 'Asistencia';
    }
}

class BuddiesSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $buddies;

    public function __construct($buddies)
    {
        $this->buddies = $buddies;
    }

    public function collection()
    {
        return collect($this->buddies)->map(function($buddy) {
            return [
                'nombre' => $buddy['name'] ?? 'N/A',
                'email' => $buddy['email'] ?? 'N/A',
                'telefono' => $buddy['phone'] ?? 'N/A',
                'ciudad' => $buddy['city'] ?? 'N/A',
                'estado' => $buddy['status'] ?? 'N/A',
                'fecha_registro' => $buddy['created_at'] ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Email',
            'Teléfono',
            'Ciudad',
            'Estado',
            'Fecha Registro'
        ];
    }

    public function title(): string
    {
        return 'Buddies';
    }
}
