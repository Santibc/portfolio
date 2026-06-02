<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuDiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Lista de items ofrecidos ese día. Vacío = sin configurar (la caja muestra todos).
            'items' => ['array'],
            'items.*' => ['integer', 'distinct', 'exists:menu_items,id'],
        ];
    }
}
