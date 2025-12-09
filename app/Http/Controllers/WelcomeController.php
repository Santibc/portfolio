<?php

namespace App\Http\Controllers;

use App\Models\PlanMembresia;
use App\Models\LandingConfiguracion;
use App\Models\LandingPagina;
use App\Models\LandingEvento;
use App\Models\LandingValor;
use App\Models\LandingEstadistica;
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
        $config = LandingConfiguracion::obtener();
        $pagina = LandingPagina::porSlug('inicio');
        $planes = PlanMembresia::activos()->get();

        return view('welcome', compact('config', 'pagina', 'planes'));
    }

    /**
     * Mostrar página de planes de membresía
     */
    public function planes()
    {
        $config = LandingConfiguracion::obtener();
        $pagina = LandingPagina::porSlug('planes');
        $planes = PlanMembresia::activos()->get();

        return view('planes', compact('config', 'pagina', 'planes'));
    }

    /**
     * Mostrar página de contacto
     */
    public function contacto()
    {
        $config = LandingConfiguracion::obtener();
        $pagina = LandingPagina::porSlug('contacto');

        return view('contacto', compact('config', 'pagina'));
    }

    /**
     * Mostrar página Sobre Nosotros
     */
    public function sobreNosotros()
    {
        $config = LandingConfiguracion::obtener();
        $pagina = LandingPagina::porSlug('nosotros');
        $valores = LandingValor::activos()->ordenados()->get();
        $estadisticas = LandingEstadistica::activas()->ordenadas()->get();

        return view('sobre-nosotros', compact('config', 'pagina', 'valores', 'estadisticas'));
    }

    /**
     * Mostrar listado de eventos
     */
    public function eventos()
    {
        $config = LandingConfiguracion::obtener();
        $pagina = LandingPagina::porSlug('eventos');
        $eventos = LandingEvento::activos()->ordenados()->get();

        return view('eventos', compact('config', 'pagina', 'eventos'));
    }

    /**
     * Mostrar detalle de un evento
     */
    public function eventoDetalle($slug)
    {
        $config = LandingConfiguracion::obtener();
        $evento = LandingEvento::where('slug', $slug)->where('activo', true)->firstOrFail();

        return view('evento-detalle', compact('config', 'evento'));
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
            // Obtener email de contacto desde la configuración
            $config = LandingConfiguracion::obtener();
            $emailDestino = $config->contacto_email ?? config('mail.from.address');

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

            // Enviar email al correo configurado
            Mail::send('emails.formulario-contacto', $datosFormulario, function ($message) use ($emailDestino, $datosFormulario) {
                $message->to($emailDestino)
                        ->subject('Nueva solicitud de lista de espera - ' . $datosFormulario['nombre'])
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

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
