<?php

declare(strict_types=1);

namespace App\Enums;

/** Fotos fijas de la landing (una por lugar del diseño). */
enum FotoLanding: string
{
    case Hero     = 'hero';
    case HeroMini = 'hero_mini';
    case Historia = 'historia';
    case Local    = 'local';
    case Encargos = 'encargos';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Hero     => 'Foto principal de la portada',
            self::HeroMini => 'Miniatura de la tarjeta flotante',
            self::Historia => 'Foto de "Nuestra historia"',
            self::Local    => 'Foto del local',
            self::Encargos => 'Fondo de "Encargos"',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::Hero     => 'La primera foto que ven tus clientes, en forma de arco. Mejor vertical.',
            self::HeroMini => 'Foto pequeña y cuadrada junto a "Recién horneado".',
            self::Historia => 'Foto grande de la sección "Nuestra historia". Mejor vertical.',
            self::Local    => 'Foto pequeña inclinada sobre la anterior. Ideal: la fachada o el mostrador.',
            self::Encargos => 'Fondo de la banda de tortas por encargo. Mejor horizontal, con el producto a la derecha.',
        };
    }

    /** Clase de proporción para la vista previa en el panel. */
    public function proporcion(): string
    {
        return match ($this) {
            self::Hero, self::Historia, self::Local => 'aspect-[4/5]',
            self::HeroMini                          => 'aspect-square',
            self::Encargos                          => 'aspect-[3/2]',
        };
    }

    public function altPorDefecto(): string
    {
        return match ($this) {
            self::Hero     => 'Panes artesanales recién horneados junto a espigas de trigo',
            self::HeroMini => 'Croissants recién horneados',
            self::Historia => 'Croissants dorados recién horneados en la vitrina',
            self::Local    => 'Nuestro local',
            self::Encargos => 'Porción de torta de frambuesas',
        };
    }
}
