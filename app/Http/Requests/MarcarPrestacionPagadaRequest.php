<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarcarPrestacionPagadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'fecha_pago' => ['required', 'date'],
        ];
    }
}
