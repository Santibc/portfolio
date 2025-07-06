<?php

namespace App\Services;

use App\Services\Contracts\ApiClientFactoryInterface;
use App\Models\Parametros;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CalendlyEventImporter
{
    private ApiClientFactoryInterface $apiFactory;
    private string $tokenOrganization;
    private string $organizationId;
    private string $baseUrl;

    public function __construct(ApiClientFactoryInterface $apiFactory)
    {
        $this->apiFactory = $apiFactory;
        $this->baseUrl = Parametros::where('nombre_parametro', 'url_organizacion')->firstOrFail()->valor_parametro; 
        $this->tokenOrganization = Parametros::where('nombre_parametro', 'token_organizacion')->firstOrFail()->valor_parametro; 
        $this->organizationId = Parametros::where('nombre_parametro', 'organizacion')->firstOrFail()->valor_parametro; 
    }

    /**
     * Obtiene todos los eventos programados para un usuario específico, manejando la paginación.
     * @param User $user El usuario (closer) para quien se obtendrán los eventos.
     * @return array La colección completa de eventos.
     */
    public function getAllScheduledEventsForUser(User $user): array
    {
        $apiClient = $this->apiFactory->createData($this->baseUrl, $this->tokenOrganization); 
        $allEvents = [];
        $nextPageUrl = 'scheduled_events'; // Endpoint inicial
        
        $params = [
            'organization' => $this->organizationId,
            'user' => $user->calendly_uri, // Asume que el user model tiene el 'calendly_uri'
            'count' => 100 // Pedir el máximo posible por página
        ];
        if (!empty($user->last_synced_at)) {
            $params['min_start_time'] = $user->last_synced_at->toIso8601String();
        }

        do {
            Log::info("Fetching events from: " . $nextPageUrl);
            $response = $apiClient->get($nextPageUrl, $params);

            if (isset($response['collection'])) {
                $allEvents = array_merge($allEvents, $response['collection']);
            }

            // Prepara la URL para la siguiente página
            $nextPageUrl = null;
            if (!empty($response['pagination']['next_page'])) {
                // Extraemos la ruta y los parámetros de la URL completa
                $parsedUrl = parse_url($response['pagination']['next_page']);
                $nextPageUrl = $parsedUrl['path'];
                if (!empty($parsedUrl['query'])) {
                     parse_str($parsedUrl['query'], $params); // Sobrescribe los params para la siguiente iteración
                }
            }

        } while ($nextPageUrl);

        return $allEvents;
    }
    
    /**
     * Obtiene los datos del invitado (lead) y las respuestas del formulario para un evento.
     * @param string $eventUuid El UUID del evento.
     * @return array|null Los datos del primer invitado.
     */
    public function getInviteeData(string $eventUuid): ?array
    {
        $apiClient = $this->apiFactory->createData($this->baseUrl, $this->tokenOrganization);
        $endpoint = "scheduled_events/{$eventUuid}/invitees"; 
        
        try {
            $response = $apiClient->get($endpoint);
            // Devuelve el primer invitado, que es el lead.
            return $response['collection'][0] ?? null;
        } catch (\Exception $e) {
            Log::error("Error fetching invitee for event {$eventUuid}: " . $e->getMessage());
            return null;
        }
    }
}