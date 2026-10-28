<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\ReciboVentaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Recibo de caja para la impresora térmica. La caja lo carga en un iframe oculto y lo imprime. */
class ReciboVentaController extends Controller
{
    /**
     * Ancho imprimible (mm) por ancho de rollo: el cabezal de 58 mm imprime 48 mm centrados y el
     * de 80 mm, 72 mm. Se deja 1 mm de holgura a cada lado para que nada quede cortado.
     */
    private const ANCHO_IMPRESION = [58 => 46, 80 => 70];

    public function __construct(private ReciboVentaService $recibos) {}

    public function show(Request $request, Venta $venta): View
    {
        return $this->vista($request, $this->recibos->deVenta($venta));
    }

    public function prueba(Request $request): View
    {
        return $this->vista($request, $this->recibos->dePrueba($request->user()));
    }

    /** @param array<string, mixed> $recibo */
    private function vista(Request $request, array $recibo): View
    {
        $papel = (int) $request->query('papel') === 80 ? 80 : 58;

        return view('caja.recibo', $recibo + [
            'papel' => $papel,
            'ancho' => self::ANCHO_IMPRESION[$papel],
        ]);
    }
}
