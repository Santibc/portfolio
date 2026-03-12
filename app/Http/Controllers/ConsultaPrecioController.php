<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use App\Models\TablaPrecioServicio;
use Illuminate\Http\Request;

class ConsultaPrecioController extends Controller
{
    public function index()
    {
        $servicios = TablaPrecioServicio::getDistinctServicios();
        $calibres = ConfiguracionSistema::get('calibres_disponibles', []);

        return view('consulta-precios.index', compact('servicios', 'calibres'));
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'tipo_servicio' => 'required|string',
            'clave_calibre' => 'required|string',
            'largo' => 'required|numeric|min:0',
            'cantidad' => 'required|integer|min:1',
        ]);

        $resultado = TablaPrecioServicio::lookup(
            $request->tipo_servicio,
            $request->clave_calibre,
            $request->largo,
            $request->cantidad
        );

        if (!$resultado) {
            return response()->json([
                'encontrado' => false,
                'mensaje' => 'No se encontro un precio para los parametros seleccionados.',
            ]);
        }

        return response()->json([
            'encontrado' => true,
            'precio' => $resultado->precio,
            'precio_formato' => '$' . number_format($resultado->precio, 0, ',', '.'),
            'precio_minimo' => $resultado->precio_minimo,
            'precio_minimo_formato' => '$' . number_format($resultado->precio_minimo, 0, ',', '.'),
            'etiqueta_servicio' => $resultado->etiqueta_servicio,
            'clave_calibre' => $resultado->clave_calibre,
            'calibre_mm' => $resultado->calibre_mm,
            'largo_rango' => $resultado->largo_rango_min . '-' . ($resultado->largo_rango_max ?? '∞') . ' mm',
            'cantidad_rango' => $resultado->cantidad_rango_min . '-' . ($resultado->cantidad_rango_max ?? '∞') . ' uds',
        ]);
    }
}
