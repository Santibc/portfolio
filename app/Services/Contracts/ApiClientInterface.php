<?php

namespace App\Services\Contracts;

interface ApiClientInterface
{
    /**
     * Envía una petición GET al endpoint especificado.
     *
     * @param string $endpoint
     * @param array $query
     * @return array
     */
    public function get(string $endpoint, array $query = []): array;

    /**
     * Envía una petición POST al endpoint especificado.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Envía una petición PUT al endpoint especificado.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function put(string $endpoint, array $data = []): array;
    
    /**
     * Envía una petición DELETE al endpoint especificado.
     *
     * @param string $endpoint
     * @return array
     */
    public function delete(string $endpoint): array;
}