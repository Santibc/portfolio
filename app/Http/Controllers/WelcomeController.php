<?php

namespace App\Http\Controllers;

use App\Models\PlanMembresia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Mostrar página de bienvenida con planes de membresía
     */
    public function index()
    {
        // Obtener planes activos ordenados
        $planes = PlanMembresia::activos()->get();
        
        return view('welcome', compact('planes'));
    }

    /**
     * Procesar el formulario de contacto de la lista de espera
     */
    public function enviarFormularioContacto(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:emprendimiento,fundacion',
            'online' => 'required|in:si,no',
            'festival' => 'required|in:si,no',
            'redes_sociales' => 'nullable|string|max:255',
            'red_social' => 'nullable|in:facebook,instagram,tiktok',
            'participar_eventos' => 'required|in:no_interesado,si_claro,depende_evento',
            'email' => 'required|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'mensaje_adicional' => 'nullable|string|max:1000',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'tipo.required' => 'Debes seleccionar si eres emprendimiento o fundación',
            'tipo.in' => 'Tipo de negocio inválido',
            'online.required' => 'Debes indicar si ya vendes en línea',
            'online.in' => 'Opción inválida para ventas en línea',
            'festival.required' => 'Debes indicar si has invertido en festivales',
            'festival.in' => 'Opción inválida para festivales',
            'participar_eventos.required' => 'Debes indicar si te gustaría participar en eventos',
            'participar_eventos.in' => 'Opción inválida para participación en eventos',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido',
        ]);

        try {
            // Obtener todos los usuarios con rol admin
            $admins = User::role('admin')->get();
            
            // Si no hay usuarios con rol admin, usar el scope de administradores
            if ($admins->isEmpty()) {
                $admins = User::administradores()->get();
            }

            // Datos del formulario para el email
            $datosFormulario = [
                'nombre' => $request->nombre,
                'tipo' => $request->tipo == 'emprendimiento' ? 'Emprendimiento' : 'Fundación',
                'online' => $request->online == 'si' ? 'Sí' : 'No',
                'festival' => $request->festival == 'si' ? 'Sí' : 'No',
                'redes_sociales' => $request->redes_sociales,
                'red_social' => $request->red_social,
                'participar_eventos' => $this->formatearParticipacionEventos($request->participar_eventos),
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'mensaje_adicional' => $request->mensaje_adicional,
                'fecha_envio' => now()->format('d/m/Y H:i:s')
            ];

            // Enviar email a cada administrador
            foreach ($admins as $admin) {
                Mail::send('emails.formulario-contacto', $datosFormulario, function ($message) use ($admin, $datosFormulario) {
                    $message->to($admin->email, $admin->name)
                            ->subject('Nueva solicitud de lista de espera - ' . $datosFormulario['nombre'])
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });
            }

            return back()->with('success', '¡Gracias por tu interés! Hemos recibido tu solicitud y nos pondremos en contacto contigo pronto.');

        } catch (\Exception $e) {
            \Log::error('Error enviando formulario de contacto: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al enviar tu solicitud. Por favor intenta nuevamente.')->withInput();
        }
    }

    /**
     * Formatear texto de participación en eventos
     */
    private function formatearParticipacionEventos($valor)
    {
        switch ($valor) {
            case 'no_interesado':
                return 'No está interesado';
            case 'si_claro':
                return 'Sí, claro';
            case 'depende_evento':
                return 'Depende del evento';
            default:
                return $valor;
        }
    }
}
