<?php
// programaAmistad/app/Exports/ReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting, WithTitle
{
    protected $data;
    protected $reportType;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($data, $reportType, $dateFrom, $dateTo)
    {
        $this->data = $data;
        $this->reportType = $reportType;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function array(): array
    {
        $exportData = [];
        
        // Información del reporte
        $exportData[] = [$this->data['title']];
        $exportData[] = ['Período: ' . \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y')];
        $exportData[] = ['Generado el: ' . now()->format('d/m/Y H:i:s')];
        $exportData[] = []; // Línea vacía

        // Estadísticas
        if (isset($this->data['statistics'])) {
            $exportData[] = ['ESTADÍSTICAS'];
            foreach ($this->data['statistics'] as $key => $value) {
                $exportData[] = [ucfirst(str_replace('_', ' ', $key)), $value];
            }
            $exportData[] = []; // Línea vacía
        }

        // Datos específicos según el tipo
        switch ($this->reportType) {
            case 'actividades':
                if (isset($this->data['activities']) && count($this->data['activities']) > 0) {
                    $exportData[] = ['ACTIVIDADES'];
                    $exportData[] = []; // Línea vacía para separar
                    
                    foreach ($this->data['activities'] as $activity) {
                        $exportData[] = [
                            $activity['title'] ?? '',
                            $activity['formatted_date'] ?? '',
                            $activity['type_label'] ?? '',
                            $activity['status_label'] ?? '',
                            $activity['location'] ?? '',
                            $activity['description'] ?? ''
                        ];
                    }
                }
                break;
            
            case 'amistades':
                if (isset($this->data['friendships']) && count($this->data['friendships']) > 0) {
                    $exportData[] = ['AMISTADES'];
                    $exportData[] = []; // Línea vacía para separar
                    
                    foreach ($this->data['friendships'] as $friendship) {
                        $exportData[] = [
                            $friendship['buddy_name'] ?? '',
                            $friendship['peer_buddy_name'] ?? '',
                            $friendship['formatted_start_date'] ?? '',
                            $friendship['status'] ?? '',
                            $friendship['duration_days'] ?? '',
                            $friendship['leader1_name'] ?? '',
                            $friendship['leader2_name'] ?? ''
                        ];
                    }
                    
                    // Agregar detalles de evaluaciones y asistencia
                    if (isset($this->data['evaluations']) && count($this->data['evaluations']) > 0) {
                        $exportData[] = [];
                        $exportData[] = ['EVALUACIONES DE AMISTADES'];
                        $exportData[] = [];
                        
                        foreach ($this->data['evaluations'] as $evaluation) {
                            $exportData[] = [
                                'Amistad: ' . ($evaluation['friendship_name'] ?? ''),
                                'Fecha: ' . ($evaluation['date'] ?? ''),
                                'Puntuación: ' . ($evaluation['score'] ?? ''),
                                'Comentarios: ' . ($evaluation['comments'] ?? '')
                            ];
                        }
                    }
                    
                    if (isset($this->data['attendance']) && count($this->data['attendance']) > 0) {
                        $exportData[] = [];
                        $exportData[] = ['ASISTENCIA DE AMISTADES'];
                        $exportData[] = [];
                        
                        foreach ($this->data['attendance'] as $attendance) {
                            $exportData[] = [
                                'Amistad: ' . ($attendance['friendship_name'] ?? ''),
                                'Fecha: ' . ($attendance['date'] ?? ''),
                                'Buddy Asistió: ' . ($attendance['buddy_attended'] ? 'Sí' : 'No'),
                                'Peer Buddy Asistió: ' . ($attendance['peer_buddy_attended'] ? 'Sí' : 'No'),
                                'Observaciones: ' . ($attendance['notes'] ?? '')
                            ];
                        }
                    }
                }
                break;
                
            case 'general':
                // Incluir resumen de todas las áreas
                $this->addGeneralData($exportData);
                break;
                
            case 'liderazgo':
                if (isset($this->data['leaders']) && count($this->data['leaders']) > 0) {
                    $exportData[] = ['LÍDERES'];
                    $exportData[] = [];
                    
                    foreach ($this->data['leaders'] as $leader) {
                        $exportData[] = [
                            $leader['name'] ?? '',
                            $leader['role'] ?? '',
                            $leader['active_friendships'] ?? '',
                            $leader['satisfaction_rating'] ?? ''
                        ];
                    }
                }
                break;
        }

        return $exportData;
    }

    public function headings(): array
    {
        switch ($this->reportType) {
            case 'actividades':
                return ['Título', 'Fecha', 'Tipo', 'Estado', 'Ubicación', 'Descripción'];
            case 'amistades':
                return ['Buddy', 'Peer Buddy', 'Fecha Inicio', 'Estado', 'Duración (días)', 'Líder 1', 'Líder 2'];
            case 'liderazgo':
                return ['Nombre', 'Rol', 'Amistades Activas', 'Calificación'];
            default:
                return [];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['italic' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function title(): string
    {
        $titles = [
            'general' => 'Reporte General',
            'actividades' => 'Reporte Actividades',
            'amistades' => 'Reporte Amistades',
            'liderazgo' => 'Reporte Liderazgo'
        ];
        
        return $titles[$this->reportType] ?? 'Reporte';
    }
    
    private function addGeneralData(&$exportData)
    {
        // Agregar datos generales de todas las secciones
        if (isset($this->data['activities'])) {
            $exportData[] = ['RESUMEN DE ACTIVIDADES'];
            $exportData[] = ['Total de actividades: ' . count($this->data['activities'])];
            $exportData[] = [];
        }
        
        if (isset($this->data['friendships'])) {
            $exportData[] = ['RESUMEN DE AMISTADES'];
            $exportData[] = ['Total de amistades: ' . count($this->data['friendships'])];
            $exportData[] = [];
        }
    }
}