<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\STRepuesto;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class STRepuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = STRepuesto::query();

            // Aplicar filtros
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->filled('buscar')) {
                $query->where(function($q) use ($request) {
                    $q->where('codigo', 'like', '%' . $request->buscar . '%')
                      ->orWhere('nombre', 'like', '%' . $request->buscar . '%');
                });
            }

            if ($request->filled('stock_bajo')) {
                if ($request->stock_bajo == '1') {
                    $query->whereRaw('stock_actual <= stock_minimo');
                } else {
                    $query->whereRaw('stock_actual > stock_minimo');
                }
            }

            return DataTables::of($query)
                ->addColumn('marca_modelo', function ($repuesto) {
                    $info = [];
                    if ($repuesto->marca) $info[] = $repuesto->marca;
                    if ($repuesto->modelo_compatible) $info[] = $repuesto->modelo_compatible;
                    return implode(' - ', $info) ?: 'Universal';
                })
                ->editColumn('stock_actual', function ($repuesto) {
                    if ($repuesto->stock_actual <= $repuesto->stock_minimo) {
                        return '<span class="badge bg-danger">' . $repuesto->stock_actual . '</span>';
                    } elseif ($repuesto->stock_actual <= ($repuesto->stock_minimo * 1.5)) {
                        return '<span class="badge bg-warning text-dark">' . $repuesto->stock_actual . '</span>';
                    }
                    return '<span class="badge bg-success">' . $repuesto->stock_actual . '</span>';
                })
                ->editColumn('precio_compra', function ($repuesto) {
                    return '$' . number_format($repuesto->precio_costo ?? 0, 0, ',', '.');
                })
                ->addColumn('estado', function ($repuesto) {
                    if ($repuesto->activo) {
                        return '<span class="badge bg-success">Activo</span>';
                    }
                    return '<span class="badge bg-secondary">Inactivo</span>';
                })
                ->addColumn('action', function ($repuesto) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('st.repuestos.edit', $repuesto->id) . '" class="btn btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="' . route('st.repuestos.destroy', $repuesto->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-eliminar" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->filterColumn('marca_modelo', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('marca', 'like', "%{$keyword}%")
                          ->orWhere('modelo_compatible', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['stock_actual', 'estado', 'action'])
                ->make(true);
        }

        // Estadísticas
        $stats = [
            'total' => STRepuesto::count(),
            'en_stock' => STRepuesto::where('stock_actual', '>', 0)->count(),
            'stock_bajo' => STRepuesto::whereRaw('stock_actual <= stock_minimo')->count(),
            'sin_stock' => STRepuesto::where('stock_actual', 0)->count(),
        ];

        return view('servicio-tecnico.repuestos.index', compact('stats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servicio-tecnico.repuestos.create');
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
            'codigo' => 'required|string|max:50|unique:st_repuestos,codigo',
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo_compatible' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'precio_costo' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $validated['activo'] = true;

        STRepuesto::create($validated);

        return redirect()->route('st.repuestos.index')
            ->with('success', 'Repuesto registrado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $repuesto = STRepuesto::with('repuestosUsados.ordenServicio')->findOrFail($id);
        return view('servicio-tecnico.repuestos.show', compact('repuesto'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $repuesto = STRepuesto::findOrFail($id);
        return view('servicio-tecnico.repuestos.create', compact('repuesto'));
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
        $repuesto = STRepuesto::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:st_repuestos,codigo,' . $id,
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo_compatible' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'precio_costo' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $repuesto->update($validated);

        return redirect()->route('st.repuestos.index')
            ->with('success', 'Repuesto actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $repuesto = STRepuesto::findOrFail($id);

        // Verificar si ha sido usado en órdenes de servicio
        if ($repuesto->repuestosUsados()->count() > 0) {
            return redirect()->route('st.repuestos.index')
                ->with('error', 'No se puede eliminar el repuesto porque ha sido usado en órdenes de servicio');
        }

        $repuesto->delete();

        return redirect()->route('st.repuestos.index')
            ->with('success', 'Repuesto eliminado exitosamente');
    }
}
