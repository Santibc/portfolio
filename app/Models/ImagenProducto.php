<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImagenProducto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_productos';

    protected $fillable = [
        'producto_id',
        'ruta_imagen',
        'texto_alternativo',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->ruta_imagen);
    }

    /**
     * Devuelve la ruta en disco de una miniatura pequeña y cacheada de la imagen,
     * pensada para incrustar en PDFs (DomPDF). Generar una miniatura reduce
     * drásticamente el tiempo de render frente a incrustar la foto a resolución
     * completa, que con muchos ítems provoca timeouts (504) al exportar.
     *
     * Si el original no existe o la miniatura no se puede generar, devuelve la
     * ruta del original (DomPDF simplemente la ignora si no es válida).
     */
    public function rutaImagenPdf(int $maxDim = 120): string
    {
        $original = public_path($this->ruta_imagen);

        if (!is_file($original)) {
            return $original;
        }

        $thumbDir  = public_path('imagenes/thumbs/pdf');
        $thumbPath = $thumbDir . DIRECTORY_SEPARATOR . $this->id . '.jpg';

        // Usar caché si está al día respecto al original.
        if (is_file($thumbPath) && filemtime($thumbPath) >= filemtime($original)) {
            return $thumbPath;
        }

        if (!extension_loaded('gd')) {
            return $original;
        }

        try {
            if (!is_dir($thumbDir)) {
                @mkdir($thumbDir, 0775, true);
            }

            $info = @getimagesize($original);
            if ($info === false) {
                return $original;
            }

            [$width, $height] = $info;
            $mime = $info['mime'] ?? '';

            switch ($mime) {
                case 'image/jpeg':
                    $src = @imagecreatefromjpeg($original);
                    break;
                case 'image/png':
                    $src = @imagecreatefrompng($original);
                    break;
                case 'image/gif':
                    $src = @imagecreatefromgif($original);
                    break;
                case 'image/webp':
                    $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($original) : false;
                    break;
                default:
                    $src = false;
            }

            if (!$src || $width < 1 || $height < 1) {
                return $original;
            }

            $scale = min(1, $maxDim / max($width, $height));
            $newW  = max(1, (int) round($width * $scale));
            $newH  = max(1, (int) round($height * $scale));

            $dst   = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $white); // aplana transparencias
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagejpeg($dst, $thumbPath, 75);
            imagedestroy($src);
            imagedestroy($dst);

            return is_file($thumbPath) ? $thumbPath : $original;
        } catch (\Throwable $e) {
            return $original;
        }
    }

    protected static function boot()
    {
        parent::boot();

        // Asegurar que solo una imagen sea principal por producto
        static::creating(function ($imagen) {
            if ($imagen->es_principal) {
                static::where('producto_id', $imagen->producto_id)
                    ->update(['es_principal' => false]);
            }
        });

        static::updating(function ($imagen) {
            if ($imagen->es_principal && $imagen->isDirty('es_principal')) {
                static::where('producto_id', $imagen->producto_id)
                    ->where('id', '!=', $imagen->id)
                    ->update(['es_principal' => false]);
            }
        });
    }
}