<?php

declare(strict_types=1);

namespace App\Data;

/**
 * Datos públicos de la panadería (config/panaderia.php) y los enlaces derivados
 * que usa la landing: WhatsApp, llamada, mapa embebido y "cómo llegar".
 */
final class Negocio
{
    /**
     * @param array<int, array{dias: string, horas: string}> $horario
     * @param array<string, ?string> $redes
     * @param array<int, string> $historia
     * @param array<int, array{valor: int, prefijo: string, sufijo: string, texto: string}> $cifras
     * @param array<int, array{icono: string, titulo: string, texto: string}> $valores
     * @param array<int, array{titulo: string, descripcion: string, imagen: string}> $productos
     */
    public function __construct(
        public readonly string $nombre,
        public readonly string $nombreLargo,
        public readonly string $lema,
        public readonly string $propietaria,
        public readonly string $propietariaCargo,
        public readonly string $telefono,
        public readonly string $whatsapp,
        public readonly string $whatsappMensaje,
        public readonly string $direccion,
        public readonly string $barrio,
        public readonly string $ciudad,
        public readonly string $pais,
        public readonly ?string $mapsApiKey,
        public readonly array $horario,
        public readonly array $redes,
        public readonly array $historia,
        public readonly array $cifras,
        public readonly string $mision,
        public readonly string $vision,
        public readonly array $valores,
        public readonly array $productos,
    ) {
    }

    /** @param array<string, mixed> $config */
    public static function desdeConfig(array $config): self
    {
        return new self(
            nombre: $config['nombre'],
            nombreLargo: $config['nombre_largo'],
            lema: $config['lema'],
            propietaria: $config['propietaria'],
            propietariaCargo: $config['propietaria_cargo'],
            telefono: $config['telefono'],
            whatsapp: $config['whatsapp'],
            whatsappMensaje: $config['whatsapp_mensaje'],
            direccion: $config['direccion'],
            barrio: $config['barrio'],
            ciudad: $config['ciudad'],
            pais: $config['pais'],
            mapsApiKey: $config['maps_api_key'] ?: null,
            horario: $config['horario'],
            redes: array_filter($config['redes']),
            historia: $config['historia'],
            cifras: $config['cifras'],
            mision: $config['mision'],
            vision: $config['vision'],
            valores: $config['valores'],
            productos: $config['productos'],
        );
    }

    public function whatsappUrl(?string $mensaje = null): string
    {
        return 'https://wa.me/'.$this->whatsapp.'?text='.rawurlencode($mensaje ?? $this->whatsappMensaje);
    }

    /** "3188655792" -> "318 865 5792" */
    public function telefonoVisible(): string
    {
        return preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $this->telefono) ?? $this->telefono;
    }

    public function telUrl(): string
    {
        return 'tel:+'.$this->whatsapp;
    }

    public function direccionCompleta(): string
    {
        return "{$this->direccion}, {$this->barrio}, {$this->ciudad}";
    }

    public function mapaEmbedUrl(): string
    {
        $lugar = rawurlencode($this->direccionParaMapa());

        if ($this->mapsApiKey !== null) {
            return 'https://www.google.com/maps/embed/v1/place?key='.rawurlencode($this->mapsApiKey).'&q='.$lugar.'&zoom=17&language=es';
        }

        return 'https://maps.google.com/maps?q='.$lugar.'&z=17&hl=es&output=embed';
    }

    public function comoLlegarUrl(): string
    {
        return 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($this->direccionParaMapa());
    }

    /**
     * schema.org Bakery (JSON-LD) para buscadores. El horario se omite a propósito
     * hasta que el cliente confirme el real.
     *
     * @return array<string, mixed>
     */
    public function datosEstructurados(string $url, string $imagen): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Bakery',
            'name' => $this->nombreLargo,
            'url' => $url,
            'image' => $imagen,
            'telephone' => '+'.$this->whatsapp,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->direccion,
                'addressLocality' => $this->ciudad,
                'addressCountry' => 'CO',
            ],
            'sameAs' => array_values($this->redes) ?: null,
        ]);
    }

    private function direccionParaMapa(): string
    {
        return "{$this->direccion}, {$this->ciudad}, {$this->pais}";
    }
}
