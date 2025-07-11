<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class SettingsController extends Controller
{
    //Constructor para proteger las rutas con middleware auth
     
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Mostrar la página de configuración
     
    public function index()
    {
        $user = Auth::user();
        
        $settings = [
            'notifications_email' => true,
            'notifications_push' => true,
            'theme' => 'light',
            'language' => 'es',
        ];
        
        return view('settings.index', compact('user', 'settings'));
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'notifications_email' => 'boolean',
            'notifications_push' => 'boolean',
            'theme' => 'string|in:light,dark',
            'language' => 'string|in:es,en',
        ]);
        
        $user = Auth::user();
        
        // configuraciones 
        // Ejemplo:
        // foreach ($validated as $key => $value) {
        //     UserSetting::updateOrCreate(
        //         ['user_id' => $user->id, 'key' => $key],
        //         ['value' => $value]
        //     );
        // }
        
        return redirect()->route('settings.index')->with('success', 'Configuración actualizada correctamente');
    }
}