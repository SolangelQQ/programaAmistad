<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
     public function login(Request $request)
     {
         $credentials = $request->validate([
             'email' => 'required|email',
             'password' => 'required',
         ]);
     
         if (Auth::attempt($credentials, $request->remember)) {
             $request->session()->regenerate();
             return redirect()->intended('/dashboard');
         }
     
         return back()->withErrors([
             'email' => 'Estas credenciales no coinciden con nuestros registros.',
         ]);
     }
     
     public function redirectToGoogle()
     {
         return Socialite::driver('google')->redirect();
     }
     
     public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // Crear nuevo usuario si no existe
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Contraseña aleatoria
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(), // Marcamos como verificado ya que viene de Google
                ]);
            } else {
                // Actualizar google_id si el usuario existe pero no lo tenía registrado
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }
            
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            Log::error('Error en Google callback: ' . $e->getMessage());
            return redirect('/login')->withErrors('Error al autenticar con Google: ' . $e->getMessage());
        }
    }
}