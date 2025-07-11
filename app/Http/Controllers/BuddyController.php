<?php
// app/Http/Controllers/BuddyController.php

namespace App\Http\Controllers;

use App\Models\Buddy;
use Illuminate\Http\Request;

class BuddyController extends Controller
{
    //Lista de buddies
    public function index()
    {
        $buddies = Buddy::paginate(10); // 10 items por página
        
        return view('buddies.index', compact('buddies'));
    }

    //Mostrar formulario de nuwvo buddy
    public function create()
    {
        return view('buddies.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:buddy,peer_buddy',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'ci' => 'required|string|max:20|unique:buddies',
            'disability' => 'required_if:type,buddy|nullable|string|max:255',
            'age' => 'required|integer|min:0|max:120',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'interests' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        Buddy::create($validated);
        
        return redirect()->route('friendships.index')
            ->with('success', 'Persona registrada exitosamente.');
    }
    
    public function destroy(Buddy $buddy)
    {
        $buddy->delete();
        
        return redirect()->route('friendships.index')
            ->with('success', 'Persona eliminada exitosamente.');
    }


    public function details(Buddy $buddy)
    {
        try {
            // Cargar la relación de amistad activa si existe
            $buddy->load(['activeFriendship.buddy', 'activeFriendship.peerBuddy']);
            
            return response()->json([
                'success' => true,
                'buddy' => [
                    'id' => $buddy->id,
                    'full_name' => $buddy->full_name,
                    'first_name' => $buddy->first_name,
                    'last_name' => $buddy->last_name,
                    'ci' => $buddy->ci,
                    'age' => $buddy->age,
                    'phone' => $buddy->phone,
                    'email' => $buddy->email,
                    'type' => $buddy->type,
                    'disability' => $buddy->disability,
                    'experience' => $buddy->experience,
                    'created_at' => $buddy->created_at,
                    'updated_at' => $buddy->updated_at,
                    'active_friendship' => $buddy->activeFriendship ? [
                        'id' => $buddy->activeFriendship->id,
                        'status' => $buddy->activeFriendship->status,
                        'start_date' => $buddy->activeFriendship->start_date,
                        'buddy' => [
                            'id' => $buddy->activeFriendship->buddy->id,
                            'full_name' => $buddy->activeFriendship->buddy->full_name,
                        ],
                        'peer_buddy' => [
                            'id' => $buddy->activeFriendship->peerBuddy->id,
                            'full_name' => $buddy->activeFriendship->peerBuddy->full_name,
                        ]
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles del buddy'
            ], 500);
        }
    }
    public function edit(Buddy $buddy)
    {
        try {
            return response()->json([
                'success' => true,
                'buddy' => [
                    'id' => $buddy->id,
                    'first_name' => $buddy->first_name,
                    'last_name' => $buddy->last_name,
                    'ci' => $buddy->ci,
                    'age' => $buddy->age,
                    'phone' => $buddy->phone,
                    'email' => $buddy->email,
                    'type' => $buddy->type,
                    'disability' => $buddy->disability,
                    'experience' => $buddy->experience,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del buddy'
            ], 500);
        }
    }

    public function update(Request $request, Buddy $buddy)
    {
        try {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'ci' => 'required|string|max:20|unique:buddies,ci,' . $buddy->id,
                'age' => 'required|integer|min:1|max:120',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255|unique:buddies,email,' . $buddy->id,
                'type' => 'required|in:buddy,peer_buddy',
            ];

            // Agregar reglas específicas según el tipo
            if ($request->type === 'buddy') {
                $rules['disability'] = 'required|string|max:255';
            } else {
                $rules['experience'] = 'required|string';
            }

            $validatedData = $request->validate($rules);

            // Limpiar campos según el tipo
            if ($validatedData['type'] === 'buddy') {
                $validatedData['experience'] = null;
            } else {
                $validatedData['disability'] = null;
            }

            $buddy->update($validatedData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Buddy actualizado exitosamente'
                ]);
            }

            return redirect()->route('buddies.index')
                ->with('success', 'Persona actualizada exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el buddy'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar la persona.')
                ->withInput();
        }
    }
}
