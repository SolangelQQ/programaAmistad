<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Buddy;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener datos dinámicos para los stats
        $stats = $this->getDashboardStats();
        
        // Obtener otras variables necesarias para el dashboard
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $activities = Activity::forMonth($currentYear, $currentMonth)->get();
        $upcomingActivities = Activity::upcoming()->take(5)->get();
        
        return view('dashboard.index', compact('stats', 'activities', 'upcomingActivities', 'currentMonth', 'currentYear'));
    }

    private function getDashboardStats()
    {
        // Fechas correctas (no modificar now())
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Para el mes pasado, usar una nueva instancia de Carbon
        $lastMonth = now()->subMonth();
        $lastMonthMonth = $lastMonth->month;
        $lastMonthYear = $lastMonth->year;

        return [
            'total_activities' => $this->getTotalActivitiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear),
            'total_peer_buddies' => $this->getTotalPeerBuddiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear),
            'active_friendships' => $this->getActiveFriendshipsStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear),
            'activities_this_month' => $this->getMonthlyActivitiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear),
        ];
    }

    private function getTotalActivitiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear)
    {
        // Total acumulado hasta ahora
        $totalActivities = Activity::count();
        
        // Total hasta el mes pasado
        $totalUntilLastMonth = Activity::where(function($query) use ($lastMonthYear, $lastMonthMonth) {
            $query->where('created_at', '<', Carbon::create($lastMonthYear, $lastMonthMonth)->endOfMonth());
        })->count();
        
        // Nuevas actividades este mes
        $newActivitiesThisMonth = $totalActivities - $totalUntilLastMonth;
        
        if ($totalUntilLastMonth > 0) {
            $percentage = round(($newActivitiesThisMonth / $totalUntilLastMonth) * 100, 1);
            $trend = $newActivitiesThisMonth > 0 ? 'up' : 'same';
        } else {
            $percentage = $newActivitiesThisMonth > 0 ? 100 : 0;
            $trend = $newActivitiesThisMonth > 0 ? 'up' : 'same';
        }

        return [
            'value' => $totalActivities,
            'percentage' => abs($percentage),
            'trend' => $trend,
            'type' => 'cumulative'
        ];
    }

    private function getTotalPeerBuddiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear)
    {
        // Total acumulado de PeerBuddies
        $totalPeerBuddies = Buddy::where('type', 'peer_buddy')->count();
        
        // Total hasta el mes pasado
        $totalUntilLastMonth = Buddy::where('type', 'peer_buddy')
            ->where('created_at', '<', Carbon::create($lastMonthYear, $lastMonthMonth)->endOfMonth())
            ->count();
        
        // Nuevos PeerBuddies este mes
        $newPeerBuddiesThisMonth = $totalPeerBuddies - $totalUntilLastMonth;
        
        if ($totalUntilLastMonth > 0) {
            $percentage = round(($newPeerBuddiesThisMonth / $totalUntilLastMonth) * 100, 1);
            $trend = $newPeerBuddiesThisMonth > 0 ? 'up' : 'same';
        } else {
            $percentage = $newPeerBuddiesThisMonth > 0 ? 100 : 0;
            $trend = $newPeerBuddiesThisMonth > 0 ? 'up' : 'same';
        }

        return [
            'value' => $totalPeerBuddies,
            'percentage' => abs($percentage),
            'trend' => $trend,
            'type' => 'cumulative'
        ];
    }

    private function getActiveFriendshipsStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear)
    {
        // Amistades activas actuales
        $activeFriendships = Friendship::where('status', 'active')->count();
        
        // Amistades activas hasta el mes pasado
        $activeFriendshipsUntilLastMonth = Friendship::where('status', 'active')
            ->where('created_at', '<', Carbon::create($lastMonthYear, $lastMonthMonth)->endOfMonth())
            ->count();
        
        // Nuevas amistades activas este mes
        $newActiveFriendshipsThisMonth = $activeFriendships - $activeFriendshipsUntilLastMonth;
        
        if ($activeFriendshipsUntilLastMonth > 0) {
            $percentage = round(($newActiveFriendshipsThisMonth / $activeFriendshipsUntilLastMonth) * 100, 1);
            $trend = $newActiveFriendshipsThisMonth > 0 ? 'up' : 'same';
        } else {
            $percentage = $newActiveFriendshipsThisMonth > 0 ? 100 : 0;
            $trend = $newActiveFriendshipsThisMonth > 0 ? 'up' : 'same';
        }

        return [
            'value' => $activeFriendships,
            'percentage' => abs($percentage),
            'trend' => $trend,
            'type' => 'cumulative'
        ];
    }

    private function getMonthlyActivitiesStats($currentMonth, $currentYear, $lastMonthMonth, $lastMonthYear)
    {
        // Actividades creadas ESTE mes
        $activitiesThisMonth = Activity::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        
        // Actividades creadas el mes PASADO
        $activitiesLastMonth = Activity::whereMonth('created_at', $lastMonthMonth)
            ->whereYear('created_at', $lastMonthYear)
            ->count();
        
        if ($activitiesLastMonth > 0) {
            $percentage = round((($activitiesThisMonth - $activitiesLastMonth) / $activitiesLastMonth) * 100, 1);
            $trend = $activitiesThisMonth > $activitiesLastMonth ? 'up' : 
                    ($activitiesThisMonth < $activitiesLastMonth ? 'down' : 'same');
        } else {
            $percentage = $activitiesThisMonth > 0 ? 100 : 0;
            $trend = $activitiesThisMonth > 0 ? 'up' : 'same';
        }

        return [
            'value' => $activitiesThisMonth,
            'percentage' => abs($percentage),
            'trend' => $trend,
            'type' => 'monthly'
        ];
    }

    // Método adicional para obtener stats más detallados si los necesitas
    public function getDetailedStats()
    {
        $stats = $this->getDashboardStats();
        
        // Agregar stats adicionales
        $additionalStats = [
            'total_buddies' => Buddy::where('type', 'buddy')->count(),
            'completed_activities' => Activity::where('status', 'completed')->count(),
            'pending_activities' => Activity::where('status', 'scheduled')->count(),
            'cancelled_activities' => Activity::where('status', 'cancelled')->count(),
            'friendships_by_status' => [
                'active' => Friendship::where('status', 'active')->count(),
                'inactive' => Friendship::where('status', 'inactive')->count(),
                'pending' => Friendship::where('status', 'pending')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'stats' => array_merge($stats, $additionalStats)
        ]);
    }
}