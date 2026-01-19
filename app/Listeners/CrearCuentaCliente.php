<?php

namespace App\Listeners;

use App\Events\CotizacionCreada;
use App\Mail\CuentaClienteCreada;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Listener que crea automáticamente una cuenta de usuario para el cliente
 * cuando se crea una cotización, si el cliente no tiene cuenta aún
 */
class CrearCuentaCliente
{
    /**
     * Handle the event.
     */
    public function handle(CotizacionCreada $event): void
    {
        $solicitud = $event->solicitud;
        $cliente = $solicitud->cliente;

        // Si el cliente ya tiene una cuenta de usuario, no hacer nada
        if ($cliente->user_id) {
            return;
        }

        // Verificar si ya existe un usuario con el mismo email
        $usuarioExistente = User::where('email', $cliente->email)->first();

        if ($usuarioExistente) {
            // Vincular el cliente con el usuario existente
            $cliente->user_id = $usuarioExistente->id;
            $cliente->save();

            // Asignar rol cliente si no lo tiene
            if (!$usuarioExistente->hasRole('cliente')) {
                $usuarioExistente->assignRole('cliente');
            }

            return;
        }

        // Generar contraseña temporal
        $passwordTemporal = Str::random(10);

        try {
            // Crear nuevo usuario
            $usuario = User::create([
                'name' => $cliente->nombre_contacto,
                'email' => $cliente->email,
                'password' => Hash::make($passwordTemporal),
                'telefono' => $cliente->telefono,
                'activo' => true,
            ]);

            // Asignar rol cliente
            $usuario->assignRole('cliente');

            // Vincular cliente con usuario
            $cliente->user_id = $usuario->id;
            $cliente->save();

            // Enviar email con credenciales
            Mail::to($cliente->email)->send(
                new CuentaClienteCreada($cliente, $usuario, $passwordTemporal)
            );

            Log::info("Cuenta de cliente creada automáticamente para: {$cliente->email}");

        } catch (\Exception $e) {
            Log::error("Error al crear cuenta de cliente: " . $e->getMessage());
        }
    }
}
