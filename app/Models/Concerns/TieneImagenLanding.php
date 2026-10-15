<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Services\ImagenLandingService;
use Illuminate\Support\Facades\File;

/** Modelos de la landing con una foto en `imagen` (nombre de archivo en public/uploads/landing). */
trait TieneImagenLanding
{
    /** Prefijo del nombre de archivo: "{prefijo}_{id}_{time}.webp" */
    abstract public function prefijoImagen(): string;

    public function hasImagen(): bool
    {
        return ! empty($this->imagen);
    }

    public function getImagenUrlAttribute(): string
    {
        return $this->imagen ? asset(ImagenLandingService::CARPETA.'/'.$this->imagen) : '';
    }

    /** @return array{0: int, 1: int}|null [ancho, alto] de la foto, para width/height (evita saltos al cargar). */
    public function dimensionesImagen(): ?array
    {
        $ruta = $this->imagen ? public_path(ImagenLandingService::CARPETA.'/'.$this->imagen) : null;
        $info = $ruta && File::exists($ruta) ? @getimagesize($ruta) : false;

        return $info ? [$info[0], $info[1]] : null;
    }

    /** srcset "móvil 800w, grande Nw" cuando existe la variante pequeña; '' si no. */
    public function getImagenSrcsetAttribute(): string
    {
        if (! $this->imagen) {
            return '';
        }

        $variante = ImagenLandingService::nombreVariante($this->imagen);
        $rutaGrande = public_path(ImagenLandingService::CARPETA.'/'.$this->imagen);
        $rutaVariante = public_path(ImagenLandingService::CARPETA.'/'.$variante);

        if ($variante === $this->imagen || ! File::exists($rutaVariante) || ! File::exists($rutaGrande)) {
            return '';
        }

        $anchoGrande = getimagesize($rutaGrande)[0] ?? 1600;
        $anchoVariante = getimagesize($rutaVariante)[0] ?? 800;

        return asset(ImagenLandingService::CARPETA.'/'.$variante).' '.$anchoVariante.'w, '
            .asset(ImagenLandingService::CARPETA.'/'.$this->imagen).' '.$anchoGrande.'w';
    }
}
