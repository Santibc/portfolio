<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Gasto;
use App\Models\Nomina;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpuestosController extends Controller
{
    /**
     * Resumen de impuestos: IVA (repercutido/soportado), IRPF y Seguridad Social.
     */
    public function resumen(Request $request): View
    {
        $anio = (int) $request->input('anio', now()->year);
        $trimestre = $request->input('trimestre'); // 1..4 o vacío = anual

        $ingresos = $this->filtrarPeriodo(Ingreso::query(), $anio, $trimestre)->get();
        $gastos = $this->filtrarPeriodo(Gasto::query(), $anio, $trimestre)->get();

        // IVA repercutido (en ingresos) y soportado (en gastos), desglosado por tipo
        $ivaRepercutido = $this->agruparIvaPorTipo($ingresos);
        $ivaSoportado = $this->agruparIvaPorTipo($gastos);

        $totalIvaRepercutido = round($ingresos->sum(fn($i) => (float) $i->iva_importe), 2);
        $totalIvaSoportado = round($gastos->sum(fn($g) => (float) $g->iva_importe), 2);
        $ivaLiquidar = round($totalIvaRepercutido - $totalIvaSoportado, 2);

        // IRPF: el que practicas a proveedores (gastos) y el que te practican clientes (retención en ingresos)
        $irpfGastos = round($gastos->sum(fn($g) => (float) ($g->irpf_importe ?? 0)), 2);
        $retencionIngresos = round($ingresos->sum(fn($i) => (float) ($i->retencion_importe ?? 0)), 2);

        // Bases imponibles (sin IVA)
        $baseIngresos = round($ingresos->sum(fn($i) => (float) $i->importe), 2);
        $baseGastos = round($gastos->sum(fn($g) => (float) $g->importe), 2);

        // Seguridad Social e IRPF de nóminas (módulo de RRHH)
        $nominasQuery = Nomina::where('anio', $anio);
        if (in_array((string) $trimestre, ['1', '2', '3', '4'], true)) {
            $mesesNom = ['1' => [1, 3], '2' => [4, 6], '3' => [7, 9], '4' => [10, 12]][(string) $trimestre];
            $nominasQuery->whereBetween('mes', $mesesNom);
        }
        $nominas = $nominasQuery->get();
        $seguridadSocial = round((float) $nominas->sum('ss_empresa') + (float) $nominas->sum('ss_trabajador'), 2);
        $irpfNominas = round((float) $nominas->sum('irpf'), 2);
        $costeEmpresaNominas = round((float) $nominas->sum('salario_bruto') + (float) $nominas->sum('ss_empresa'), 2);

        $anios = range(now()->year, now()->year - 5);

        return view('impuestos.resumen', compact(
            'anio', 'trimestre', 'anios',
            'ivaRepercutido', 'ivaSoportado',
            'totalIvaRepercutido', 'totalIvaSoportado', 'ivaLiquidar',
            'irpfGastos', 'retencionIngresos',
            'baseIngresos', 'baseGastos', 'seguridadSocial',
            'irpfNominas', 'costeEmpresaNominas'
        ));
    }

    /**
     * Filtrar una query por año y (opcionalmente) trimestre usando la columna fecha.
     */
    private function filtrarPeriodo($query, int $anio, $trimestre)
    {
        $query->whereYear('fecha', $anio);

        if (in_array((string) $trimestre, ['1', '2', '3', '4'], true)) {
            $meses = [
                '1' => [1, 3], '2' => [4, 6], '3' => [7, 9], '4' => [10, 12],
            ][(string) $trimestre];
            $query->whereMonth('fecha', '>=', $meses[0])
                  ->whereMonth('fecha', '<=', $meses[1]);
        }

        return $query;
    }

    /**
     * Agrupar el IVA por tipo (%), usando el desglose si existe o el IVA único como fallback.
     */
    private function agruparIvaPorTipo($registros): array
    {
        $res = [];
        foreach ($registros as $r) {
            $desglose = is_array($r->desglose_iva) && count($r->desglose_iva)
                ? $r->desglose_iva
                : [['base' => $r->importe, 'porcentaje' => $r->iva_porcentaje, 'importe' => $r->iva_importe]];

            foreach ($desglose as $d) {
                $pct = (string) (float) ($d['porcentaje'] ?? 0);
                if (!isset($res[$pct])) {
                    $res[$pct] = ['base' => 0, 'iva' => 0];
                }
                $res[$pct]['base'] += (float) ($d['base'] ?? 0);
                $res[$pct]['iva'] += (float) ($d['importe'] ?? 0);
            }
        }
        uksort($res, fn($a, $b) => (float) $a <=> (float) $b);
        return $res;
    }
}
