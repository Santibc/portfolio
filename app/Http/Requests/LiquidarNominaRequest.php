<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TipoPeriodo;
use App\Models\Nomina;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class LiquidarNominaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::enum(TipoPeriodo::class)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'dias' => ['required', 'integer', 'min:1', 'max:31'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $existe = Nomina::whereDate('fecha_inicio', $this->input('fecha_inicio'))
                ->whereDate('fecha_fin', $this->input('fecha_fin'))
                ->exists();

            if ($existe) {
                $validator->errors()->add('fecha_inicio', 'Ya existe una nómina liquidada para ese período.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
        ];
    }
}
