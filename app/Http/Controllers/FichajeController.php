<?php

namespace App\Http\Controllers;

use App\Models\Fichaje;
use App\Models\Trabajador;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FichajeController extends Controller
{
    /**
     * Display a listing of fichajes.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $esTrabajador = $user->hasRole('Trabajador');
        $trabajadorActual = null;
        $fichajeHoy = null;

        $query = Fichaje::with(['trabajador', 'obra', 'validadoPor']);

        // Si es trabajador, solo ver sus propios fichajes
        if ($esTrabajador) {
            $trabajadorActual = Trabajador::where('user_id', $user->id)->first();

            if (!$trabajadorActual) {
                return redirect()->route('dashboard')
                    ->with('error', 'Tu cuenta no está vinculada a un trabajador. Contacta con administración.');
            }

            $query->where('trabajador_id', $trabajadorActual->id);

            // Verificar si tiene fichaje hoy (para mostrar botón de salida)
            $fichajeHoy = Fichaje::where('trabajador_id', $trabajadorActual->id)
                                 ->where('fecha', now()->toDateString())
                                 ->first();
        }

        // Filtros (solo para no-trabajadores o filtros básicos)
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Solo admin/encargados pueden filtrar por trabajador
        if (!$esTrabajador && $request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }

        if ($request->filled('validado')) {
            $query->where('validado', $request->validado === '1');
        }

        // Por defecto mostrar el mes actual
        if (!$request->filled('fecha_desde') && !$request->filled('fecha_hasta')) {
            $query->where('fecha', '>=', now()->startOfMonth())
                  ->where('fecha', '<=', now()->endOfMonth());
        }

        $fichajes = $query->orderBy('fecha', 'desc')
                          ->orderBy('hora_entrada', 'desc')
                          ->paginate(50);

        // Estadísticas del período (filtradas para trabajador)
        if ($esTrabajador && $trabajadorActual) {
            $statsQuery = Fichaje::where('trabajador_id', $trabajadorActual->id)
                                 ->where('fecha', '>=', now()->startOfMonth());
            $stats = [
                'total_fichajes' => $statsQuery->count(),
                'pendientes_validar' => (clone $statsQuery)->where('validado', false)->count(),
                'horas_totales' => (clone $statsQuery)->sum('horas_trabajadas'),
                'horas_extra' => (clone $statsQuery)->sum('horas_extra'),
            ];
        } else {
            $stats = [
                'total_fichajes' => Fichaje::where('fecha', '>=', now()->startOfMonth())->count(),
                'pendientes_validar' => Fichaje::pendientesValidar()->count(),
                'horas_totales' => Fichaje::whereNotNull('horas_trabajadas')
                                          ->where('fecha', '>=', now()->startOfMonth())
                                          ->sum('horas_trabajadas'),
                'horas_extra' => Fichaje::where('fecha', '>=', now()->startOfMonth())
                                        ->sum('horas_extra'),
            ];
        }

        $trabajadores = Trabajador::where('activo', true)
                                   ->orderBy('nombre')
                                   ->get();

        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->orderBy('nombre')
                     ->get();

        return view('fichajes.index', compact(
            'fichajes', 'trabajadores', 'obras', 'stats',
            'esTrabajador', 'trabajadorActual', 'fichajeHoy'
        ));
    }

    /**
     * Show the form for creating a new fichaje.
     */
    public function create()
    {
        $user = Auth::user();
        $esTrabajador = $user->hasRole('Trabajador');
        $trabajadorActual = null;

        // Si es trabajador, solo puede fichar para sí mismo
        if ($esTrabajador) {
            $trabajadorActual = Trabajador::where('user_id', $user->id)->first();

            if (!$trabajadorActual) {
                return redirect()->route('fichajes.index')
                    ->with('error', 'Tu cuenta de usuario no está vinculada a un trabajador. Contacta con administración.');
            }

            $trabajadores = collect([$trabajadorActual]);
        } else {
            $trabajadores = Trabajador::where('activo', true)
                                       ->orderBy('nombre')
                                       ->get();
        }

        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->orderBy('nombre')
                     ->get();

        return view('fichajes.create', compact('trabajadores', 'obras', 'esTrabajador', 'trabajadorActual'));
    }

    /**
     * Store a newly created fichaje.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'obra_id' => 'nullable|exists:obras,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'latitud_entrada' => 'nullable|numeric',
            'longitud_entrada' => 'nullable|numeric',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Verificar que no exista un fichaje para el mismo trabajador y fecha
        $existe = Fichaje::where('trabajador_id', $validated['trabajador_id'])
                         ->where('fecha', $validated['fecha'])
                         ->exists();

        if ($existe) {
            return back()->withErrors(['fecha' => 'Ya existe un fichaje para este trabajador en esta fecha.'])
                         ->withInput();
        }

        // Calcular horas trabajadas si hay entrada y salida
        $horasTrabajadas = null;
        $horasExtra = 0;

        if (!empty($validated['hora_entrada']) && !empty($validated['hora_salida'])) {
            $entrada = Carbon::createFromFormat('H:i', $validated['hora_entrada']);
            $salida = Carbon::createFromFormat('H:i', $validated['hora_salida']);
            $horasTrabajadas = $salida->diffInMinutes($entrada) / 60;

            // Si trabaja más de 8 horas, el resto son extras
            if ($horasTrabajadas > 8) {
                $horasExtra = $horasTrabajadas - 8;
                $horasTrabajadas = 8;
            }
        }

        $fichaje = Fichaje::create([
            'trabajador_id' => $validated['trabajador_id'],
            'obra_id' => $validated['obra_id'] ?? null,
            'fecha' => $validated['fecha'],
            'hora_entrada' => $validated['hora_entrada'] ?? null,
            'hora_salida' => $validated['hora_salida'] ?? null,
            'latitud_entrada' => $validated['latitud_entrada'] ?? null,
            'longitud_entrada' => $validated['longitud_entrada'] ?? null,
            'horas_trabajadas' => $horasTrabajadas,
            'horas_extra' => $horasExtra,
            'notas' => $validated['notas'] ?? null,
        ]);

        return redirect()->route('fichajes.index')
                         ->with('success', 'Fichaje registrado correctamente.');
    }

    /**
     * Display the specified fichaje.
     */
    public function show(Fichaje $fichaje)
    {
        $fichaje->load(['trabajador', 'obra', 'validadoPor', 'corregidoPor']);

        return view('fichajes.show', compact('fichaje'));
    }

    /**
     * Show the form for editing the specified fichaje.
     */
    public function edit(Fichaje $fichaje)
    {
        $trabajadores = Trabajador::where('activo', true)
                                   ->orderBy('nombre')
                                   ->get();

        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->orderBy('nombre')
                     ->get();

        return view('fichajes.edit', compact('fichaje', 'trabajadores', 'obras'));
    }

    /**
     * Update the specified fichaje.
     */
    public function update(Request $request, Fichaje $fichaje)
    {
        $validated = $request->validate([
            'obra_id' => 'nullable|exists:obras,id',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'motivo_correccion' => 'required_if:corregido,true|nullable|string|max:500',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Calcular horas trabajadas si hay entrada y salida
        $horasTrabajadas = null;
        $horasExtra = 0;

        if (!empty($validated['hora_entrada']) && !empty($validated['hora_salida'])) {
            $entrada = Carbon::createFromFormat('H:i', $validated['hora_entrada']);
            $salida = Carbon::createFromFormat('H:i', $validated['hora_salida']);
            $horasTrabajadas = $salida->diffInMinutes($entrada) / 60;

            if ($horasTrabajadas > 8) {
                $horasExtra = $horasTrabajadas - 8;
                $horasTrabajadas = 8;
            }
        }

        // Detectar si hay corrección
        $esCorreccion = $fichaje->hora_entrada != $validated['hora_entrada'] ||
                        $fichaje->hora_salida != $validated['hora_salida'];

        $fichaje->update([
            'obra_id' => $validated['obra_id'] ?? null,
            'hora_entrada' => $validated['hora_entrada'] ?? null,
            'hora_salida' => $validated['hora_salida'] ?? null,
            'horas_trabajadas' => $horasTrabajadas,
            'horas_extra' => $horasExtra,
            'notas' => $validated['notas'] ?? null,
            'corregido' => $esCorreccion ? true : $fichaje->corregido,
            'corregido_por' => $esCorreccion ? Auth::id() : $fichaje->corregido_por,
            'motivo_correccion' => $esCorreccion ? $validated['motivo_correccion'] : $fichaje->motivo_correccion,
        ]);

        return redirect()->route('fichajes.index')
                         ->with('success', 'Fichaje actualizado correctamente.');
    }

    /**
     * Remove the specified fichaje.
     */
    public function destroy(Fichaje $fichaje)
    {
        $fichaje->delete();

        return redirect()->route('fichajes.index')
                         ->with('success', 'Fichaje eliminado correctamente.');
    }

    /**
     * Validar un fichaje.
     */
    public function validar(Fichaje $fichaje)
    {
        if ($fichaje->validado) {
            return back()->with('error', 'Este fichaje ya está validado.');
        }

        $fichaje->update([
            'validado' => true,
            'validado_por' => Auth::id(),
            'fecha_validacion' => now(),
        ]);

        return back()->with('success', 'Fichaje validado correctamente.');
    }

    /**
     * Validar múltiples fichajes.
     */
    public function validarMultiple(Request $request)
    {
        $validated = $request->validate([
            'fichajes' => 'required|array',
            'fichajes.*' => 'exists:fichajes,id',
        ], [
            'fichajes.required' => 'Debe seleccionar al menos un fichaje para validar.',
            'fichajes.array' => 'Selección de fichajes inválida.',
        ]);

        $count = Fichaje::whereIn('id', $validated['fichajes'])
               ->where('validado', false)
               ->update([
                   'validado' => true,
                   'validado_por' => Auth::id(),
                   'fecha_validacion' => now(),
               ]);

        return back()->with('success', $count . ' fichaje(s) validado(s) correctamente.');
    }

    /**
     * Registrar entrada (check-in).
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'obra_id' => 'nullable|exists:obras,id',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $hoy = now()->toDateString();

        // Verificar si ya hay fichaje hoy
        $fichaje = Fichaje::where('trabajador_id', $validated['trabajador_id'])
                          ->where('fecha', $hoy)
                          ->first();

        if ($fichaje && $fichaje->hora_entrada) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has fichado entrada hoy.',
            ], 400);
        }

        if (!$fichaje) {
            $fichaje = Fichaje::create([
                'trabajador_id' => $validated['trabajador_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'fecha' => $hoy,
                'hora_entrada' => now()->format('H:i'),
                'latitud_entrada' => $validated['latitud'] ?? null,
                'longitud_entrada' => $validated['longitud'] ?? null,
            ]);
        } else {
            $fichaje->update([
                'hora_entrada' => now()->format('H:i'),
                'latitud_entrada' => $validated['latitud'] ?? null,
                'longitud_entrada' => $validated['longitud'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrada registrada correctamente.',
            'fichaje' => $fichaje,
        ]);
    }

    /**
     * Registrar salida (check-out).
     */
    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $hoy = now()->toDateString();

        $fichaje = Fichaje::where('trabajador_id', $validated['trabajador_id'])
                          ->where('fecha', $hoy)
                          ->first();

        if (!$fichaje || !$fichaje->hora_entrada) {
            return response()->json([
                'success' => false,
                'message' => 'No has fichado entrada hoy.',
            ], 400);
        }

        if ($fichaje->hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has fichado salida hoy.',
            ], 400);
        }

        // Calcular horas
        $entrada = Carbon::parse($fichaje->hora_entrada);
        $salida = now();
        $horasTrabajadas = $salida->diffInMinutes($entrada) / 60;
        $horasExtra = 0;

        if ($horasTrabajadas > 8) {
            $horasExtra = $horasTrabajadas - 8;
            $horasTrabajadas = 8;
        }

        $fichaje->update([
            'hora_salida' => $salida->format('H:i'),
            'latitud_salida' => $validated['latitud'] ?? null,
            'longitud_salida' => $validated['longitud'] ?? null,
            'horas_trabajadas' => round($horasTrabajadas, 2),
            'horas_extra' => round($horasExtra, 2),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salida registrada correctamente.',
            'fichaje' => $fichaje,
        ]);
    }

    /**
     * Resumen de fichajes por trabajador.
     */
    public function resumen(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);

        $trabajadores = Trabajador::where('activo', true)
            ->with(['fichajes' => function($query) use ($mes, $anio) {
                $query->whereMonth('fecha', $mes)
                      ->whereYear('fecha', $anio);
            }])
            ->get()
            ->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'nombre' => $trabajador->nombre_completo,
                    'dias_trabajados' => $trabajador->fichajes->count(),
                    'horas_trabajadas' => $trabajador->fichajes->sum('horas_trabajadas'),
                    'horas_extra' => $trabajador->fichajes->sum('horas_extra'),
                    'fichajes_pendientes' => $trabajador->fichajes->where('validado', false)->count(),
                ];
            });

        return view('fichajes.resumen', compact('trabajadores', 'mes', 'anio'));
    }
}
