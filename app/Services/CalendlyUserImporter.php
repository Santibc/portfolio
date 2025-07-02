<?php
// app/Services/CalendlyUserImporter.php

namespace App\Services;

use App\Services\Contracts\ApiClientFactoryInterface;
use App\Services\Contracts\UserImporterInterface;
use App\Models\Parametros; // Se asume que el modelo Parametros está disponible aquí

class CalendlyUserImporter implements UserImporterInterface
{
    private ApiClientFactoryInterface $apiFactory;
    private string $urlOrganization;
    private string $tokenOrganization;
    private string $organizationId;

    public function __construct(ApiClientFactoryInterface $apiFactory)
    {
        $this->apiFactory = $apiFactory;

        // Obtener parámetros aquí. En una aplicación real, considera un servicio
        // de configuración dedicado o inyectar estos parámetros si son dinámicos por solicitud.
        // Usando firstOrFail() para asegurar que los parámetros existan, de lo contrario, lanzará una excepción.
        $this->urlOrganization = Parametros::where('nombre_parametro', 'url_organizacion')->firstOrFail()->valor_parametro;
        $this->tokenOrganization = Parametros::where('nombre_parametro', 'token_organizacion')->firstOrFail()->valor_parametro;
        $this->organizationId = Parametros::where('nombre_parametro', 'organizacion')->firstOrFail()->valor_parametro;
    }

    /**
     * Obtiene los datos del usuario de la API de Calendly.
     *
     * @return array El array 'collection' de la respuesta de la API de Calendly.
     */
    public function getUsersData(): array
    {
        $apiClient = $this->apiFactory->createData(
            $this->urlOrganization,
            $this->tokenOrganization
        );

        $response = $apiClient->get('organization_memberships', [
            'organization' => $this->organizationId
        ]);

        // Se asume que la estructura de la respuesta de la API de Calendly es consistente y contiene una clave 'collection'.
        return $response['collection'] ?? [];
    }
}