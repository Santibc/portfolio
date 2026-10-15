<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IconoLanding;
use App\Enums\TextoLanding;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTextosLandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cifras' => array_values((array) $this->input('cifras', [])),
            'valores' => array_values((array) $this->input('valores', [])),
        ]);
    }

    public function rules(): array
    {
        $reglas = [
            'textos'          => ['required', 'array'],
            'cifras'          => ['array', 'max:4'],
            'cifras.*.valor'  => ['required', 'integer', 'min:0', 'max:999999'],
            'cifras.*.prefijo' => ['nullable', 'string', 'max:5'],
            'cifras.*.sufijo' => ['nullable', 'string', 'max:10'],
            'cifras.*.texto'  => ['required', 'string', 'max:80'],
            'valores'         => ['array', 'max:8'],
            'valores.*.icono' => ['required', new Enum(IconoLanding::class)],
            'valores.*.titulo' => ['required', 'string', 'max:40'],
            'valores.*.texto' => ['required', 'string', 'max:200'],
        ];

        foreach (TextoLanding::cases() as $clave) {
            $reglas['textos.'.$clave->value] = ['required', 'string', 'max:'.$clave->maximo()];
        }

        return $reglas;
    }

    public function attributes(): array
    {
        $nombres = [
            'cifras.*.valor'   => 'número',
            'cifras.*.texto'   => 'texto de la cifra',
            'valores.*.icono'  => 'icono',
            'valores.*.titulo' => 'título',
            'valores.*.texto'  => 'texto',
        ];

        foreach (TextoLanding::cases() as $clave) {
            $nombres['textos.'.$clave->value] = $clave->seccion().' › '.mb_strtolower($clave->etiqueta());
        }

        return $nombres;
    }

    /** @return array<string, string> */
    public function textos(): array
    {
        return $this->validated()['textos'];
    }

    /** @return list<array<string, mixed>> */
    public function cifras(): array
    {
        return $this->validated()['cifras'] ?? [];
    }

    /** @return list<array<string, mixed>> */
    public function valores(): array
    {
        return $this->validated()['valores'] ?? [];
    }
}
