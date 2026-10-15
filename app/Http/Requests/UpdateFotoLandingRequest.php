<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFotoLandingRequest extends FormRequest
{
    /** Fotos de celular: hasta 8 MB; el servidor las reduce y convierte a WebP. */
    public const REGLA_IMAGEN = ['nullable', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:8192'];

    public function authorize(): bool
    {
        return true;
    }

    /** La página tiene un formulario por foto: cada uno con su bolsa de errores ("foto_hero"…). */
    protected function prepareForValidation(): void
    {
        $this->errorBag = self::bolsaErrores($this->route('foto')->slot->value);
    }

    public static function bolsaErrores(string $slot): string
    {
        return 'foto_'.$slot;
    }

    public function rules(): array
    {
        return [
            'imagen' => self::REGLA_IMAGEN,
            'alt'    => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'imagen' => 'foto',
            'alt'    => 'descripción de la foto',
        ];
    }
}
