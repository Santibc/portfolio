<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNegocioLandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Teléfono y WhatsApp se guardan solo con dígitos (se aceptan espacios o guiones al escribir). */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefono' => preg_replace('/\D+/', '', (string) $this->input('telefono')),
            'whatsapp' => preg_replace('/\D+/', '', (string) $this->input('whatsapp')),
            'horarios' => array_values((array) $this->input('horarios', [])),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'            => ['required', 'string', 'max:80'],
            'nombre_largo'      => ['required', 'string', 'max:120'],
            'lema'              => ['required', 'string', 'max:60'],
            'propietaria'       => ['nullable', 'string', 'max:80'],
            'propietaria_cargo' => ['nullable', 'string', 'max:40'],
            'telefono'          => ['required', 'digits_between:7,15'],
            'whatsapp'          => ['required', 'digits_between:10,15'],
            'whatsapp_mensaje'  => ['required', 'string', 'max:200'],
            'direccion'         => ['required', 'string', 'max:120'],
            'barrio'            => ['nullable', 'string', 'max:60'],
            'ciudad'            => ['required', 'string', 'max:60'],
            'instagram_url'     => ['nullable', 'url', 'max:255'],
            'facebook_url'      => ['nullable', 'url', 'max:255'],
            'tiktok_url'        => ['nullable', 'url', 'max:255'],
            'horarios'          => ['array', 'max:7'],
            'horarios.*.dias'   => ['required', 'string', 'max:60'],
            'horarios.*.horas'  => ['required', 'string', 'max:60'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_largo'      => 'nombre completo',
            'propietaria_cargo' => 'cargo',
            'whatsapp_mensaje'  => 'mensaje de WhatsApp',
            'direccion'         => 'dirección',
            'instagram_url'     => 'Instagram',
            'facebook_url'      => 'Facebook',
            'tiktok_url'        => 'TikTok',
            'horarios.*.dias'   => 'días',
            'horarios.*.horas'  => 'horas',
        ];
    }

    /** @return array<string, mixed> datos de landing_ajustes (sin el horario) */
    public function datosNegocio(): array
    {
        return collect($this->validated())->except('horarios')->all();
    }

    /** @return list<array{dias: string, horas: string}> */
    public function horarios(): array
    {
        return $this->validated()['horarios'] ?? [];
    }
}
