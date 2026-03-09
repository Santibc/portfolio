<?php

namespace App\Helpers;

use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Convierte una imagen a cuadrada agregando padding blanco.
     * La imagen se centra en un canvas blanco de max(w,h) x max(w,h).
     * Sobreescribe el archivo original.
     */
    public static function makeSquare(string $absolutePath): void
    {
        $img = Image::make($absolutePath);
        $width = $img->width();
        $height = $img->height();

        if ($width === $height) {
            $img->destroy();
            return;
        }

        $size = max($width, $height);
        $square = Image::canvas($size, $size, '#ffffff');
        $square->insert($img, 'center');

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $quality = ($extension === 'png') ? 9 : 85;

        $square->save($absolutePath, $quality);
        $img->destroy();
        $square->destroy();
    }

    /**
     * Genera miniatura cuadrada desde una imagen ya cuadrada.
     */
    public static function makeSquareThumbnail(string $sourcePath, string $thumbPath, int $size = 300, int $quality = 80): void
    {
        $img = Image::make($sourcePath);
        $img->resize($size, $size);
        $img->save($thumbPath, $quality);
        $img->destroy();
    }
}
