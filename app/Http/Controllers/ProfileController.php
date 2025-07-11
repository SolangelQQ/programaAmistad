<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Constructor para proteger las rutas con middleware auth
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    // Actualizar la información del perfil
         public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        
        return redirect()->route('profile.index')->with('success', 'Perfil actualizado correctamente');
    }

    //Cambiar la contraseña del usuario
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
        }
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return redirect()->route('profile.index')->with('success', 'Contraseña actualizada correctamente');
    }
}