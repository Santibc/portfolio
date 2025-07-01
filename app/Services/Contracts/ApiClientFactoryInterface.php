<?php

namespace App\Services\Contracts;

interface ApiClientFactoryInterface
{
    /**
     * Crea una nueva instancia del cliente de API con credenciales específicas.
     *
     * @param string $baseUrl
     * @param string $token
     * @return ApiClientInterface
     */
    public function createData(string $baseUrl, string $token): ApiClientInterface;
}