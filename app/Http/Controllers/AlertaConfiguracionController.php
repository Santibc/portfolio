<?php

namespace App\Http\Controllers;

use App\Models\AlertaConfiguracion;
use App\Services\AlertaService;
use Illuminate\Http\Request;

class AlertaConfiguracionController extends Controller
{
    /**
     * Listado de configuraciones de alerta
     */
    public function index()
    {
        $configuraciones = AlertaConfiguracion::orderBy('tipo')->get();

        // Agregar etiquetas legibles
        $configuraciones->transform(function ($config) {
            $config->tipo_label = AlertaService::getTipoLabel($config->tipo);
            $config->tipo_icono = AlertaService::getTipoIcono($config->tipo);
            return $config;
        });

        // Estadísticas
        $stats = [
            'total' => $configuraciones->count(),
            'activas' => $configuraciones->where('activa', true)->count(),
            'inactivas' => $configuraciones->where('activa', false)->count(),
        ];

        return view('alertas.configuracion.index', compact('configuraciones', 'stats'));
    }

    /**
     * Actualizar configuración (días de antelación, activa)
     */
    public function update(Request $request, AlertaConfiguracion $configuracion)
    {
        $request->validate([
            'dias_antelacion' => 'required|integer|min:1|max:365',
            'activa' => 'sometimes|boolean',
        ]);

        $configuracion->update([
            'dias_antelacion' => $request->dias_antelacion,
            'activa' => $request->has('activa') ? $request->boolean('activa') : $configuracion->activa,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente',
                'configuracion' => $configuracion,
            ]);
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Toggle activar/desactivar tipo de alerta (AJAX)
     */
    public function toggleActiva(AlertaConfiguracion $configuracion)
    {
        $configuracion->update([
            'activa' => !$configuracion->activa,
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $configuracion->activa ? 'Alerta activada' : 'Alerta desactivada',
                'activa' => $configuracion->activa,
            ]);
        }

        return back()->with('success', $configuracion->activa ? 'Alerta activada' : 'Alerta desactivada');
    }
}
