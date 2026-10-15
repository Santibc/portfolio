<?php

declare(strict_types=1);

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

/**
 * Guarda las fotos de la landing en public/uploads/landing (RULE 6: sin storage:link).
 *
 * Las fotos suelen venir del celular (varios MB, a veces rotadas): se corrige la orientación
 * EXIF, se reducen a 1600 px de ancho y se guardan en WebP, más una variante de 800 px
 * ("…-800.webp") para pantallas pequeñas. Si GD no puede abrir el archivo se guarda tal cual.
 */
final class ImagenLandingService
{
    public const CARPETA = 'uploads/landing';

    private const ANCHO_MAXIMO = 1600;
    private const ANCHO_MOVIL = 800;
    private const CALIDAD = 80;

    public function guardar(UploadedFile $archivo, string $prefijo, int $id): string
    {
        return $this->procesar($archivo->getRealPath(), strtolower($archivo->getClientOriginalExtension()), $prefijo, $id);
    }

    /** Para el seeder: importa una foto que ya está en el servidor. */
    public function guardarDesdeRuta(string $ruta, string $prefijo, int $id): string
    {
        return $this->procesar($ruta, strtolower(pathinfo($ruta, PATHINFO_EXTENSION)), $prefijo, $id);
    }

    public function eliminar(?string $nombre): void
    {
        if (! $nombre) {
            return;
        }

        foreach ([$nombre, self::nombreVariante($nombre)] as $archivo) {
            $ruta = public_path(self::CARPETA.'/'.$archivo);
            if (File::exists($ruta)) {
                File::delete($ruta);
            }
        }
    }

    /** "producto_3_1700000000.webp" -> "producto_3_1700000000-800.webp" */
    public static function nombreVariante(string $nombre): string
    {
        return (string) preg_replace('/\.webp$/', '-800.webp', $nombre);
    }

    private function procesar(string $origen, string $extension, string $prefijo, int $id): string
    {
        $carpeta = public_path(self::CARPETA);
        if (! File::exists($carpeta)) {
            File::makeDirectory($carpeta, 0755, true);
        }

        $base = $prefijo.'_'.$id.'_'.time();
        $imagen = $this->abrir($origen);

        if ($imagen === null) {
            $nombre = $base.'.'.$extension;
            File::copy($origen, $carpeta.'/'.$nombre);

            return $nombre;
        }

        $nombre = $base.'.webp';
        $this->exportar($imagen, $carpeta.'/'.$nombre, self::ANCHO_MAXIMO);
        if (imagesx($imagen) > self::ANCHO_MOVIL) {
            $this->exportar($imagen, $carpeta.'/'.self::nombreVariante($nombre), self::ANCHO_MOVIL);
        }
        imagedestroy($imagen);

        return $nombre;
    }

    private function abrir(string $ruta): ?GdImage
    {
        $datos = @file_get_contents($ruta);
        $imagen = $datos === false ? false : @imagecreatefromstring($datos);
        if ($imagen === false) {
            return null;
        }

        imagepalettetotruecolor($imagen);

        return $this->enderezar($imagen, $ruta);
    }

    /** Las cámaras guardan la foto "acostada" y anotan el giro en EXIF; aquí se aplica. */
    private function enderezar(GdImage $imagen, string $ruta): GdImage
    {
        $exif = function_exists('exif_read_data') ? @exif_read_data($ruta) : false;
        $grados = match ((int) ($exif['Orientation'] ?? 1)) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($grados === 0) {
            return $imagen;
        }

        $girada = imagerotate($imagen, $grados, 0);
        if ($girada === false) {
            return $imagen;
        }
        imagedestroy($imagen);

        return $girada;
    }

    private function exportar(GdImage $imagen, string $destino, int $anchoMaximo): void
    {
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        $salida = $imagen;

        if ($ancho > $anchoMaximo) {
            $nuevoAlto = (int) round($alto * $anchoMaximo / $ancho);
            $salida = imagecreatetruecolor($anchoMaximo, $nuevoAlto);
            imagealphablending($salida, false);
            imagesavealpha($salida, true);
            imagecopyresampled($salida, $imagen, 0, 0, 0, 0, $anchoMaximo, $nuevoAlto, $ancho, $alto);
        }

        imagesavealpha($salida, true);
        imagewebp($salida, $destino, self::CALIDAD);

        if ($salida !== $imagen) {
            imagedestroy($salida);
        }
    }
}
