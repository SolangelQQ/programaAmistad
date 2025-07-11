<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleLoginController extends Controller
{
    /**
     * Redirecciona al usuario a la página de autenticación de Google
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtiene la información del usuario desde Google
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar el usuario por correo electrónico
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Usuario ya existe, actualizamos los detalles de Google si no los tiene
                if (!$existingUser->google_id) {
                    $existingUser->google_id = $googleUser->id;
                    $existingUser->save();
                }
                
                Auth::login($existingUser);
                return redirect()->intended('/dashboard');
            } else {
                // Usuario no existe, crear cuenta automáticamente con contraseña aleatoria
                // Solo necesitará seleccionar el rol
                return $this->createGoogleUser($googleUser);
            }
            
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }

    /**
     * Crea un usuario con datos de Google y redirige para seleccionar rol
     *
     * @param $googleUser
     * @return \Illuminate\Http\Response
     */
    private function createGoogleUser($googleUser)
    {
        try {
            // Generar contraseña aleatoria segura
            $randomPassword = $this->generateSecurePassword();
            
            // Crear usuario temporal con datos de Google
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => bcrypt($randomPassword),
                'email_verified_at' => now(), // Los usuarios de Google ya tienen email verificado
                'role_id' => null, // Será asignado después de seleccionar rol
            ]);

            // Guardar datos temporales en sesión para el formulario de selección de rol
            session([
                'google_registration' => [
                    'user_id' => $user->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'avatar' => $googleUser->avatar,
                    'password' => $randomPassword // Para mostrar al usuario si es necesario
                ]
            ]);

            // Redirigir a formulario de selección de rol
            return redirect()->route('google.select-role')
                ->with('success', 'Cuenta creada exitosamente. Por favor selecciona tu rol en la organización.');
                
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Error al crear la cuenta: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para seleccionar rol
     *
     * @return \Illuminate\Http\Response
     */
    public function showRoleSelection()
    {
        // Verificar que hay datos de registro de Google en sesión
        if (!session('google_registration')) {
            return redirect()->route('login')
                ->with('error', 'No se encontraron datos de registro de Google.');
        }

        $roles = Role::all();
        $googleData = session('google_registration');
        
        return view('auth.google-role-selection', compact('roles', 'googleData'));
    }

    /**
     * Completa el registro asignando el rol y ciudad seleccionados
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function completeRegistrationWithRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'city' => 'required|in:La Paz,Cochabamba',
        ]);

        // Verificar datos de sesión
        $googleData = session('google_registration');
        if (!$googleData) {
            return redirect()->route('login')
                ->with('error', 'Sesión expirada. Por favor inicia sesión nuevamente.');
        }

        try {
            // Buscar el usuario creado
            $user = User::find($googleData['user_id']);
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Usuario no encontrado.');
            }

            // Asignar rol y ciudad
            $user->role_id = $request->role_id;
            $user->city = $request->city;
            $user->save();

            // Asignar rol si usas Spatie Permission
            $role = Role::find($request->role_id);
            if ($role) {
                $user->assignRole($role->name);
            }

            // Limpiar sesión
            session()->forget('google_registration');

            // Iniciar sesión
            Auth::login($user);
            
            return redirect()->intended('/dashboard')
                ->with('success', '¡Registro completado exitosamente! Tu contraseña fue generada automáticamente.');
                
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al completar el registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Genera una contraseña segura aleatoria
     *
     * @return string
     */
    private function generateSecurePassword()
    {
        // Generar contraseña con al menos: 1 mayúscula, 1 minúscula, 1 número, 1 símbolo
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';
        
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $symbols[rand(0, strlen($symbols) - 1)];
        
        // Completar hasta 12 caracteres
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 12; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }

    /**
     * Permite al usuario ver su contraseña generada (opcional)
     *
     * @return \Illuminate\Http\Response
     */
    public function showGeneratedPassword()
    {
        $googleData = session('google_registration');
        if (!$googleData) {
            return redirect()->route('login');
        }

        return view('auth.show-password', ['password' => $googleData['password']]);
    }
}