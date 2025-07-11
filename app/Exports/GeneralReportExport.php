<?php

// 1. INSTALAR MAATWEBSITE/EXCEL
// Ejecuta en terminal: composer require maatwebsite/excel

// 2. CREAR CLASES EXPORT PARA CADA REPORTE

// App/Exports/GeneralReportExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralReportExport implements WithMultipleSheets
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
        $sheets[] = new GeneralStatsSheet($this->data);
        
        // Otras hojas según tus datos
        if (isset($this->data['buddies']) && count($this->data['buddies']) > 0) {
            $sheets[] = new BuddiesSheet($this->data['buddies']);
        }
        
        if (isset($this->data['activities']) && count($this->data['activities']) > 0) {
            $sheets[] = new ActivitiesSheet($this->data['activities']);
        }

        return $sheets;
    }
}

class GeneralStatsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
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
        return 'Estadísticas Generales';
    }
}