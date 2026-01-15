<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Compra;
use App\Models\PuntoCliente;
use App\Models\ConfiguracionPuntos;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterClienteController extends Controller
{
    /**
     * Display the registration view for clients.
     */
    public function create(Request $request): View
    {
        // Obtener código de referido si viene en la URL
        $codigoReferido = $request->query('ref');
        $referente = null;

        if ($codigoReferido) {
            $referente = User::where('codigo_referido', $codigoReferido)->first();
        }

        return view('auth.register-cliente', compact('referente', 'codigoReferido'));
    }

    /**
     * Handle an incoming registration request for clients.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telefono' => ['nullable', 'string', 'max:255'],
            'codigo_referido' => ['nullable', 'string', 'exists:users,codigo_referido'],
        ]);

        DB::beginTransaction();

        try {
            // Obtener empresa del contexto (primera empresa activa por defecto)
            $empresaId = session('empresa_id') ?? \App\Models\Empresa::where('activo', true)->first()?->id ?? 1;

            // Obtener configuración de puntos
            $configPuntos = ConfiguracionPuntos::getConfiguracion($empresaId);

            // Procesar referido si viene
            $referidoPor = null;
            if ($request->codigo_referido) {
                $referente = User::where('codigo_referido', $request->codigo_referido)->first();
                if ($referente && $referente->email !== $request->email) {
                    $referidoPor = $referente->id;
                }
            }

            // Generar código de referido único para el nuevo usuario
            $codigoReferidoNuevo = Str::upper(Str::random(8));
            while (User::where('codigo_referido', $codigoReferidoNuevo)->exists()) {
                $codigoReferidoNuevo = Str::upper(Str::random(8));
            }

            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'codigo_referido' => $codigoReferidoNuevo,
                'referido_por' => $referidoPor,
            ]);

            // Asignar rol cliente
            $user->assignRole('cliente');

            // Vincular compras existentes por email
            Compra::where('email_cliente', $request->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            // === SISTEMA DE PUNTOS ===
            if ($configPuntos && $configPuntos->sistema_activo) {

                // 1. Puntos por registro
                if ($configPuntos->puntos_registro > 0) {
                    PuntoCliente::registrarPuntos(
                        $user->id,
                        $configPuntos->puntos_registro,
                        'registro',
                        'Puntos de bienvenida por registrarte',
                        null,
                        $configPuntos->getFechaExpiracion()
                    );
                }

                // 2. Puntos para quien fue referido
                if ($referidoPor && $configPuntos->referidos_activo && $configPuntos->puntos_ser_referido > 0) {
                    PuntoCliente::registrarPuntos(
                        $user->id,
                        $configPuntos->puntos_ser_referido,
                        'referido',
                        'Puntos por registrarte con código de referido',
                        null,
                        $configPuntos->getFechaExpiracion()
                    );
                }

                // 3. Puntos para quien refirió
                if ($referidoPor && $configPuntos->referidos_activo && $configPuntos->puntos_referir > 0) {
                    PuntoCliente::registrarPuntosReferido(
                        $referidoPor,
                        $user->name,
                        $configPuntos->puntos_referir
                    );
                }
            }

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            // Mensaje de bienvenida con puntos si aplica
            if ($configPuntos && $configPuntos->sistema_activo && $configPuntos->puntos_registro > 0) {
                session()->flash('success', "¡Bienvenido! Has ganado {$configPuntos->puntos_registro} puntos de bienvenida.");
            }

            // Redirigir al panel de cliente
            return redirect()->route('cliente.compras');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'email' => 'Error al crear la cuenta: ' . $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
