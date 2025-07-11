<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\RoleController;
use App\Models\FriendshipFollowUp;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\BuddyController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MonthlyMonitoringController;
use App\Http\Controllers\LiderazgoController;
use App\Http\Controllers\ReportsExportController;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/test-email-debug', function() {
    echo "<h2>🔍 Diagnóstico de Email - Best Buddies Bolivia</h2>";
    
    // Verificar configuración
    echo "<h3>1. Configuración actual:</h3>";
    echo "MAIL_MAILER: " . config('mail.default') . "<br>";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "<br>";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "<br>";
    echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "<br>";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "<br><br>";
    
    // Probar envío básico
    echo "<h3>2. Probando envío de email...</h3>";
    try {
        Mail::raw('🧪 Email de prueba desde Best Buddies Bolivia', function($message) {
            $message->to('perlusida@gmail.com')
                   ->subject('Prueba Email - Best Buddies Bolivia');
        });
        echo "✅ Email enviado sin errores<br>";
    } catch(Exception $e) {
        echo "❌ Error al enviar: " . $e->getMessage() . "<br>";
        echo "📋 Detalles: " . $e->getTraceAsString() . "<br>";
    }
    
    // Verificar tabla password_reset_tokens
    echo "<h3>3. Últimos tokens generados:</h3>";
    $tokens = DB::table('password_reset_tokens')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
    
    foreach($tokens as $token) {
        echo "📧 Email: " . $token->email . " | Creado: " . $token->created_at . "<br>";
    }
    
    echo "<br><h3>4. Próximos pasos:</h3>";
    echo "- Si hay errores arriba, revisar configuración SMTP<br>";
    echo "- Si no hay errores, revisar carpeta spam<br>";
    echo "- Verificar logs en storage/logs/laravel.log<br>";
});

Route::get('/check-users', function() {
    return \App\Models\User::all();
});

Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
    ->name('login.google.redirect');
    
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
    ->name('login.google.callback');


    Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
    ->name('login.google.redirect');

Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
    ->name('login.google.callback');

Route::get('/auth/google/select-role', [GoogleLoginController::class, 'showRoleSelection'])
    ->name('google.select-role');

Route::post('/auth/google/complete-registration', [GoogleLoginController::class, 'completeRegistrationWithRole'])
    ->name('google.complete-registration');

Route::get('/auth/google/show-password', [GoogleLoginController::class, 'showGeneratedPassword'])
    ->name('google.show-password');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);


Route::middleware('auth')->group(function () {
    Route::resource('users', UserManagementController::class)->except(['create', 'store']);    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/roles', [App\Http\Controllers\UserManagementController::class, 'index'])->name('roles.index');
    Route::get('/roles/{user}', [App\Http\Controllers\UserManagementController::class, 'show'])->name('roles.show');
    Route::get('/roles/{user}/edit', [App\Http\Controllers\UserManagementController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('roles.destroy');
    

    Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/perfil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    // Rutas de configuración
    Route::get('/configuracion', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/configuracion', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/friendships', [FriendshipController::class, 'index'])->name('friendships.index');
    Route::post('/friendships', [FriendshipController::class, 'store'])->name('friendships.store');
    Route::get('/friendships/filter', [FriendshipController::class, 'filter'])->name('friendships.filter');
    Route::patch('/friendships/{friendship}/status', [FriendshipController::class, 'updateStatus'])->name('friendships.updateStatus');
    Route::delete('/friendships/{friendship}', [FriendshipController::class, 'destroy'])->name('friendships.destroy');
    Route::resource('friendships', FriendshipController::class)->except(['show']);
    Route::post('friendships/filter', [FriendshipController::class, 'filter'])->name('friendships.filter');
    Route::get('/friendships/{friendship}', [FriendshipController::class, 'show'])->name('friendships.show'); //aca


    Route::prefix('buddies')->group(function () {
        Route::get('/', [FriendshipController::class, 'indexBuddy'])->name('buddies.index');
        Route::get('/create', [FriendshipController::class, 'createBuddy'])->name('buddies.create');
        Route::post('/store', [FriendshipController::class, 'storeBuddy'])->name('buddies.store');
    });

    Route::resource('buddies', BuddyController::class);
    Route::get('/buddies', [BuddyController::class, 'index'])->name('buddies.index');
    Route::delete('/buddies/{buddy}', [BuddyController::class, 'destroy'])->name('buddies.destroy');
    Route::get('/buddies/{buddy}/details', [BuddyController::class, 'details'])->name('buddies.details');
    Route::get('/buddies/{buddy}/edit', [BuddyController::class, 'edit'])->name('buddies.edit');
    Route::put('/buddies/{buddy}', [BuddyController::class, 'update'])->name('buddies.update');
    Route::get('/buddies/create', [FriendshipController::class, 'createBuddy'])->name('buddies.create');


    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    // API routes para el calendario
    Route::get('/api/activities/calendar', [ActivityController::class, 'getCalendarData'])->name('api.activities.calendar');
    Route::get('/api/activities/day', [ActivityController::class, 'getDayActivities'])->name('api.activities.day');
    // CRUD de actividades
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    // Subida de fotos
    Route::post('/activities/{activity}/photos', [ActivityController::class, 'uploadPhotos'])->name('activities.photos');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    // Rutas para fotos
    Route::post('/activities/{activity}/photos', [ActivityController::class, 'uploadPhotos'])->name('activities.photos.upload');
    Route::delete('/activities/{activity}/photos', [ActivityController::class, 'deletePhoto'])->name('activities.photos.delete');
    // Rutas API para calendario
    Route::get('/api/activities/calendar', [ActivityController::class, 'getCalendarData'])->name('activities.calendar.data');
    Route::get('/api/activities/by-date', [ActivityController::class, 'getActivitiesByDate'])->name('activities.by.date');
    Route::get('/api/activities/upcoming', [ActivityController::class, 'getUpcomingActivities']);
    Route::prefix('api')->group(function () {
    // Ruta principal para el sidebar
        Route::get('/activities', [ActivityController::class, 'apiIndex']);
        
        // Rutas existentes del calendario
        Route::get('/activities/calendar', [ActivityController::class, 'getCalendarData']);
        Route::get('/activities/by-date', [ActivityController::class, 'getActivitiesByDate']);
        Route::get('/activities/upcoming', [ActivityController::class, 'getUpcomingActivities']);
    });
    Route::resource('activities', ActivityController::class);
    Route::post('/activities/{activity}/photos', [ActivityController::class, 'uploadPhotos']);
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);
    Route::post('/activities/{activity}/photos', [ActivityController::class, 'uploadPhotos'])->name('activities.photos.upload');
    Route::delete('/activities/{activity}/photos', [ActivityController::class, 'deletePhoto'])->name('activities.photos.delete');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Vista principal de notificaciones
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        
        // API para obtener notificaciones (para el dropdown)
        Route::get('/api', [NotificationController::class, 'getNotifications']);
        
        // API para obtener solo el conteo de no leídas
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        
        // Marcar notificación específica como leída
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        
        // Marcar todas las notificaciones como leídas
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        
        // Eliminar notificación específica
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        
        // Formulario para crear notificación personalizada (solo admins)
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        
        // Enviar notificación personalizada
        Route::post('/send', [NotificationController::class, 'store'])->name('store');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::patch('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        
        // API para el componente dropdown
        Route::get('/api', [NotificationController::class, 'apiIndex'])->name('api');
        
        // Invitación de amistad específica
        Route::post('/friendship-invitation', [NotificationController::class, 'sendFriendshipInvitation'])->name('friendship-invitation');
    });


    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');


    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::put('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Rutas específicas para marcar como leídas
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Rutas AJAX para obtener datos
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

    // Agregar esta línea en tus rutas de notificaciones
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');


Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
     ->name('notifications.show');


     // Document Management
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    // Report Generation
    Route::get('/documents/generate/buddies/{year}', [DocumentController::class, 'generateBuddiesReport'])
        ->name('documents.generate.buddies');
    Route::get('/documents/generate/friendships/{year}', [DocumentController::class, 'generateFriendshipsReport'])
        ->name('documents.generate.friendships');
    
    // API Endpoints
    Route::get('/api/documents/{document}', [DocumentController::class, 'show'])->name('api.documents.show');

    Route::get('/documents/generate/buddies/{year}', [DocumentController::class, 'generateBuddiesReport'])
    ->name('documents.generate.buddies');

Route::get('/documents/generate/friendships/{year}', [DocumentController::class, 'generateFriendshipsReport'])
    ->name('documents.generate.friendships');


    // routes/web.php
Route::resource('documents', DocumentController::class);

Route::prefix('reports')->name('reports.')->group(function () {
    // Ruta principal de reportes
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    
    // Rutas para obtener datos de cada tab via AJAX
    Route::get('/general', function(Request $request) {
        return app(ReportsController::class)->getTabData('general', $request);
    })->name('general');
    
    Route::get('/actividades', function(Request $request) {
        return app(ReportsController::class)->getTabData('actividades', $request);
    })->name('actividades');
    

    Route::get('/amistades', function(Request $request) {
        return app(ReportsController::class)->getTabData('amistades', $request);
    })->name('amistades');
    
    Route::get('/liderazgo', function(Request $request) {
        return app(ReportsController::class)->getTabData('liderazgo', $request);
    })->name('liderazgo');
    
    // Ruta para exportación
    Route::post('/export', [ReportsController::class, 'export'])->name('export');
    
    // Rutas adicionales para funcionalidades específicas
    Route::get('/stats/monthly', [ReportsController::class, 'getMonthlyStats'])->name('stats.monthly');
    Route::get('/stats/weekly', [ReportsController::class, 'getWeeklyStats'])->name('stats.weekly');
    Route::get('/charts/participation', [ReportsController::class, 'getParticipationChart'])->name('charts.participation');
    Route::get('/charts/activities', [ReportsController::class, 'getActivitiesChart'])->name('charts.activities');
});

// Si usas middleware de autenticación
Route::middleware(['auth'])->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/{tab}', [ReportsController::class, 'getTabData'])->name('tab.data');
        Route::post('/export', [ReportsController::class, 'export'])->name('export');
    });
});

Route::get('/reports/general', [ReportsController::class, 'general'])->name('reports.general');
Route::post('/reports/actividades', [ReportsController::class, 'actividades'])->name('reports.actividades');
Route::post('/reports/actividades', [ReportsController::class, 'actividades'])->name('reports.actividades');
// O también acepta GET para testing:
Route::match(['GET', 'POST'], '/reports/actividades', [ReportsController::class, 'actividades'])->name('reports.actividades');


Route::prefix('reports')->group(function() {
    Route::post('general', [ReportsController::class, 'general']);
    Route::post('actividades', [ReportsController::class, 'actividades']);
    Route::post('amistades', [ReportsController::class, 'amistades']);
    Route::post('liderazgo', [ReportsController::class, 'liderazgo']);
    Route::post('export', [ReportsController::class, 'export']);
});
Route::get('/friendships/tracking', [FriendshipController::class, 'tracking'])->name('friendships.tracking');
Route::prefix('friendships/{friendship}')->name('friendships.')->group(function () {
    // Mostrar modal de seguimiento
    Route::get('/tracking', [FriendshipController::class, 'showTracking'])->name('tracking.show');
    
    // Guardar nuevo seguimiento
    Route::post('/tracking', [FriendshipController::class, 'storeTracking'])->name('tracking.store');
    
    // Obtener datos de asistencia
    Route::get('/attendance', [FriendshipController::class, 'getAttendanceData'])->name('attendance.data');
    
    // Historial de seguimientos
    Route::get('/follow-ups', [FriendshipController::class, 'getFollowUpHistory'])->name('followups.history');
    
    // Actualizar seguimiento específico
    Route::put('/tracking/{followUp}', [FriendshipController::class, 'updateTracking'])->name('tracking.update');
    
    // Eliminar seguimiento específico
    Route::delete('/tracking/{followUp}', [FriendshipController::class, 'destroyTracking'])->name('tracking.destroy');
    
    // Generar reporte de seguimiento
    Route::get('/report', [FriendshipController::class, 'generateFollowUpReport'])->name('report.generate');
});

// Dashboard de seguimientos
Route::get('/follow-ups/dashboard', [FriendshipController::class, 'getFollowUpDashboard'])->name('followups.dashboard');
Route::get('/friendships/{friendship}/attendance-info', [FriendshipController::class, 'getAttendanceInfo']);
Route::post('/friendships/{friendship}/attendance', [FriendshipController::class, 'storeAttendance']);
Route::get('/friendships/{friendship}/show', [FriendshipController::class, 'show']);
Route::get('/friendships/{id}/details', [FriendshipController::class, 'show']);

Route::get('/friendships/{friendship}/show', [FriendshipController::class, 'show'])
    ->name('friendships.show');

    
    Route::get('/friendships/{id}/show', [FriendshipController::class, 'show']);

Route::get('/friendships/{friendship}/attendance-range', [FriendshipController::class, 'getAttendanceRange']);
Route::post('/friendships/{friendship}/attendance-bulk', [FriendshipController::class, 'storeAttendanceBulk']);

// routes/web.php
Route::post('/friendships/{friendship}/follow-ups', [FriendshipController::class, 'storeFollowUp'])
     ->name('friendships.follow-ups.store');

Route::put('/friendships/{friendship}/follow-ups/{followUp}', [FriendshipController::class, 'updateFollowUp'])
     ->name('friendships.follow-ups.update');

Route::post('/friendships/{friendship}/attendance', [FriendshipController::class, 'storeAttendance'])
    ->name('friendships.attendance.store');
Route::get('/friendships/{friendship}/attendance/check', [FriendshipController::class, 'checkAttendance'])
    ->name('friendships.attendance.check');
   
    
    Route::post('/friendships/{friendship}/attendance', [FriendshipController::class, 'storeAttendance'])
    ->name('friendships.attendance.store');

Route::get('/friendships/{friendship}/check-attendance', [FriendshipController::class, 'checkAttendance'])
    ->name('friendships.attendance.check');
  
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/stats', [DashboardController::class, 'getDetailedStats'])->name('dashboard.stats');

// En routes/web.php
Route::post('/reports/actividades', [ActivityController::class, 'getReportData'])->name('reports.actividades');
Route::post('/reports/actividades', [ActivityController::class, 'getReportData']);

Route::post('/reports/actividades', [ActivityController::class, 'getReportData'])->name('reports.actividades');
Route::get('/api/friendships/list', [FriendshipController::class, 'getFriendshipsList']);



Route::prefix('monitoring')->group(function () {
    // Obtener lista de amistades para el formulario
    Route::get('/friendships/list', [MonthlyMonitoringController::class, 'getFriendships']);
    
    // CRUD de reportes de monitoreo
    Route::post('/monthly', [MonthlyMonitoringController::class, 'store']);
    Route::get('/monthly', [MonthlyMonitoringController::class, 'getReports']);
    Route::get('/monthly/{id}', [MonthlyMonitoringController::class, 'show']);
    Route::put('/monthly/{id}', [MonthlyMonitoringController::class, 'update']);
    Route::delete('/monthly/{id}', [MonthlyMonitoringController::class, 'destroy']);
    
    // Estadísticas de monitoreo
    Route::get('/monthly/statistics/general', [MonthlyMonitoringController::class, 'getStatistics']);
});
// En tu archivo de rutas (api.php o web.php)
Route::get('/api/monitoring/friendships', [MonthlyMonitoringController::class, 'getFriendships']);
Route::post('/api/monitoring/monthly-reports', [MonthlyMonitoringController::class, 'store']);

Route::get('/monthly-monitoring', [MonthlyMonitoringController::class, 'index']);
Route::get('/monthly-monitoring/friendships', [MonthlyMonitoringController::class, 'getFriendships']);
Route::post('/monthly-monitoring', [MonthlyMonitoringController::class, 'store']);
// En routes/api.php o routes/web.php
Route::post('/api/monthly-monitoring/check-existing', [MonthlyMonitoringController::class, 'checkExistingReport']);

 // Ruta para mostrar el formulario de monitoreo
    Route::get('/monthly-monitoring', [MonthlyMonitoringController::class, 'index'])
        ->name('monitoring.monthly.index');
    
    // API endpoints para el monitoreo mensual
    Route::prefix('api')->group(function () {
        
        // Obtener lista de amistades activas
        Route::get('/friendships/list', [MonthlyMonitoringController::class, 'getFriendships'])
            ->name('api.friendships.list');
        
        // Verificar si existe un reporte
        Route::post('/monitoring/check-existing', [MonthlyMonitoringController::class, 'checkExistingReport'])
            ->name('api.monitoring.check-existing');
        
        // CRUD de reportes de monitoreo
        Route::get('/monitoring/reports', [MonthlyMonitoringController::class, 'getReports'])
            ->name('api.monitoring.reports.index');
        
        Route::get('/monitoring/reports/{id}', [MonthlyMonitoringController::class, 'show'])
            ->name('api.monitoring.reports.show');
        
        Route::put('/monitoring/reports/{id}', [MonthlyMonitoringController::class, 'update'])
            ->name('api.monitoring.reports.update');
        
        Route::delete('/monitoring/reports/{id}', [MonthlyMonitoringController::class, 'destroy'])
            ->name('api.monitoring.reports.destroy');
        
        // Estadísticas
        Route::get('/monitoring/statistics', [MonthlyMonitoringController::class, 'getStatistics'])
            ->name('api.monitoring.statistics');
    });
    
    // Ruta principal para guardar el monitoreo (POST)
    Route::post('/monthly-monitoring', [MonthlyMonitoringController::class, 'store'])
        ->name('monitoring.monthly.store');
Route::get('/api/friendships/list', [FriendshipController::class, 'getFriendshipsList'])->name('api.friendships.list');

Route::get('/activities/{activity}', [ActivityController::class, 'show']);

Route::get('/reports/liderazgo', [LiderazgoController::class, 'liderazgo'])
        ->name('reports.liderazgo');
    
    // Ruta para obtener detalles de un líder específico
    Route::get('/reports/liderazgo/leader/{leaderId}', [LiderazgoController::class, 'getLeaderDetail'])
        ->name('reports.liderazgo.leader');
    
    // Si necesitas una ruta para la vista principal de reportes
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
    
    Route::get('/api/liderazgo-dashboard', [App\Http\Controllers\LiderazgoController::class, 'getDashboardData'])->name('liderazgo.dashboard.data');
Route::get('/api/liderazgo-dashboard', [App\Http\Controllers\LiderazgoController::class, 'getDashboardData'])->name('liderazgo.dashboard.data');
    // En routes/web.php o routes/api.php
Route::get('/reports/liderazgo', [ReportsController::class, 'liderazgo']);
Route::get('/api/liderazgo-dashboard', [App\Http\Controllers\LiderazgoController::class, 'getDashboardData'])->name('liderazgo.dashboard.data');
Route::get('/monthly-monitoring/liderazgo-data', [MonthlyMonitoringController::class, 'getLiderazgoData']);


Route::get('/reports/liderazgo/data', [MonthlyMonitoringController::class, 'getLiderazgoData'])->name('reports.liderazgo.data');

// Rutas adicionales que podrías necesitar
Route::get('/reports/liderazgo/dashboard', [MonthlyMonitoringController::class, 'getDashboardData'])->name('reports.liderazgo.dashboard');
Route::get('/reports/liderazgo/progress', [MonthlyMonitoringController::class, 'getProgressSummary'])->name('reports.liderazgo.progress');

// Si necesitas las rutas de monitoreo también:
Route::prefix('monitoring')->group(function () {
    Route::get('/', [MonthlyMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/friendships', [MonthlyMonitoringController::class, 'getFriendships'])->name('monitoring.friendships');
    Route::post('/store', [MonthlyMonitoringController::class, 'store'])->name('monitoring.store');
    Route::get('/reports', [MonthlyMonitoringController::class, 'getReports'])->name('monitoring.reports');
    Route::get('/reports/{id}', [MonthlyMonitoringController::class, 'show'])->name('monitoring.show');
    Route::put('/reports/{id}', [MonthlyMonitoringController::class, 'update'])->name('monitoring.update');
    Route::delete('/reports/{id}', [MonthlyMonitoringController::class, 'destroy'])->name('monitoring.destroy');
    Route::get('/statistics', [MonthlyMonitoringController::class, 'getStatistics'])->name('monitoring.statistics');
    Route::post('/check-existing', [MonthlyMonitoringController::class, 'checkExistingReport'])->name('monitoring.check');
});



Route::get('/reports/monthly-monitoring/liderazgo-data', [MonthlyMonitoringController::class, 'getLiderazgoData'])
    ->name('reports.liderazgo.data');

Route::get('/reports/monthly-monitoring/liderazgo-data', [ReportsController::class, 'getLiderazgoData'])
    ->name('reports.liderazgo-data');

// 4. VERIFICAR MIDDLEWARE Y PERMISOS
// Asegúrate de que el usuario tenga los permisos necesarios
Route::get('/reports/monthly-monitoring/liderazgo-data', [ReportsController::class, 'getLiderazgoData'])
    ->middleware(['auth', 'verified']) // Ajusta según tus middlewares
    ->name('reports.liderazgo-data');

Route::get('/reports/monthly-monitoring/liderazgo-data', [ReportsController::class, 'getLiderazgoData']);

Route::get('/reports/monthly-monitoring/liderazgo-data', [ReportsController::class, 'getLiderazgoData'])
    ->name('reports.liderazgo.data');







// Rutas para reportes
Route::get('/reports/liderazgo', [ReportsController::class, 'liderazgo']);
Route::get('/reports/liderazgo/export/pdf', [ReportsController::class, 'exportLiderazgoPDF']);
Route::get('/reports/liderazgo/export/excel', [ReportsController::class, 'exportLiderazgoExcel']);
Route::get('/reports/liderazgo/export/word', [ReportsController::class, 'exportLiderazgoWord']);

// In routes/web.php
Route::get('/reports/liderazgo', [ReportsController::class, 'liderazgo'])->name('reports.liderazgo');



Route::middleware(['auth'])->group(function () {
    // Ruta para exportar reportes
    Route::post('/reports/export', [ReportsExportController::class, 'exportReport'])->name('reports.export');
    
    // Rutas adicionales para reportes si las necesitas
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/data/{section}', [ReportsController::class, 'getSectionData'])->name('reports.data');
});

Route::post('/reports/export', [ReportsController::class, 'export'])->name('reports.export');


// En tu web.php, agrega temporalmente:
Route::get('/debug-leadership', function() {
    $controller = new App\Http\Controllers\ReportsController();
    $dateFrom = Carbon::now()->startOfYear();
    $dateTo = Carbon::now()->endOfYear();
    
    // Usar reflection para acceder al método privado
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('debugLeadershipReport');
    $method->setAccessible(true);
    
    $debug = $method->invoke($controller, $dateFrom, $dateTo);
    
    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
});

Route::get('/check-config', function() {
    return [
        'mail_host' => config('mail.mailers.smtp.host'),
        'mail_username' => config('mail.mailers.smtp.username'),
        'mail_from' => config('mail.from.address'),
    ];
});

});

require __DIR__.'/auth.php';
