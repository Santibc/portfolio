<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\StockProducto;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockPdvController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = StockProducto::with(['producto', 'variante', 'ubicacionRelacion'])
                ->whereHas('producto', function ($q) {
                    $q->where('eliminado', false)->where('activo', true);
                })
                ->select('stock_productos.*');

            if ($request->filled('ubicacion_id')) {
                $query->where('ubicacion_id', $request->ubicacion_id);
            }

            return DataTables::of($query)
                ->addColumn('producto_nombre', function ($row) {
                    $ref = $row->producto->referencia ?? '';
                    $nombre = $row->producto->nombre ?? '';
                    return "<strong>{$ref}</strong> {$nombre}";
                })
                ->addColumn('variante_nombre', function ($row) {
                    if (!$row->variante) return '<span class="text-muted">-</span>';
                    $nombre = $row->variante->nombre_variante ?? '';
                    $sku = $row->variante->sku ?? '';
                    return $nombre . ($sku ? " <small class='text-muted'>({$sku})</small>" : '');
                })
                ->addColumn('ubicacion_nombre', function ($row) {
                    return $row->ubicacionRelacion->nombre ?? '<span class="text-muted">Sin ubicación</span>';
                })
                ->addColumn('stock_display', function ($row) {
                    $stock = $row->cantidad_disponible - $row->cantidad_reservada;
                    if ($stock <= 0) {
                        return '<span class="badge bg-danger">' . $stock . '</span>';
                    }
                    if ($row->alerta_stock_bajo && $stock <= $row->stock_minimo) {
                        return '<span class="badge bg-warning text-dark">' . $stock . '</span>';
                    }
                    return '<span class="badge bg-success">' . $stock . '</span>';
                })
                ->filterColumn('producto_nombre', function ($query, $keyword) {
                    $query->whereHas('producto', function ($q) use ($keyword) {
                        $q->where('referencia', 'like', "%{$keyword}%")
                          ->orWhere('nombre', 'like', "%{$keyword}%");
                    })->orWhereHas('variante', function ($q) use ($keyword) {
                        $q->where('nombre_variante', 'like', "%{$keyword}%")
                          ->orWhere('sku', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['producto_nombre', 'variante_nombre', 'ubicacion_nombre', 'stock_display'])
                ->make(true);
        }

        $ubicaciones = Ubicacion::activas()->get();

        return view('pdv.stock.index', compact('ubicaciones'));
    }
}
