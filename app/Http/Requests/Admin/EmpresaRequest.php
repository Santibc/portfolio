<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaRequest extends FormRequest
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
            'razon_social' => ['required', 'string', 'max:150'],
            'nit' => ['required', 'string', 'max:30'],
            'direccion' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email:rfc', 'max:120'],
            'sitio_web' => ['nullable', 'string', 'max:120'],
            'regimen_tributario' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],

            'dian_resolucion_clc' => ['nullable', 'string', 'max:1000'],
            'dian_resolucion_fv' => ['nullable', 'string', 'max:1000'],

            'banco_nombre' => ['required', 'string', 'max:80'],
            'banco_pais' => ['required', 'string', 'max:60'],
            'banco_direccion' => ['required', 'string', 'max:150'],
            'banco_titular' => ['required', 'string', 'max:120'],
            'banco_moneda' => ['required', 'string', 'max:60'],
            'banco_swift' => ['required', 'string', 'max:60', 'regex:/^[A-Z0-9, ]{8,60}$/'],
            'banco_numero_cuenta' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9\- ]+$/'],

            'contacto_nombre' => ['required', 'string', 'max:120'],
            'contacto_email' => ['required', 'email:rfc', 'max:120'],
            'contacto_telefono' => ['required', 'string', 'max:40'],

            'facturacion_prefijo' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facturacion_prefijo.regex' => 'El prefijo solo puede contener letras y números, sin espacios ni guiones.',
        ];
    }
}
