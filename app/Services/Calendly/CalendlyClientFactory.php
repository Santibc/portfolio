<?php

namespace App\Services\Calendly;

use App\Services\Contracts\ApiClientFactoryInterface;
use App\Services\Contracts\ApiClientInterface;

// Ya no necesitamos estas clases aquí
// use App\Models\User;
// use InvalidArgumentException;

class CalendlyClientFactory implements ApiClientFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createData(string $baseUrl, string $token): ApiClientInterface
    {
        // La lógica de validación de si existen las credenciales
        // se mueve a donde se obtienen (el controlador),
        // que es donde debe estar.
        return new ApiCalendly(
            baseUrl: $baseUrl,
            token: $token
        );
    }
}