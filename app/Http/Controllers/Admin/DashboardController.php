<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Impuesto;
use App\Models\Incoterm;
use App\Models\Moneda;
use App\Models\Puerto;
use App\Models\TipoDescuento;
use App\Models\TipoPago;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'counts' => [
                'monedas' => Moneda::count(),
                'impuestos' => Impuesto::count(),
                'tipos_descuento' => TipoDescuento::count(),
                'incoterms' => Incoterm::count(),
                'puertos' => Puerto::count(),
                'tipos_pago' => TipoPago::count(),
            ],
        ]);
    }
}
