<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\MenuItem;
use App\Models\MetodoPago;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.menu_item_id'   => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.cantidad'       => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.precio_unitario' => ['nullable', 'integer', 'min:0', 'max:99999999'],

            'pagos'                  => ['required', 'array', 'min:1'],
            'pagos.*.metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'pagos.*.monto'          => ['required', 'integer', 'min:1', 'max:999999999'],
            'pagos.*.referencia'     => ['nullable', 'string', 'max:100'],

            'notas'                  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $items = $this->input('items', []);
            $pagos = $this->input('pagos', []);

            $itemIds = collect($items)->pluck('menu_item_id')->unique()->all();
            $menus   = MenuItem::whereIn('id', $itemIds)->pluck('precio', 'id');
            $total   = 0;
            foreach ($items as $row) {
                // El precio puede editarse en el carrito; usa el enviado y, si no llega,
                // el de catálogo. Debe coincidir con VentaService::calcularItems().
                $precio = array_key_exists('precio_unitario', $row) && $row['precio_unitario'] !== ''
                    ? (int) $row['precio_unitario']
                    : (int) ($menus[$row['menu_item_id']] ?? 0);
                $total += $precio * (int) $row['cantidad'];
            }

            $metodos         = MetodoPago::whereIn('id', collect($pagos)->pluck('metodo_pago_id')->unique()->all())->get()->keyBy('id');
            $sumNoEfectivo   = 0;
            $sumEfectivoPago = 0;
            foreach ($pagos as $row) {
                $m = $metodos->get($row['metodo_pago_id']);
                if (! $m) {
                    continue;
                }
                if ($m->es_efectivo) {
                    $sumEfectivoPago += (int) $row['monto'];
                } else {
                    $sumNoEfectivo += (int) $row['monto'];
                }
            }

            if ($total <= 0) {
                $v->errors()->add('items', 'El total de la venta debe ser mayor a cero.');

                return;
            }

            if ($sumNoEfectivo > $total) {
                $v->errors()->add('pagos', 'Los pagos por transferencia superan el total de la venta.');
            }

            $efectivoRequerido = max(0, $total - $sumNoEfectivo);
            if ($sumEfectivoPago < $efectivoRequerido) {
                $faltante = $efectivoRequerido - $sumEfectivoPago;
                $v->errors()->add(
                    'pagos',
                    'Falta efectivo: $ ' . number_format($faltante, 0, ',', '.') . ' para cubrir el total.',
                );
            }
        });
    }
}
