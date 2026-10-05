<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

/** Reglas de los componentes de inventario de un item de menú (crear y editar comparten). */
trait ValidaComponentesInventario
{
    protected function reglasComponentes(): array
    {
        return [
            'componentes'               => ['nullable', 'array', 'max:10'],
            'componentes.*.nombre'      => ['required', 'string', 'max:100'],
            'componentes.*.cantidad'    => ['required', 'numeric', 'min:0.01', 'max:9999', 'decimal:0,2'],
            'componentes.*.productos'   => ['required', 'array', 'min:1'],
            'componentes.*.productos.*' => [
                'integer',
                Rule::exists('productos_mercado', 'id')
                    ->where('controla_inventario', true)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    protected function mensajesComponentes(): array
    {
        return [
            'componentes.*.nombre.required'    => 'Cada componente necesita un nombre (ej. "Sabor de Doritos").',
            'componentes.*.cantidad.min'       => 'La cantidad de cada componente debe ser mayor a 0.',
            'componentes.*.productos.required' => 'Elige al menos un producto de inventario para cada componente.',
            'componentes.*.productos.*.exists' => 'Solo se pueden usar productos de mercado que controlan inventario.',
        ];
    }
}
