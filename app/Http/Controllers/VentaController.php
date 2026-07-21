<?php

namespace App\Http\Controllers;

use App\Exports\PlantillaVentasExport;
use App\Imports\VentasImport;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\ItemVenta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $u = auth()->user();
            if (!$u->hasRole('admin') && !$u->hasRole('vendedor')) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    protected function esAdmin(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    protected function esSoloVendedor(): bool
    {
        $u = auth()->user();
        return $u->hasRole('vendedor') && !$u->hasRole('admin');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Venta::with(['vendedor:id,name', 'almacen:id,codigo,nombre', 'cliente:id,nombre_contacto'])
                ->withCount('items')
                ->select('ventas.*');

            if ($this->esSoloVendedor()) {
                $query->where('user_id', auth()->id());
            } elseif ($request->filled('user_id')) {
                $query->where('user_id', (int) $request->input('user_id'));
            }
            if ($request->filled('almacen_id')) {
                $query->where('almacen_id', (int) $request->input('almacen_id'));
            }
            if ($request->filled('desde')) {
                $query->where('fecha', '>=', $request->input('desde'));
            }
            if ($request->filled('hasta')) {
                $query->where('fecha', '<=', $request->input('hasta'));
            }

            return DataTables::of($query)
                ->addColumn('vendedor', fn($v) => $v->vendedor?->name)
                ->addColumn('almacen', fn($v) => $v->almacen?->nombre)
                ->addColumn('cliente', fn($v) => $v->cliente?->nombre_contacto ?? '—')
                ->addColumn('items_count', fn($v) => $v->items_count > 0 ? $v->items_count : '—')
                ->addColumn('fecha_fmt', fn($v) => $v->fecha?->format('Y-m-d'))
                ->addColumn('monto_fmt', fn($v) => '$ ' . number_format((float) $v->monto, 0, ',', '.'))
                ->addColumn('action', function ($v) {
                    $url = route('ventas.form', $v->id);
                    return <<<HTML
<div class="d-flex justify-content-center gap-1">
  <a href="{$url}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>
HTML;
                })
                ->filterColumn('vendedor', function ($q, $keyword) {
                    $q->whereHas('vendedor', fn($qq) => $qq->where('name', 'like', "%{$keyword}%"));
                })
                ->filterColumn('almacen', function ($q, $keyword) {
                    $q->whereHas('almacen', fn($qq) => $qq->where('nombre', 'like', "%{$keyword}%"));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $esAdmin = $this->esAdmin();
        $vendedores = User::role('vendedor')->orderBy('name')->pluck('name', 'id');
        $almacenes = Almacen::activos()->orderBy('nombre')->pluck('nombre', 'id');

        return view('ventas.ventas_index', compact('vendedores', 'almacenes', 'esAdmin'));
    }

    public function form(Venta $venta = null)
    {
        $venta = $venta ?? new Venta();

        if ($venta->exists && $this->esSoloVendedor() && $venta->user_id !== auth()->id()) {
            abort(403, 'No puedes editar ventas de otros vendedores.');
        }

        if ($venta->exists) {
            $venta->load(['items', 'cliente', 'vendedor', 'almacen']);
        }

        $esAdmin = $this->esAdmin();

        if ($esAdmin) {
            $vendedores = User::role('vendedor')
                ->with('almacen:id,codigo,nombre')
                ->orderBy('name')
                ->get(['id', 'name', 'almacen_id']);
        } else {
            $vendedores = User::where('id', auth()->id())
                ->with('almacen:id,codigo,nombre')
                ->get(['id', 'name', 'almacen_id']);
            if (!$venta->exists) {
                $venta->user_id = auth()->id();
            }
        }

        $almacenes = Almacen::activos()->orderBy('nombre')->pluck('nombre', 'id');

        return view('ventas.ventas_form', compact('venta', 'vendedores', 'almacenes', 'esAdmin'));
    }

    public function buscarProductos(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $listaId = null;
        if ($request->filled('cliente_id')) {
            $listaId = Cliente::where('id', (int) $request->input('cliente_id'))->value('lista_precio_id');
        }

        $productos = Producto::where('activo', true)
            ->where(function ($qb) use ($q) {
                $qb->where('referencia', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get(['id', 'referencia', 'nombre', 'marca']);

        $resultado = $productos->map(function ($p) use ($listaId) {
            $precio = null;
            if ($listaId) {
                $precio = $p->getPrecioPorLista($listaId);
            }
            if ($precio === null) {
                $precio = (float) $p->precios()->where('activo', true)->orderBy('precio')->value('precio');
            }
            return [
                'id' => $p->id,
                'referencia' => $p->referencia,
                'nombre' => $p->nombre,
                'marca' => $p->marca,
                'precio_sugerido' => (float) ($precio ?? 0),
            ];
        });

        return response()->json($resultado);
    }

    public function guardar(Request $request)
    {
        $venta = $request->id
            ? Venta::findOrFail($request->id)
            : new Venta();

        if ($this->esSoloVendedor()) {
            if ($venta->exists && $venta->user_id !== auth()->id()) {
                abort(403, 'No puedes editar ventas de otros vendedores.');
            }
            $request->merge(['user_id' => auth()->id()]);
        }

        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'monto_manual' => ['nullable', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.producto_id' => ['required_with:items', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required_with:items', 'integer', 'min:1'],
            'items.*.precio_unitario' => ['required_with:items', 'numeric', 'min:0'],
        ];

        $data = $request->validate($rules, [
            'required' => 'Este campo es obligatorio.',
            'exists' => 'El valor seleccionado no es válido.',
            'before_or_equal' => 'La fecha no puede ser futura.',
            'min' => 'El valor debe ser mayor o igual a :min.',
        ]);

        $items = $data['items'] ?? [];
        if (empty($items) && (!isset($data['monto_manual']) || (float) $data['monto_manual'] <= 0)) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'Agrega al menos un producto o ingresa un monto manual mayor a 0.']);
        }

        if (empty($data['almacen_id'])) {
            $vendedor = User::find($data['user_id']);
            $data['almacen_id'] = $vendedor?->almacen_id;
        }

        DB::transaction(function () use ($venta, $data, $items, $request) {
            $venta->fill([
                'user_id' => $data['user_id'],
                'almacen_id' => $data['almacen_id'] ?? null,
                'cliente_id' => $data['cliente_id'] ?? null,
                'fecha' => $data['fecha'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            if (!$venta->exists) {
                $venta->created_by = auth()->id();
                $venta->monto = 0;
            }
            $venta->save();

            if ($request->boolean('reemplazar_items', true) && $venta->exists) {
                $venta->items()->delete();
            }

            foreach ($items as $it) {
                $producto = Producto::find($it['producto_id']);
                if (!$producto) {
                    continue;
                }
                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'variante_producto_id' => $it['variante_producto_id'] ?? null,
                    'cantidad' => (int) $it['cantidad'],
                    'precio_unitario' => (float) $it['precio_unitario'],
                    'referencia_producto' => $producto->referencia,
                    'nombre_producto' => $producto->nombre,
                    'info_variante' => $it['info_variante'] ?? null,
                ]);
            }

            if (empty($items) && isset($data['monto_manual']) && (float) $data['monto_manual'] > 0) {
                $venta->monto = (float) $data['monto_manual'];
                $venta->saveQuietly();
            } else {
                $venta->recalcularMonto();
            }
        });

        return redirect()->route('ventas')
            ->with('success', 'Venta guardada correctamente.');
    }

    public function importarForm()
    {
        if (!$this->esAdmin()) abort(403, 'Solo el administrador puede hacer carga masiva.');
        return view('ventas.ventas_importar');
    }

    public function importar(Request $request)
    {
        if (!$this->esAdmin()) abort(403, 'Solo el administrador puede hacer carga masiva.');

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $import = new VentasImport(auth()->id());

        try {
            Excel::import($import, $request->file('archivo'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Error procesando el archivo: ' . $e->getMessage());
        }

        return view('ventas.ventas_importar', [
            'procesadas' => $import->procesadas,
            'errores' => $import->errores,
        ]);
    }

    public function descargarPlantilla()
    {
        if (!$this->esAdmin()) abort(403);
        return Excel::download(new PlantillaVentasExport(), 'plantilla_ventas.xlsx');
    }
}
