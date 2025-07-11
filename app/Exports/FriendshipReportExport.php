<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FriendshipsReportExport implements WithMultipleSheets
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
        $sheets[] = new FriendshipsStatsSheet($this->data);
        
        // Hoja de amistades
        if (isset($this->data['friendships']) && count($this->data['friendships']) > 0) {
            $sheets[] = new FriendshipsListSheet($this->data['friendships']);
        }
        
        // Hoja de seguimientos
        if (isset($this->data['followups']) && count($this->data['followups']) > 0) {
            $sheets[] = new FollowUpsSheet($this->data['followups']);
        }

        return $sheets;
    }
}

class FriendshipsStatsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
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
        return 'Estadísticas Amistades';
    }
}

class FriendshipsListSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $friendships;

    public function __construct($friendships)
    {
        $this->friendships = $friendships;
    }

    public function collection()
    {
        return collect($this->friendships)->map(function($friendship) {
            return [
                'buddy' => $friendship['buddy_name'] ?? 'N/A',
                'peer_buddy' => $friendship['peer_buddy_name'] ?? 'N/A',
                'estado' => $friendship['status'] ?? 'N/A',
                'fecha_inicio' => $friendship['start_date'] ?? 'N/A',
                'fecha_fin' => $friendship['end_date'] ?? 'N/A',
                'duracion_dias' => $friendship['duration_days'] ?? 0,
                'lider_buddy' => $friendship['buddy_leader'] ?? 'N/A',
                'lider_peer' => $friendship['peer_buddy_leader'] ?? 'N/A',
                'notas' => $friendship['notes'] ?? 'Sin notas'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Buddy',
            'Peer Buddy', 
            'Estado',
            'Fecha Inicio',
            'Fecha Fin',
            'Duración (días)',
            'Líder Buddy',
            'Líder Peer Buddy',
            'Notas'
        ];
    }

    public function title(): string
    {
        return 'Lista de Amistades';
    }
}