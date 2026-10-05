<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RolUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol'      => ['required', new Enum(RolUsuario::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
