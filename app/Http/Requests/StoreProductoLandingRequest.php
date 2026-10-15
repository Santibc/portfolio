<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoLandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'      => ['required', 'string', 'max:60'],
            'descripcion' => ['required', 'string', 'max:200'],
            'imagen'      => UpdateFotoLandingRequest::REGLA_IMAGEN,
            'orden'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['titulo' => 'título', 'descripcion' => 'descripción', 'imagen' => 'foto'];
    }

    /** @return array<string, mixed> */
    public function datos(): array
    {
        return collect($this->validated())->except(['imagen', 'quitar_imagen'])
            ->put('activo', $this->boolean('activo'))
            ->all();
    }
}
