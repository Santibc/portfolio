<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\LandingAjuste;

/**
 * Datos públicos del negocio (módulo "Página web") y los enlaces derivados que usa la
 * landing: WhatsApp, llamada, mapa embebido y "cómo llegar".
 */
final class Negocio
{
    private const PAIS = 'Colombia';

    /**
     * @param list<array{dias: string, horas: string}> $horario
     * @param array<string, string> $redes red => URL
     */
    public function __construct(
        public readonly string $nombre,
        public readonly string $nombreLargo,
        public readonly string $lema,
        public readonly ?string $propietaria,
        public readonly ?string $propietariaCargo,
        public readonly string $telefono,
        public readonly string $whatsapp,
        public readonly string $whatsappMensaje,
        public readonly string $direccion,
        public readonly ?string $barrio,
        public readonly string $ciudad,
        public readonly ?string $mapsApiKey,
        public readonly array $horario,
        public readonly array $redes,
    ) {
    }

    /** @param iterable<\App\Models\LandingHorario> $horarios */
    public static function desde(LandingAjuste $ajuste, iterable $horarios, ?string $mapsApiKey): self
    {
        $horario = [];
        foreach ($horarios as $franja) {
            $horario[] = ['dias' => $franja->dias, 'horas' => $franja->horas];
        }

        return new self(
            nombre: $ajuste->nombre,
            nombreLargo: $ajuste->nombre_largo,
            lema: $ajuste->lema,
            propietaria: $ajuste->propietaria,
            propietariaCargo: $ajuste->propietaria_cargo,
            telefono: $ajuste->telefono,
            whatsapp: $ajuste->whatsapp,
            whatsappMensaje: $ajuste->whatsapp_mensaje,
            direccion: $ajuste->direccion,
            barrio: $ajuste->barrio,
            ciudad: $ajuste->ciudad,
            mapsApiKey: $mapsApiKey ?: null,
            horario: $horario,
            redes: $ajuste->redes(),
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
        return implode(', ', array_filter([$this->direccion, $this->barrio, $this->ciudad]));
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
        return "{$this->direccion}, {$this->ciudad}, ".self::PAIS;
    }
}
