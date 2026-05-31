<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TipoGasto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreGastoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo'                => ['required', new Enum(TipoGasto::class)],
            'metodo_pago_id'      => ['required', 'integer', 'exists:metodos_pago,id'],
            'valor'               => ['required', 'integer', 'min:1', 'max:999999999'],
            'ahorro'              => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'observacion'         => [
                Rule::requiredIf(fn () => $this->input('tipo') === TipoGasto::General->value),
                'nullable',
                'string',
                'max:500',
            ],
            'trabajador_turno_id' => [
                Rule::requiredIf(fn () => $this->input('tipo') === TipoGasto::Turno->value),
                'nullable',
                'integer',
                'exists:trabajadores_turno,id',
            ],
        ];
    }
}
