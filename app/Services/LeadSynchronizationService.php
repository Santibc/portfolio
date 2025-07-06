<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lead;
use App\Models\Llamada;
use App\Models\LlamadaRespuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LeadSynchronizationService
{
    private CalendlyEventImporter $eventImporter;

    public function __construct(CalendlyEventImporter $eventImporter)
    {
        $this->eventImporter = $eventImporter;
    }

    /**
     * Sincroniza los leads y las llamadas desde Calendly para UN closer específico.
     *
     * @param User $closer El usuario (closer) para quien se realizará la importación.
     * @return array Un informe del proceso.
     */
    public function synchronizeLeadsAndCalls(User $closer): array
    {
        $report = ['imported' => 0, 'skipped' => 0, 'errors' => []];

        // 1. Validar que el closer proporcionado es válido y tiene la URI de Calendly.
        if (empty($closer->calendly_uri)) {
            $report['errors'][] = "El usuario '{$closer->name}' no tiene un 'calendly_uri' configurado.";
            Log::warning("Sincronización de Leads: El usuario {$closer->id} no tiene un calendly_uri.");
            return $report;
        }

        Log::info("Iniciando sincronización de eventos para el closer: {$closer->name} (ID: {$closer->id})");

        // 2. Obtener todas las URIs de llamadas ya existentes del closer (optimización)
        $llamadasExistentes = Llamada::where('user_id', $closer->id)->pluck('uri')->toArray();

        // 3. Obtener todos los eventos para el closer específico.
        $events = $this->eventImporter->getAllScheduledEventsForUser($closer);

        foreach ($events as $eventData) {
            // 4. Validar si la llamada ya existe en memoria (más eficiente)
            if (in_array($eventData['uri'], $llamadasExistentes)) {
                $report['skipped']++;
                continue;
            }

            // 5. Obtener los datos del invitado (lead) para el evento.
            $eventUuid = basename($eventData['uri']);
            $inviteeData = $this->eventImporter->getInviteeData($eventUuid);

            if (!$inviteeData || !isset($inviteeData['email'])) {
                $errorMessage = "No se encontró información del invitado o su email para el evento: {$eventData['uri']}";
                $report['errors'][] = $errorMessage;
                Log::warning($errorMessage);
                continue;
            }

            // 6. Iniciar transacción para garantizar la integridad de los datos.
            DB::beginTransaction();
            try {
                // 7. Buscar o crear el lead, asignando el closer si es la primera vez.
                $lead = Lead::firstOrCreate(
                    ['email' => $inviteeData['email']],
                    [
                        'nombre'  => $inviteeData['name'],
                        'user_id' => $closer->id
                    ]
                );

                // 8. Enriquecer el lead con info adicional si está vacía.
                if (isset($inviteeData['questions_and_answers'])) {
                    $needsSave = false;

                    if (empty($lead->instagram_user)) {
                        foreach ($inviteeData['questions_and_answers'] as $qa) {
                            if (stripos($qa['question'], 'instagram') !== false) {
                                $lead->instagram_user = $qa['answer'];
                                $needsSave = true;
                                break;
                            }
                        }
                    }

                    if (empty($lead->telefono)) {
                        foreach ($inviteeData['questions_and_answers'] as $qa) {
                            if (stripos($qa['question'], 'contacto') !== false || stripos($qa['question'], 'telefono') !== false) {
                                $lead->telefono = $qa['answer'];
                                $needsSave = true;
                                break;
                            }
                        }
                    }

                    if ($needsSave) {
                        $lead->save();
                    }
                }

                // 9. Crear el registro de la llamada.
                $llamada = Llamada::create([
                    'uri'               => $eventData['uri'],
                    'lead_id'           => $lead->id,
                    'user_id'           => $closer->id,
                    'nombre_evento'     => $eventData['name'],
                    'status'            => $eventData['status'],
                    'start_time'        => new \DateTime($eventData['start_time']),
                    'end_time'          => new \DateTime($eventData['end_time']),
                    'join_url'          => $eventData['location']['join_url'] ?? null,
                    'event_type_uri'    => $eventData['event_type'],
                    'cancelado_por'     => $eventData['cancellation']['canceled_by'] ?? null,
                    'motivo_cancelacion'=> $eventData['cancellation']['reason'] ?? null,
                ]);

                // 10. Guardar las respuestas del formulario.
                if (!empty($inviteeData['questions_and_answers'])) {
                    foreach ($inviteeData['questions_and_answers'] as $qa) {
                        LlamadaRespuesta::create([
                            'llamada_id' => $llamada->id,
                            'pregunta'   => $qa['question'],
                            'respuesta'  => $qa['answer']
                        ]);
                    }
                }

                DB::commit();
                $report['imported']++;
                Log::info("Evento importado: {$eventData['uri']} para el lead {$lead->email}");

            } catch (Exception $e) {
                DB::rollBack();
                $errorMessage = "Error al procesar el evento {$eventData['uri']}: " . $e->getMessage();
                $report['errors'][] = $errorMessage;
                Log::error($errorMessage, ['exception' => $e, 'event_data' => $eventData]);
            }
        }
        $closer->last_synced_at = now();
        $closer->save();
        return $report;
    }
}
