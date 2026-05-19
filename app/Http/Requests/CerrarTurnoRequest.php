<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CerrarTurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_declarado' => ['required', 'integer', 'min:0', 'max:999999999'],
            'notas'           => ['nullable', 'string', 'max:500'],
        ];
    }
}
