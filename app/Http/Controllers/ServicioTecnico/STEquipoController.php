<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\STEquipo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class STEquipoController extends Controller
{
    /**
     * Endpoint AJAX para Select2: busca equipos.
     * Si se pasa cliente_id, filtra por cliente.
     */
    public function buscarAjax(Request $request)
    {
        $q         = trim((string) $request->input('q', ''));
        $clienteId = $request->input('cliente_id');
        $page      = max(1, (int) $request->input('page', 1));
        $per       = 20;

        $query = STEquipo::query()->where('activo', true);

        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('numero_serie', 'like', "%{$q}%")
                   ->orWhere('marca', 'like', "%{$q}%")
                   ->orWhere('modelo', 'like', "%{$q}%")
                   ->orWhere('tipo_equipo', 'like', "%{$q}%");
            });
        }

        $total = $query->count();
        $items = $query->orderBy('marca')->orderBy('modelo')
            ->skip(($page - 1) * $per)
            ->take($per)
            ->get(['id', 'tipo_equipo', 'marca', 'modelo', 'numero_serie']);

        $results = $items->map(function ($e) {
            $partes = array_filter([$e->marca, $e->modelo, $e->tipo_equipo]);
            $label  = implode(' ', $partes);
            if ($e->numero_serie) {
                $label .= ' — S/N: ' . $e->numero_serie;
            }
            return ['id' => $e->id, 'text' => $label];
        });

        return response()->json([
            'results'    => $results,
            'pagination' => ['more' => ($page * $per) < $total],
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = STEquipo::with('cliente')->select('st_equipos.*');

            // Aplicar filtros
            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->filled('tipo_equipo')) {
                $query->where('tipo_equipo', $request->tipo_equipo);
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('en_garantia')) {
                $query->where('en_garantia', $request->en_garantia);
            }

            return DataTables::of($query)
                ->addColumn('cliente', function ($equipo) {
                    return $equipo->cliente ? $equipo->cliente->nombre_completo_formateado : 'N/A';
                })
                ->addColumn('marca_modelo', function ($equipo) {
                    return ($equipo->marca ?? '') . ' ' . ($equipo->modelo ?? '');
                })
                ->addColumn('ip_mac', function ($equipo) {
                    $info = [];
                    if ($equipo->ip_address) $info[] = 'IP: ' . $equipo->ip_address;
                    if ($equipo->mac_address) $info[] = 'MAC: ' . $equipo->mac_address;
                    return implode('<br>', $info) ?: 'N/A';
                })
                ->addColumn('garantia', function ($equipo) {
                    if ($equipo->en_garantia) {
                        $html = '<span class="badge bg-success">Sí</span>';
                        if ($equipo->vencimiento_garantia) {
                            $html .= '<br><small class="text-muted">' . $equipo->vencimiento_garantia->format('d/m/Y') . '</small>';
                        }
                        return $html;
                    }
                    return '<span class="badge bg-secondary">No</span>';
                })
                ->editColumn('estado', function ($equipo) {
                    $badges = [
                        'operativo' => 'success',
                        'en_reparacion' => 'warning',
                        'fuera_servicio' => 'danger',
                        'en_bodega' => 'secondary'
                    ];
                    $color = $badges[$equipo->estado] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $equipo->estado)) . '</span>';
                })
                ->addColumn('action', function ($equipo) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('st.equipos.show', $equipo->id) . '" class="btn btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="' . route('st.equipos.edit', $equipo->id) . '" class="btn btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="' . route('st.equipos.destroy', $equipo->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-eliminar" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['ip_mac', 'garantia', 'estado', 'action'])
                ->make(true);
        }

        return view('servicio-tecnico.equipos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get();
        return view('servicio-tecnico.equipos.create', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_equipo' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'required|string|max:100|unique:st_equipos,numero_serie',
            'mac_address' => 'nullable|string|max:50',
            'ip_address' => 'nullable|ip',
            'especificaciones' => 'nullable|string',
            'fecha_compra' => 'nullable|date',
            'fecha_instalacion' => 'nullable|date',
            'en_garantia' => 'boolean',
            'vencimiento_garantia' => 'nullable|date',
            'ubicacion_instalacion' => 'nullable|string|max:255',
            'estado' => 'required|in:operativo,en_reparacion,fuera_servicio,en_bodega',
        ]);

        $validated['activo'] = true;

        STEquipo::create($validated);

        return redirect()->route('st.equipos.index')
            ->with('success', 'Equipo registrado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $equipo = STEquipo::with(['cliente', 'ordenesServicio.tecnico'])
            ->findOrFail($id);

        return view('servicio-tecnico.equipos.show', compact('equipo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $equipo = STEquipo::findOrFail($id);
        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get();

        return view('servicio-tecnico.equipos.create', compact('equipo', 'clientes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $equipo = STEquipo::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_equipo' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'required|string|max:100|unique:st_equipos,numero_serie,' . $id,
            'mac_address' => 'nullable|string|max:50',
            'ip_address' => 'nullable|ip',
            'especificaciones' => 'nullable|string',
            'fecha_compra' => 'nullable|date',
            'fecha_instalacion' => 'nullable|date',
            'en_garantia' => 'boolean',
            'vencimiento_garantia' => 'nullable|date',
            'ubicacion_instalacion' => 'nullable|string|max:255',
            'estado' => 'required|in:operativo,en_reparacion,fuera_servicio,en_bodega',
            'activo' => 'boolean',
        ]);

        $equipo->update($validated);

        return redirect()->route('st.equipos.index')
            ->with('success', 'Equipo actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $equipo = STEquipo::findOrFail($id);

        // Verificar si tiene órdenes de servicio asociadas
        if ($equipo->ordenesServicio()->count() > 0) {
            return redirect()->route('st.equipos.index')
                ->with('error', 'No se puede eliminar el equipo porque tiene órdenes de servicio asociadas');
        }

        $equipo->delete();

        return redirect()->route('st.equipos.index')
            ->with('success', 'Equipo eliminado exitosamente');
    }
}
