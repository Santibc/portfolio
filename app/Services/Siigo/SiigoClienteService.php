<?php

namespace App\Services\Siigo;

use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Garantiza que el cliente exista en Siigo antes de facturar.
 *
 * Siigo exige que el cliente esté registrado para poder asociarlo a una factura
 * (en el payload de la factura solo se envía su `identification`). Este servicio
 * lo busca por identificación y, si no existe, lo crea (POST /v1/customers)
 * mapeando los datos fiscales del modelo Cliente de clc.
 *
 * Replica el flujo probado del proyecto miracle (resolverCliente / crearClienteEnSiigo).
 *
 * @see https://developers.siigo.com/docs/siigoapi/customer/1-create-customer
 */
class SiigoClienteService
{
    /**
     * Mapa tipo_identificacion (clc) → id_type (código DIAN/Siigo).
     */
    private const ID_TYPES = [
        'NIT' => '31',
        'CC' => '13',
        'CE' => '22',
        'TI' => '12',
        'PA' => '41',
        'PASAPORTE' => '41',
        'DEX' => '42',
        'NIT_EXT' => '50',
    ];

    public function __construct(private readonly SiigoClient $cliente) {}

    /**
     * Asegura que el cliente exista en Siigo y devuelve su identificación.
     *
     * @throws RuntimeException si falta identificación o Siigo rechaza la creación.
     */
    public function resolver(Cliente $cliente): string
    {
        $identificacion = trim((string) $cliente->identificacion);

        if ($identificacion === '') {
            throw new RuntimeException('El cliente no tiene identificación — requerida por la DIAN.');
        }

        // Ya vinculado a Siigo en una emisión anterior.
        if (! empty($cliente->siigo_id)) {
            return $identificacion;
        }

        if ($this->existeEnSiigo($identificacion)) {
            return $identificacion;
        }

        $this->crear($cliente, $identificacion);

        return $identificacion;
    }

    private function existeEnSiigo(string $identificacion): bool
    {
        $response = $this->cliente->request('GET', '/v1/customers', [
            'identification' => $identificacion,
        ]);

        if ($response->failed()) {
            return false;
        }

        $results = (array) ($response->json('results') ?? []);

        return count($results) > 0;
    }

    private function crear(Cliente $cliente, string $identificacion): void
    {
        $payload = $this->construirPayload($cliente, $identificacion);

        Log::channel('siigo')->info('Siigo — creando cliente', [
            'cliente_id' => $cliente->id,
            'identificacion' => $identificacion,
        ]);

        $response = $this->cliente->request('POST', '/v1/customers', $payload);

        if ($response->failed()) {
            $mensaje = (string) ($response->json('Errors.0.Message') ?? $response->json('message') ?? 'Error desconocido.');

            throw new RuntimeException("No se pudo crear el cliente en Siigo: {$mensaje}");
        }

        $siigoId = (string) ($response->json('id') ?? '');

        if ($siigoId !== '') {
            $cliente->forceFill(['siigo_id' => $siigoId])->save();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function construirPayload(Cliente $cliente, string $identificacion): array
    {
        $esEmpresa = $this->esEmpresa($cliente);

        $payload = [
            'type' => 'Customer',
            'person_type' => $esEmpresa ? 'Company' : 'Person',
            'id_type' => $this->mapearIdType($cliente),
            'identification' => $identificacion,
            'branch_office' => 0,
            'active' => true,
            'vat_responsible' => $esEmpresa,
            'fiscal_responsibilities' => [['code' => 'R-99-PN']],
            'name' => $this->nombre($cliente, $esEmpresa),
            'contacts' => [$this->contacto($cliente)],
        ];

        if (! empty($cliente->telefono)) {
            $payload['phones'] = [['number' => (string) $cliente->telefono]];
        }

        // Solo enviamos dirección para clientes nacionales: Siigo exige códigos de
        // ciudad/estado colombianos válidos. Para el exterior se omite (campo opcional)
        // y el carácter internacional queda determinado por id_type.
        if (! $cliente->esInternacional() && ! empty($cliente->direccion_facturacion)) {
            $payload['address'] = [
                'address' => (string) $cliente->direccion_facturacion,
                'city' => [
                    'country_code' => 'Co',
                    'state_code' => '11',
                    'city_code' => '11001',
                ],
            ];
        }

        return $payload;
    }

    private function esEmpresa(Cliente $cliente): bool
    {
        return strtoupper((string) $cliente->tipo_identificacion) === 'NIT';
    }

    private function mapearIdType(Cliente $cliente): string
    {
        $tipo = strtoupper(trim((string) $cliente->tipo_identificacion));

        if (isset(self::ID_TYPES[$tipo])) {
            return self::ID_TYPES[$tipo];
        }

        // Fallback: pasaporte para el exterior, cédula de ciudadanía para nacionales.
        return $cliente->esInternacional() ? '41' : '13';
    }

    /**
     * Siigo espera el nombre como arreglo: [razón social] para empresa,
     * [nombre, apellido] para persona natural.
     *
     * @return array<int, string>
     */
    private function nombre(Cliente $cliente, bool $esEmpresa): array
    {
        $nombre = trim((string) ($cliente->nombre ?? ''));

        if ($esEmpresa) {
            return [$nombre !== '' ? $nombre : 'Cliente'];
        }

        $partes = preg_split('/\s+/', $nombre) ?: [];
        $first = $partes[0] ?? 'Cliente';
        $last = count($partes) > 1 ? implode(' ', array_slice($partes, 1)) : '.';

        return [$first !== '' ? $first : 'Cliente', $last];
    }

    /**
     * @return array<string, string>
     */
    private function contacto(Cliente $cliente): array
    {
        $email = trim((string) ($cliente->email ?? ''));
        if ($email === '') {
            $email = 'sin-email@clc.com';
        }

        [$first, $last] = array_pad($this->nombre($cliente, false), 2, '.');

        return [
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
        ];
    }
}
