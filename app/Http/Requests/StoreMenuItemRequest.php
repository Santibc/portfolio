<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'  => ['required', 'string', 'max:255'],
            'precio'  => ['required', 'integer', 'min:1', 'max:999999999'],
            'tipo_id' => ['required', 'integer', 'exists:tipos_menu_item,id'],
            'imagen'  => ['nullable', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'activo'  => ['nullable', 'boolean'],
            'orden'   => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
