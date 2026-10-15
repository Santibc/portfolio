<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGaleriaLandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:80'],
            'imagen' => UpdateFotoLandingRequest::REGLA_IMAGEN,
            'orden'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function attributes(): array
    {
        return ['titulo' => 'descripción', 'imagen' => 'foto'];
    }

    /** @return array<string, mixed> */
    public function datos(): array
    {
        return collect($this->validated())->except(['imagen', 'quitar_imagen'])->all();
    }
}
