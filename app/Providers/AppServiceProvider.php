<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\App::setLocale('es');
        // $this->registerPolicies();
        // Configura el Route Model Binding explícito
        Route::model('user', User::class);
        
        // Opcional: Configuración de claves personalizadas si usas algo diferente a 'id'
        // Route::bind('user', function ($value) {
        //     return User::where('id', $value)->firstOrFail();
        // });
    
        
    }


}