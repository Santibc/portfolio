<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UpdateGaleriaLandingRequest extends StoreGaleriaLandingRequest
{
    public function rules(): array
    {
        return parent::rules() + ['quitar_imagen' => ['nullable', 'boolean']];
    }
}
