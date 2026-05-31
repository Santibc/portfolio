<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoAhorroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trabajador_turno_id' => ['required', 'integer', 'exists:trabajadores_turno,id'],
            'monto' => ['required', 'integer', 'min:1', 'max:999999999'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
