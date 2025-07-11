<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Cerrar sesión de usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout(); 
        $request->session()->invalidate(); // Invalida la sesión
        $request->session()->regenerateToken(); // Regenera el token CSRF

        return response()->json(['message' => 'Cuenta cerrada exitosamente']);
    }
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->remember) {
            Cookie::queue(Cookie::make('remember_web_'.Auth::id(), true, 43200)); // 30 días
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
