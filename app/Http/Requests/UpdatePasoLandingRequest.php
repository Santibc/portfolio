<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UpdatePasoLandingRequest extends StorePasoLandingRequest
{
    public function rules(): array
    {
        return parent::rules() + ['quitar_imagen' => ['nullable', 'boolean']];
    }
}
