<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Textos editables de la landing. Si no hay fila en `landing_textos` para una clave,
 * se usa porDefecto(). En los títulos, lo que va entre *asteriscos* sale en cursiva dorada.
 */
enum TextoLanding: string
{
    case HeroEtiqueta      = 'hero_etiqueta';
    case HeroTitulo        = 'hero_titulo';
    case HeroTexto         = 'hero_texto';
    case HeroTarjetaTitulo = 'hero_tarjeta_titulo';
    case HeroTarjetaTexto  = 'hero_tarjeta_texto';
    case HistoriaTitulo    = 'historia_titulo';
    case Historia          = 'historia';
    case ProductosTitulo   = 'productos_titulo';
    case ProductosTexto    = 'productos_texto';
    case ProcesoTitulo     = 'proceso_titulo';
    case ProcesoTexto      = 'proceso_texto';
    case EncargosTitulo    = 'encargos_titulo';
    case EncargosTexto     = 'encargos_texto';
    case ValoresTitulo     = 'valores_titulo';
    case Mision            = 'mision';
    case Vision            = 'vision';
    case GaleriaTitulo     = 'galeria_titulo';
    case GaleriaTexto      = 'galeria_texto';
    case VisitanosTitulo   = 'visitanos_titulo';
    case PieTexto          = 'pie_texto';

    /** Sección de la página donde aparece (agrupa el formulario). */
    public function seccion(): string
    {
        return match ($this) {
            self::HeroEtiqueta, self::HeroTitulo, self::HeroTexto,
            self::HeroTarjetaTitulo, self::HeroTarjetaTexto => 'Portada',
            self::HistoriaTitulo, self::Historia            => 'Nuestra historia',
            self::ProductosTitulo, self::ProductosTexto     => 'Productos',
            self::ProcesoTitulo, self::ProcesoTexto         => 'Del horno a tu mesa',
            self::EncargosTitulo, self::EncargosTexto       => 'Encargos',
            self::ValoresTitulo                             => 'Por qué elegirnos',
            self::Mision, self::Vision                      => 'Misión y visión',
            self::GaleriaTitulo, self::GaleriaTexto         => 'Galería',
            self::VisitanosTitulo, self::PieTexto           => 'Visítanos y pie de página',
        };
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::HeroEtiqueta      => 'Etiqueta superior',
            self::HeroTitulo        => 'Título principal',
            self::HeroTexto         => 'Texto de presentación',
            self::HeroTarjetaTitulo => 'Tarjeta flotante: título',
            self::HeroTarjetaTexto  => 'Tarjeta flotante: texto',
            self::HistoriaTitulo, self::ProductosTitulo, self::ProcesoTitulo,
            self::EncargosTitulo, self::ValoresTitulo, self::GaleriaTitulo,
            self::VisitanosTitulo   => 'Título',
            self::Historia          => 'Historia',
            self::ProductosTexto, self::ProcesoTexto, self::EncargosTexto,
            self::GaleriaTexto      => 'Texto',
            self::Mision            => 'Misión',
            self::Vision            => 'Visión',
            self::PieTexto          => 'Texto del pie de página',
        };
    }

    /** Los títulos admiten *cursiva* y van en una línea; el resto es texto corrido. */
    public function esTitulo(): bool
    {
        return in_array($this, [
            self::HeroTitulo, self::HistoriaTitulo, self::ProductosTitulo, self::ProcesoTitulo,
            self::EncargosTitulo, self::ValoresTitulo, self::GaleriaTitulo, self::VisitanosTitulo,
        ], true);
    }

    public function multilinea(): bool
    {
        return in_array($this, [
            self::HeroTexto, self::Historia, self::ProductosTexto, self::ProcesoTexto,
            self::EncargosTexto, self::Mision, self::Vision, self::GaleriaTexto, self::PieTexto,
        ], true);
    }

    public function maximo(): int
    {
        return match ($this) {
            self::Historia                                   => 1500,
            self::Mision, self::Vision                       => 600,
            self::HeroTexto, self::EncargosTexto, self::PieTexto,
            self::ProductosTexto, self::ProcesoTexto,
            self::GaleriaTexto                               => 400,
            default                                          => 120,
        };
    }

    public function ayuda(): ?string
    {
        if ($this->esTitulo()) {
            return 'Encierra entre *asteriscos* la palabra que va en cursiva dorada.';
        }

        return $this === self::Historia ? 'Deja una línea en blanco entre párrafos.' : null;
    }

    public function porDefecto(): string
    {
        return match ($this) {
            self::HeroEtiqueta      => 'Panadería & Pastelería · Primavera, Bogotá',
            self::HeroTitulo        => 'Pan *dorado*, horneado con amor cada mañana.',
            self::HeroTexto         => 'Pan artesanal, pastelería y tortas por encargo, hechos a mano en nuestro horno del barrio Primavera. Escríbenos por WhatsApp y te lo tenemos listo.',
            self::HeroTarjetaTitulo => 'Recién horneado',
            self::HeroTarjetaTexto  => 'Varias horneadas al día',
            self::HistoriaTitulo    => 'Una tradición que *huele* a pan recién hecho',
            self::Historia          => "Nacimos con una idea sencilla: que cada mañana el barrio despierte con el olor del pan recién horneado. Desde nuestro horno en Primavera preparamos panes, hojaldres y tortas con recetas de casa, ingredientes seleccionados y mucha paciencia.\n\nCreemos que el buen pan se hace sin afán. Por eso amasamos a mano, respetamos los tiempos de cada masa y horneamos varias veces al día, para que siempre encuentres algo calientico en nuestra vitrina.",
            self::ProductosTitulo   => 'Hecho a mano, *todos* los días',
            self::ProductosTexto    => 'Del pan de la mañana a la torta de tu celebración. Elige lo que se te antoje y pídelo por WhatsApp: te lo tenemos listo.',
            self::ProcesoTitulo     => 'Así nace *cada* pan',
            self::ProcesoTexto      => 'Sin atajos: buenos ingredientes, manos expertas y el tiempo que cada masa necesita.',
            self::EncargosTitulo    => '¿Una celebración especial? *Hacemos tu torta* a la medida.',
            self::EncargosTexto     => 'Cumpleaños, primeras comuniones, bautizos, matrimonios o simplemente porque sí. Cuéntanos la ocasión, el sabor y las porciones, y te ayudamos a diseñarla.',
            self::ValoresTitulo     => 'Calidad *&* servicio en cada detalle',
            self::Mision            => 'Llevar a cada hogar del barrio pan y pastelería frescos, hechos con ingredientes de calidad y recetas de tradición, acompañados de una atención cercana que haga de cada visita un buen momento.',
            self::Vision            => 'Ser la panadería y pastelería de referencia en Bogotá por el sabor de nuestros productos, la calidad de nuestro servicio y el cariño con el que horneamos cada día.',
            self::GaleriaTitulo     => 'Un vistazo a *nuestra* vitrina',
            self::GaleriaTexto      => 'Muy pronto, fotos de nuestro local, de nuestro equipo y de todo lo que sale del horno cada día.',
            self::VisitanosTitulo   => 'Te esperamos con el *horno* encendido',
            self::PieTexto          => 'Pan artesanal y pastelería hechos con calidad & servicio, en el barrio Primavera de Bogotá.',
        };
    }

    /** @return array<string, list<self>> sección => claves, en el orden de la página */
    public static function porSeccion(): array
    {
        $grupos = [];
        foreach (self::cases() as $clave) {
            $grupos[$clave->seccion()][] = $clave;
        }

        return $grupos;
    }
}
