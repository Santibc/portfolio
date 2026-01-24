<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::withCount(['obras', 'facturas', 'leads']);

        // Filtro por búsqueda (nombre, razón social, CIF)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_comercial', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%")
                  ->orWhere('persona_contacto', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por estado
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        // Filtro por provincia
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        $clientes = $query->orderBy('nombre_comercial')->get();

        // Obtener provincias únicas para el filtro
        $provincias = Cliente::whereNotNull('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia');

        // Estadísticas
        $stats = [
            'total' => Cliente::count(),
            'activos' => Cliente::where('activo', true)->count(),
            'publicos' => Cliente::where('tipo', 'publico')->count(),
            'privados' => Cliente::where('tipo', 'privado')->count(),
        ];

        return view('clientes.index', compact('clientes', 'provincias', 'stats'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:publico,privado',
            'nombre_comercial' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cif' => 'nullable|string|max:20|unique:clientes',
            'direccion' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:10',
            'ciudad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'persona_contacto' => 'nullable|string|max:150',
            'telefono_contacto' => 'nullable|string|max:20',
            'email_contacto' => 'nullable|email|max:255',
            'condiciones_pago' => 'nullable|string|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
        ], [
            'tipo.required' => 'El tipo de cliente es obligatorio.',
            'tipo.in' => 'El tipo de cliente debe ser público o privado.',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
            'cif.unique' => 'Este CIF ya está registrado.',
            'email.email' => 'El email no tiene un formato válido.',
            'email_contacto.email' => 'El email de contacto no tiene un formato válido.',
        ]);

        $validated['activo'] = true;
        $validated['pais'] = $validated['pais'] ?? 'España';

        $cliente = Cliente::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'clientes', $cliente->id, null, $cliente->toArray());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'obras' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'facturas' => function ($query) {
                $query->orderBy('fecha_emision', 'desc')->limit(10);
            },
            'leads' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'contratos' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'interacciones' => function ($query) {
                $query->orderBy('fecha', 'desc')->limit(10);
            },
        ]);

        // Estadísticas del cliente
        $stats = [
            'total_obras' => $cliente->obras()->count(),
            'obras_activas' => $cliente->obras()->whereIn('estado', ['en_curso', 'aprobada'])->count(),
            'total_facturas' => $cliente->facturas()->count(),
            'facturas_pendientes' => $cliente->facturas()->where('estado', '!=', 'cobrada')->count(),
            'importe_facturado' => $cliente->facturas()->sum('total'),
            'importe_pendiente' => $cliente->facturas()->where('estado', '!=', 'cobrada')->sum('total'),
        ];

        return view('clientes.show', compact('cliente', 'stats'));
    }

    public function edit(Request $request, Cliente $cliente)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $cliente->id,
                'tipo' => $cliente->tipo,
                'nombre_comercial' => $cliente->nombre_comercial,
                'razon_social' => $cliente->razon_social,
                'cif' => $cliente->cif,
                'direccion' => $cliente->direccion,
                'codigo_postal' => $cliente->codigo_postal,
                'ciudad' => $cliente->ciudad,
                'provincia' => $cliente->provincia,
                'pais' => $cliente->pais,
                'telefono' => $cliente->telefono,
                'email' => $cliente->email,
                'persona_contacto' => $cliente->persona_contacto,
                'telefono_contacto' => $cliente->telefono_contacto,
                'email_contacto' => $cliente->email_contacto,
                'condiciones_pago' => $cliente->condiciones_pago,
                'retencion_porcentaje' => $cliente->retencion_porcentaje,
                'notas' => $cliente->notas,
                'activo' => $cliente->activo,
            ]);
        }

        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:publico,privado',
            'nombre_comercial' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cif' => 'nullable|string|max:20|unique:clientes,cif,' . $cliente->id,
            'direccion' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:10',
            'ciudad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'persona_contacto' => 'nullable|string|max:150',
            'telefono_contacto' => 'nullable|string|max:20',
            'email_contacto' => 'nullable|email|max:255',
            'condiciones_pago' => 'nullable|string|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'tipo.required' => 'El tipo de cliente es obligatorio.',
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
            'cif.unique' => 'Este CIF ya está registrado.',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $cliente->toArray();

        $cliente->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'clientes', $cliente->id, $datosAnteriores, $cliente->fresh()->toArray());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        // Verificar que no tiene obras activas
        if ($cliente->obras()->whereIn('estado', ['en_curso', 'aprobada'])->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar un cliente con obras activas.');
        }

        // Verificar que no tiene facturas pendientes
        if ($cliente->facturas()->where('estado', '!=', 'cobrada')->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar un cliente con facturas pendientes.');
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'clientes', $cliente->id, $cliente->toArray(), null);

        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    // =============================================
    // INTERACCIONES (CRM básico)
    // =============================================

    public function storeInteraccion(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:llamada,email,reunion,visita,otro',
            'fecha' => 'required|date',
            'descripcion' => 'required|string',
            'proximo_paso' => 'nullable|string',
            'fecha_proximo_contacto' => 'nullable|date|after_or_equal:today',
        ], [
            'tipo.required' => 'El tipo de interacción es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        $cliente->interacciones()->create([
            'tipo' => $validated['tipo'],
            'fecha' => $validated['fecha'],
            'descripcion' => $validated['descripcion'],
            'proximo_paso' => $validated['proximo_paso'],
            'fecha_proximo_contacto' => $validated['fecha_proximo_contacto'],
            'registrado_por' => auth()->id(),
        ]);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Interacción registrada exitosamente.');
    }
}
