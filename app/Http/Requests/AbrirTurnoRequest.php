<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbrirTurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'base_inicial' => ['required', 'integer', 'min:0', 'max:999999999'],
            'notas'        => ['nullable', 'string', 'max:500'],
        ];
    }
}
