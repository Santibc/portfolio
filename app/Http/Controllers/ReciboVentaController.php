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
    public function __construct(private ReciboVentaService $recibos) {}

    public function show(Venta $venta): View
    {
        return view('caja.recibo', $this->recibos->deVenta($venta));
    }

    public function prueba(Request $request): View
    {
        return view('caja.recibo', $this->recibos->dePrueba($request->user()));
    }
}
