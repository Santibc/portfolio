<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UpdateProductoLandingRequest extends StoreProductoLandingRequest
{
    public function rules(): array
    {
        return parent::rules() + ['quitar_imagen' => ['nullable', 'boolean']];
    }
}
