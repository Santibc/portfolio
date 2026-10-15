<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Iconos (Lucide) disponibles para las tarjetas de "Por qué elegirnos".
 * Cada uno debe estar importado en resources/js/landing.js.
 */
enum IconoLanding: string
{
    case Medalla   = 'award';
    case Apreton   = 'heart-handshake';
    case Amanecer  = 'sunrise';
    case Trigo     = 'wheat';
    case Hoja      = 'leaf';
    case Destellos = 'sparkles';
    case Corazon   = 'heart';
    case Croissant = 'croissant';
    case Torta     = 'cake-slice';
    case Cafe      = 'coffee';
    case Reloj     = 'clock';
    case Tienda    = 'store';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Medalla   => 'Medalla',
            self::Apreton   => 'Apretón de manos',
            self::Amanecer  => 'Amanecer',
            self::Trigo     => 'Espiga de trigo',
            self::Hoja      => 'Hoja',
            self::Destellos => 'Destellos',
            self::Corazon   => 'Corazón',
            self::Croissant => 'Croissant',
            self::Torta     => 'Porción de torta',
            self::Cafe      => 'Café',
            self::Reloj     => 'Reloj',
            self::Tienda    => 'Tienda',
        };
    }

    /** @return array<string, string> valor => etiqueta, para <x-select> */
    public static function opciones(): array
    {
        $opciones = [];
        foreach (self::cases() as $icono) {
            $opciones[$icono->value] = $icono->etiqueta();
        }

        return $opciones;
    }
}
