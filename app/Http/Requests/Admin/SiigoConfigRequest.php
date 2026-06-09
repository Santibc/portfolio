<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SiigoConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Administrador') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'email:rfc', 'max:150'],
            'access_key' => ['nullable', 'string', 'max:500'],
            'partner_id' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9_\-]*$/'],
            'ambiente' => ['required', 'in:sandbox,produccion'],
            'activo' => ['sometimes', 'boolean'],
            'nit_emisor' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\-]+$/'],
            'tipo_documento_id' => ['nullable', 'integer', 'min:1'],
            'tipo_documento_export_id' => ['nullable', 'integer', 'min:1'],
            'seller_id' => ['nullable', 'integer', 'min:1'],
            'payment_type_id' => ['nullable', 'integer', 'min:1'],
            'tax_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }
}
