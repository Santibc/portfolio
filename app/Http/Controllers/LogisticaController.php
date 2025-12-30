<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ZonaCobertura;
use App\Models\TarifaZona;
use App\Models\HorarioEntrega;
use App\Models\ReglaCapacidad;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Auth;

class LogisticaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================================
    // ZONAS DE COBERTURA
    // ============================================

    public function indexZonas()
    {
        $empresa = Auth::user()->empresa;
        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->orderBy('orden')
            ->get();

        return view('logistica.zonas.index', compact('zonas', 'empresa'));
    }

    public function createZona()
    {
        $empresa = Auth::user()->empresa;
        $ciudades = Ciudad::orderBy('nombre')->get();

        return view('logistica.zonas.create', compact('empresa', 'ciudades'));
    }

    public function storeZona(Request $request)
    {
        $empresa = Auth::user()->empresa;

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ciudades_ids' => 'required|array|min:1',
            'ciudades_ids.*' => 'exists:ciudades,id',
            'barrios' => 'nullable|array',
            'codigo_postal_desde' => 'nullable|string|max:20',
            'codigo_postal_hasta' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);

        $validated['empresa_id'] = $empresa->id;
        $validated['activo'] = $request->boolean('activo', true);

        $zona = ZonaCobertura::create($validated);

        return redirect()->route('logistica.zonas.index')
            ->with('success', 'Zona de cobertura creada exitosamente');
    }

    public function editZona($id)
    {
        $empresa = Auth::user()->empresa;
        $zona = ZonaCobertura::where('empresa_id', $empresa->id)->findOrFail($id);
        $ciudades = Ciudad::orderBy('nombre')->get();

        return view('logistica.zonas.edit', compact('zona', 'ciudades', 'empresa'));
    }

    public function updateZona(Request $request, $id)
    {
        $empresa = Auth::user()->empresa;
        $zona = ZonaCobertura::where('empresa_id', $empresa->id)->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ciudades_ids' => 'required|array|min:1',
            'ciudades_ids.*' => 'exists:ciudades,id',
            'barrios' => 'nullable|array',
            'codigo_postal_desde' => 'nullable|string|max:20',
            'codigo_postal_hasta' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $zona->update($validated);

        return redirect()->route('logistica.zonas.index')
            ->with('success', 'Zona de cobertura actualizada exitosamente');
    }

    public function destroyZona($id)
    {
        $empresa = Auth::user()->empresa;
        $zona = ZonaCobertura::where('empresa_id', $empresa->id)->findOrFail($id);

        $zona->delete();

        return redirect()->route('logistica.zonas.index')
            ->with('success', 'Zona de cobertura eliminada exitosamente');
    }

    // ============================================
    // TARIFAS POR ZONA
    // ============================================

    public function indexTarifas()
    {
        $empresa = Auth::user()->empresa;
        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->with('tarifas')
            ->orderBy('orden')
            ->get();

        return view('logistica.tarifas.index', compact('zonas', 'empresa'));
    }

    public function createTarifa($zonaId = null)
    {
        $empresa = Auth::user()->empresa;
        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->activo()
            ->orderBy('nombre')
            ->get();

        return view('logistica.tarifas.create', compact('zonas', 'empresa', 'zonaId'));
    }

    public function storeTarifa(Request $request)
    {
        $validated = $request->validate([
            'zona_cobertura_id' => 'required|exists:zonas_cobertura,id',
            'nombre' => 'required|string|max:255',
            'costo_base' => 'required|numeric|min:0',
            'costo_por_km' => 'nullable|numeric|min:0',
            'minimo_compra' => 'required|numeric|min:0',
            'costo_si_no_alcanza_minimo' => 'nullable|numeric|min:0',
            'envio_gratis_desde' => 'boolean',
            'monto_envio_gratis' => 'nullable|numeric|min:0',
            'tiempo_entrega_horas' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);
        $validated['envio_gratis_desde'] = $request->boolean('envio_gratis_desde', false);

        TarifaZona::create($validated);

        return redirect()->route('logistica.tarifas.index')
            ->with('success', 'Tarifa creada exitosamente');
    }

    public function editTarifa($id)
    {
        $empresa = Auth::user()->empresa;
        $tarifa = TarifaZona::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->activo()
            ->orderBy('nombre')
            ->get();

        return view('logistica.tarifas.edit', compact('tarifa', 'zonas', 'empresa'));
    }

    public function updateTarifa(Request $request, $id)
    {
        $empresa = Auth::user()->empresa;
        $tarifa = TarifaZona::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'zona_cobertura_id' => 'required|exists:zonas_cobertura,id',
            'nombre' => 'required|string|max:255',
            'costo_base' => 'required|numeric|min:0',
            'costo_por_km' => 'nullable|numeric|min:0',
            'minimo_compra' => 'required|numeric|min:0',
            'costo_si_no_alcanza_minimo' => 'nullable|numeric|min:0',
            'envio_gratis_desde' => 'boolean',
            'monto_envio_gratis' => 'nullable|numeric|min:0',
            'tiempo_entrega_horas' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);
        $validated['envio_gratis_desde'] = $request->boolean('envio_gratis_desde', false);

        $tarifa->update($validated);

        return redirect()->route('logistica.tarifas.index')
            ->with('success', 'Tarifa actualizada exitosamente');
    }

    public function destroyTarifa($id)
    {
        $empresa = Auth::user()->empresa;
        $tarifa = TarifaZona::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $tarifa->delete();

        return redirect()->route('logistica.tarifas.index')
            ->with('success', 'Tarifa eliminada exitosamente');
    }

    // ============================================
    // HORARIOS DE ENTREGA
    // ============================================

    public function indexHorarios()
    {
        $empresa = Auth::user()->empresa;
        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->with('horarios')
            ->orderBy('orden')
            ->get();

        return view('logistica.horarios.index', compact('zonas', 'empresa'));
    }

    public function createHorario($zonaId = null)
    {
        $empresa = Auth::user()->empresa;
        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->activo()
            ->orderBy('nombre')
            ->get();

        return view('logistica.horarios.create', compact('zonas', 'empresa', 'zonaId'));
    }

    public function storeHorario(Request $request)
    {
        $validated = $request->validate([
            'zona_cobertura_id' => 'required|exists:zonas_cobertura,id',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'nombre' => 'nullable|string|max:255',
            'capacidad_pedidos' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        HorarioEntrega::create($validated);

        return redirect()->route('logistica.horarios.index')
            ->with('success', 'Horario de entrega creado exitosamente');
    }

    public function editHorario($id)
    {
        $empresa = Auth::user()->empresa;
        $horario = HorarioEntrega::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $zonas = ZonaCobertura::where('empresa_id', $empresa->id)
            ->activo()
            ->orderBy('nombre')
            ->get();

        return view('logistica.horarios.edit', compact('horario', 'zonas', 'empresa'));
    }

    public function updateHorario(Request $request, $id)
    {
        $empresa = Auth::user()->empresa;
        $horario = HorarioEntrega::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'zona_cobertura_id' => 'required|exists:zonas_cobertura,id',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'nombre' => 'nullable|string|max:255',
            'capacidad_pedidos' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $horario->update($validated);

        return redirect()->route('logistica.horarios.index')
            ->with('success', 'Horario de entrega actualizado exitosamente');
    }

    public function destroyHorario($id)
    {
        $empresa = Auth::user()->empresa;
        $horario = HorarioEntrega::whereHas('zona', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->findOrFail($id);

        $horario->delete();

        return redirect()->route('logistica.horarios.index')
            ->with('success', 'Horario de entrega eliminado exitosamente');
    }

    // ============================================
    // REGLAS DE CAPACIDAD
    // ============================================

    public function indexCapacidad()
    {
        $empresa = Auth::user()->empresa;
        $reglas = ReglaCapacidad::where('empresa_id', $empresa->id)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('logistica.capacidad.index', compact('reglas', 'empresa'));
    }

    public function createCapacidad()
    {
        $empresa = Auth::user()->empresa;

        return view('logistica.capacidad.create', compact('empresa'));
    }

    public function storeCapacidad(Request $request)
    {
        $empresa = Auth::user()->empresa;

        $validated = $request->validate([
            'fecha' => 'required|date|after_or_equal:today|unique:reglas_capacidad,fecha,NULL,id,empresa_id,' . $empresa->id,
            'capacidad_total_dia' => 'required|integer|min:1',
            'capacidad_por_hora' => 'required|integer|min:1',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $validated['empresa_id'] = $empresa->id;
        $validated['activo'] = $request->boolean('activo', true);

        ReglaCapacidad::create($validated);

        return redirect()->route('logistica.capacidad.index')
            ->with('success', 'Regla de capacidad creada exitosamente');
    }

    public function editCapacidad($id)
    {
        $empresa = Auth::user()->empresa;
        $regla = ReglaCapacidad::where('empresa_id', $empresa->id)->findOrFail($id);

        return view('logistica.capacidad.edit', compact('regla', 'empresa'));
    }

    public function updateCapacidad(Request $request, $id)
    {
        $empresa = Auth::user()->empresa;
        $regla = ReglaCapacidad::where('empresa_id', $empresa->id)->findOrFail($id);

        $validated = $request->validate([
            'fecha' => 'required|date|after_or_equal:today|unique:reglas_capacidad,fecha,' . $id . ',id,empresa_id,' . $empresa->id,
            'capacidad_total_dia' => 'required|integer|min:1',
            'capacidad_por_hora' => 'required|integer|min:1',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $regla->update($validated);

        return redirect()->route('logistica.capacidad.index')
            ->with('success', 'Regla de capacidad actualizada exitosamente');
    }

    public function destroyCapacidad($id)
    {
        $empresa = Auth::user()->empresa;
        $regla = ReglaCapacidad::where('empresa_id', $empresa->id)->findOrFail($id);

        $regla->delete();

        return redirect()->route('logistica.capacidad.index')
            ->with('success', 'Regla de capacidad eliminada exitosamente');
    }

    // ============================================
    // API PARA CHECKOUT
    // ============================================

    public function verificarDisponibilidad(Request $request)
    {
        $empresaId = $request->input('empresa_id');
        $ciudadId = $request->input('ciudad_id');
        $fecha = $request->input('fecha');
        $subtotal = $request->input('subtotal', 0);

        // Buscar zona que cubra la ciudad
        $zona = ZonaCobertura::where('empresa_id', $empresaId)
            ->activo()
            ->get()
            ->first(function($z) use ($ciudadId) {
                return $z->cubreCiudad($ciudadId);
            });

        if (!$zona) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Lo sentimos, no hacemos entregas en tu zona.',
            ]);
        }

        // Obtener tarifas de la zona
        $tarifas = $zona->tarifas()->activo()->get();

        if ($tarifas->isEmpty()) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'No hay tarifas configuradas para tu zona.',
            ]);
        }

        // Verificar capacidad del día
        $reglaCapacidad = ReglaCapacidad::porEmpresa($empresaId)
            ->porFecha($fecha)
            ->activo()
            ->first();

        if ($reglaCapacidad && !$reglaCapacidad->tieneCapacidad($fecha)) {
            return response()->json([
                'disponible' => false,
                'mensaje' => 'Lo sentimos, hemos alcanzado nuestra capacidad para esa fecha. Por favor selecciona otra fecha.',
            ]);
        }

        // Calcular costo de envío con la primera tarifa activa
        $tarifa = $tarifas->first();
        $costoEnvio = $tarifa->calcularCosto($subtotal);

        return response()->json([
            'disponible' => true,
            'zona_nombre' => $zona->nombre,
            'costo_envio' => $costoEnvio,
            'tiempo_entrega_horas' => $tarifa->tiempo_entrega_horas,
            'mensaje' => $costoEnvio == 0 ? '¡Envío gratis!' : null,
        ]);
    }

    public function obtenerHorariosDisponibles(Request $request)
    {
        $empresaId = $request->input('empresa_id');
        $ciudadId = $request->input('ciudad_id');
        $fecha = $request->input('fecha');

        // Buscar zona
        $zona = ZonaCobertura::where('empresa_id', $empresaId)
            ->activo()
            ->get()
            ->first(function($z) use ($ciudadId) {
                return $z->cubreCiudad($ciudadId);
            });

        if (!$zona) {
            return response()->json(['horarios' => []]);
        }

        // Obtener día de la semana
        $diaSemana = strtolower(\Carbon\Carbon::parse($fecha)->locale('es')->dayName);

        // Obtener horarios disponibles
        $horarios = $zona->horarios()
            ->activo()
            ->where('dia_semana', $diaSemana)
            ->get()
            ->filter(function($h) use ($fecha) {
                return $h->tieneCapacidad($fecha);
            })
            ->map(function($h) {
                return [
                    'id' => $h->id,
                    'nombre' => $h->nombre ?? ($h->hora_inicio . ' - ' . $h->hora_fin),
                    'hora_inicio' => $h->hora_inicio,
                    'hora_fin' => $h->hora_fin,
                    'capacidad_disponible' => $h->capacidad_pedidos,
                ];
            });

        return response()->json(['horarios' => $horarios->values()]);
    }
}
