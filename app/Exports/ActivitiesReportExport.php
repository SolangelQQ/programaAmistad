<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivitiesReportExport implements WithMultipleSheets
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
        $sheets[] = new ActivitiesStatsSheet($this->data);
        
        // Hoja de actividades
        if (isset($this->data['activities']) && count($this->data['activities']) > 0) {
            $sheets[] = new ActivitiesListSheet($this->data['activities']);
        }

        return $sheets;
    }
}

class ActivitiesStatsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
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
        return 'Estadísticas Actividades';
    }
}

class ActivitiesListSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection()
    {
        return collect($this->activities)->map(function($activity) {
            return [
                'nombre' => $activity['name'] ?? 'N/A',
                'descripcion' => $activity['description'] ?? 'N/A',
                'fecha' => $activity['date'] ?? 'N/A',
                'ubicacion' => $activity['location'] ?? 'N/A',
                'participantes' => $activity['participants_count'] ?? 0,
                'organizador' => $activity['organizer'] ?? 'N/A',
                'estado' => $activity['status'] ?? 'N/A',
                'tipo' => $activity['type'] ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Descripción',
            'Fecha',
            'Ubicación',
            'Participantes',
            'Organizador',
            'Estado',
            'Tipo'
        ];
    }

    public function title(): string
    {
        return 'Lista de Actividades';
    }
}
