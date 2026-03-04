<?php

namespace App\Http\Controllers;

use App\Models\CumpleanosConfiguracion;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CumpleanosConfiguracionController extends Controller
{
    public function index()
    {
        $config = CumpleanosConfiguracion::obtener();

        $stats = [
            'enviados_hoy' => EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
                ->where('estado', EmailLog::ESTADO_ENVIADO)
                ->whereDate('created_at', today())
                ->count(),
            'enviados_mes' => EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
                ->where('estado', EmailLog::ESTADO_ENVIADO)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'enviados_anio' => EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
                ->where('estado', EmailLog::ESTADO_ENVIADO)
                ->whereYear('created_at', now()->year)
                ->count(),
            'fallidos_mes' => EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
                ->where('estado', EmailLog::ESTADO_FALLIDO)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $placeholders = [
            '{nombre}' => 'Nombre del trabajador',
            '{apellidos}' => 'Apellidos del trabajador',
            '{nombre_completo}' => 'Nombre completo (nombre + apellidos)',
        ];

        $logReciente = EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
            ->latest()
            ->take(10)
            ->get();

        return view('cumpleanos.configuracion', compact('config', 'stats', 'placeholders', 'logReciente'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'asunto' => 'required|string|max:255',
            'cuerpo' => 'required|string',
            'hora_envio' => 'required|date_format:H:i',
        ]);

        $config = CumpleanosConfiguracion::obtener();
        $config->update($request->only(['asunto', 'cuerpo', 'hora_envio']));

        return back()->with('success', 'Plantilla de cumpleaños actualizada correctamente.');
    }

    public function toggleActiva()
    {
        $config = CumpleanosConfiguracion::obtener();
        $config->update(['activa' => !$config->activa]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $config->activa ? 'Emails de cumpleaños activados' : 'Emails de cumpleaños desactivados',
                'activa' => $config->activa,
            ]);
        }

        return back()->with('success', $config->activa ? 'Emails activados' : 'Emails desactivados');
    }

    public function subirAdjunto(Request $request)
    {
        $request->validate([
            'adjunto' => 'required|file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $config = CumpleanosConfiguracion::obtener();

        // Eliminar adjunto anterior si existe
        if ($config->adjunto_path && file_exists(public_path($config->adjunto_path))) {
            unlink(public_path($config->adjunto_path));
        }

        $file = $request->file('adjunto');
        $nombre = 'cumpleanos_' . time() . '.' . $file->getClientOriginalExtension();

        // Crear directorio si no existe
        $directorio = public_path('uploads/cumpleanos');
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $file->move($directorio, $nombre);

        $config->update([
            'adjunto_path' => 'uploads/cumpleanos/' . $nombre,
            'adjunto_nombre_original' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Adjunto subido correctamente.');
    }

    public function eliminarAdjunto()
    {
        $config = CumpleanosConfiguracion::obtener();

        if ($config->adjunto_path && file_exists(public_path($config->adjunto_path))) {
            unlink(public_path($config->adjunto_path));
        }

        $config->update([
            'adjunto_path' => null,
            'adjunto_nombre_original' => null,
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Adjunto eliminado']);
        }

        return back()->with('success', 'Adjunto eliminado correctamente.');
    }

    public function enviarPrueba(Request $request)
    {
        $request->validate([
            'email_prueba' => 'required|email',
        ]);

        try {
            Artisan::call('cumpleanos:enviar', [
                '--test-email' => $request->email_prueba,
            ]);

            return back()->with('success', 'Email de prueba enviado a ' . $request->email_prueba);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }
}
