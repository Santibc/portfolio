<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteEmailAdicional;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteEmailController extends Controller
{
    /**
     * Store a newly created email
     */
    public function store(Request $request, Cliente $cliente): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'nombre' => 'nullable|string|max:150',
                'cargo' => 'nullable|string|max:150',
                'enviar_facturas_por_defecto' => 'nullable|boolean',
                'notas' => 'nullable|string|max:500',
            ]);

            // Check for duplicates
            $exists = $cliente->emailsAdicionales()
                ->where('email', $validated['email'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya existe para el cliente.',
                ], 422);
            }

            $emailAdicional = $cliente->emailsAdicionales()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Email adicional creado correctamente.',
                'email' => $emailAdicional,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified email
     */
    public function update(Request $request, ClienteEmailAdicional $emailAdicional): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'nombre' => 'nullable|string|max:150',
                'cargo' => 'nullable|string|max:150',
                'activo' => 'nullable|boolean',
                'enviar_facturas_por_defecto' => 'nullable|boolean',
                'notas' => 'nullable|string|max:500',
            ]);

            // Check for duplicates (excluding current)
            $exists = ClienteEmailAdicional::where('cliente_id', $emailAdicional->cliente_id)
                ->where('email', $validated['email'])
                ->where('id', '!=', $emailAdicional->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya existe para el cliente.',
                ], 422);
            }

            $emailAdicional->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Email actualizado correctamente.',
                'email' => $emailAdicional->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified email
     */
    public function destroy(ClienteEmailAdicional $emailAdicional): JsonResponse
    {
        try {
            $emailAdicional->delete();

            return response()->json([
                'success' => true,
                'message' => 'Email eliminado correctamente.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
