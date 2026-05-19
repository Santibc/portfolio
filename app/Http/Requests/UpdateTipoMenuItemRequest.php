<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('tipo')?->id ?? $this->route('tipo');

        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('tipos_menu_item', 'nombre')->ignore($id)],
            'orden'  => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
