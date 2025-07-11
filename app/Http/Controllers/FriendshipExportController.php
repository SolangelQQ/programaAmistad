<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\FriendshipAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FriendshipReportExport;

class FriendshipExportController extends Controller
{
    public function exportReport(Request $request)
    {
        try {
            $format = $request->input('format', 'pdf');
            $friendshipId = $request->input('friendship_id');
            
            // Obtener datos completos del emparejamiento
            $friendship = $this->getFriendshipData($friendshipId);
            
            if (!$friendship) {
                return response()->json(['error' => 'Emparejamiento no encontrado'], 404);
            }

            switch ($format) {
                case 'pdf':
                    return $this->exportToPdf($friendship);
                case 'excel':
                    return $this->exportToExcel($friendship);
                case 'csv':
                    return $this->exportToCsv($friendship);
                default:
                    return response()->json(['error' => 'Formato no soportado'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error en exportación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al exportar el reporte: ' . $e->getMessage()], 500);
        }
    }

    private function getFriendshipData($friendshipId)
    {
        return Friendship::with([
            'buddy.user',
            'peerBuddy.user', 
            'leader.user',
            'coLeader.user',
            'attendances',
            'followUps'
        ])->find($friendshipId);
    }

    private function exportToPdf($friendship)
    {
        $data = $this->prepareReportData($friendship);
        
        $pdf = Pdf::loadView('exports.friendship-report', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Informe_Amistad_' . $friendship->id . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportToExcel($friendship)
    {
        try {
            $filename = 'Informe_Amistad_' . $friendship->id . '_' . date('Y-m-d') . '.xlsx';
            
            return Excel::download(
                new FriendshipReportExport($friendship), 
                $filename,
                \Maatwebsite\Excel\Excel::XLSX
            );
        } catch (\Exception $e) {
            \Log::error('Error específico en Excel: ' . $e->getMessage());
            throw new \Exception('Error en la exportación de Excel: ' . $e->getMessage());
        }
    }

    private function exportToCsv($friendship)
    {
        $filename = 'Informe_Amistad_' . $friendship->id . '_' . date('Y-m-d') . '.csv';
        
        return Excel::download(
            new FriendshipReportExport($friendship), 
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    private function prepareReportData($friendship)
    {
        // Calcular estadísticas de asistencia
        $totalSessions = $friendship->attendances->count();
        $attendedSessions = $friendship->attendances->where('attended', true)->count();
        $attendanceRate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0;

        return [
            'friendship' => $friendship,
            'buddy' => $friendship->buddy,
            'peerBuddy' => $friendship->peerBuddy,
            'leader' => $friendship->leader,
            'coLeader' => $friendship->coLeader,
            'attendances' => $friendship->attendances->sortBy('session_date'),
            'followUps' => $friendship->followUps->sortBy('created_at'),
            'stats' => [
                'total_sessions' => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'attendance_rate' => $attendanceRate,
                'start_date' => $friendship->start_date,
                'status' => $this->getStatusInSpanish($friendship->status),
                'created_at' => $friendship->created_at
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user()->name ?? 'Sistema'
        ];
    }

    private function getStatusInSpanish($status)
    {
        $statusMap = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'completed' => 'Completado',
            'paused' => 'Pausado',
            'cancelled' => 'Cancelado'
        ];

        return $statusMap[$status] ?? $status;
    }
}