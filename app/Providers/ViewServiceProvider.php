<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
        // Compartir datos con todas las vistas
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with([
                    'userName' => Auth::user()->name,
                    'userRole' => Auth::user()->role->name ?? 'Usuario',
                ]);
            }
        });

        // Determinar el título de la página según la ruta actual
        View::composer('layouts.app', function ($view) {
            $titles = [
                'dashboard' => 'Panel Principal',
                'emparejamiento' => 'Emparejamiento de Amistades',
                'reportes' => 'Reportes',
                'documentos' => 'Documentos',
                'roles.index' => 'Gestión de Roles',
                'roles.create' => 'Crear Rol',
                'roles.edit' => 'Editar Rol',
            ];

            foreach ($titles as $route => $title) {
                if (request()->routeIs($route)) {
                    $view->with('title', $title);
                    break;
                }
            }
        });
    }
}