<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index()
    {
        // Verificar si el usuario actual tiene el rol adecuado
        if (auth()->user()->role && auth()->user()->role->name !== 'Encargado del Programa Amistad') {
            return redirect()->back()->with('error', 'No tienes permisos para ver esta página');
        }

        $users = User::with('role')->get();
        return view('roles.index', compact('users'));
    }

    public function show(User $user)
    {
        // Verificar permisos
        if (auth()->user()->role && auth()->user()->role->name !== 'Encargado del Programa Amistad') {
            return redirect()->back()->with('error', 'No tienes permisos para ver esta página');
        }
        
        $roleDescription = $user->role ? Role::getDescription($user->role->name) : 'Sin rol asignado';
        return view('roles.show', compact('user', 'roleDescription'));
    }

    public function edit(User $user)
    {
        // Verificar permisos
        if (auth()->user()->role && auth()->user()->role->name !== 'Encargado del Programa Amistad') {
            return redirect()->back()->with('error', 'No tienes permisos para editar usuarios');
        }
        
        $roles = Role::all();
        return view('roles.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Verificar permisos
        if (auth()->user()->role && auth()->user()->role->name !== 'Encargado del Programa Amistad') {
            return redirect()->back()->with('error', 'No tienes permisos para actualizar usuarios');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'city' => ['required', 'in:La Paz,Cochabamba']
        ]);

        $user->update($request->all());
        return redirect()->route('roles.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        // Verificar si el usuario actual tiene el rol adecuado
        if (auth()->user()->role && auth()->user()->role->name !== 'Encargado del Programa Amistad') {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar usuarios');
        }
        
        // Evitar que el usuario se elimine a sí mismo
        if (auth()->user()->id === $user->id) {
            return redirect()->route('roles.index')->with('error', 'No puedes eliminarte a ti mismo');
        }
        
        try {
            $userName = $user->name; // Guardar el nombre antes de eliminar
            $user->delete();
            return redirect()->route('roles.index')->with('success', "Usuario '{$userName}' eliminado correctamente");
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'No se pudo eliminar el usuario. Error: ' . $e->getMessage());
        }
    }
}