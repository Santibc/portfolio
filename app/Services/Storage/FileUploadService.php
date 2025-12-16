<?php

namespace App\Services\Storage;

use App\Models\DocumentoProyecto;
use App\Models\ImagenProyecto;
use App\Models\Proyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Tipos de documentos permitidos
     */
    const DOCUMENT_TYPES = ['pdf', 'doc', 'docx'];

    /**
     * Tipos de imágenes permitidos
     */
    const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Tamaño máximo de documento (5MB)
     */
    const MAX_DOCUMENT_SIZE = 5 * 1024 * 1024;

    /**
     * Tamaño máximo de imagen (2MB)
     */
    const MAX_IMAGE_SIZE = 2 * 1024 * 1024;

    /**
     * Tamaño del thumbnail
     */
    const THUMBNAIL_WIDTH = 300;
    const THUMBNAIL_HEIGHT = 200;

    /**
     * Subir documento de proyecto
     */
    public function uploadDocument(
        UploadedFile $file,
        Proyecto $proyecto,
        string $tipoDocumento,
        ?string $descripcion = null,
        int $subidoPor = null
    ): DocumentoProyecto {
        // Guardar datos del archivo ANTES de moverlo
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $extension = $file->getClientOriginalExtension();

        // Crear directorio si no existe
        $directory = $this->getDocumentDirectory($proyecto->id);
        $this->ensureDirectoryExists($directory);

        // Generar nombre único
        $fileName = $this->generateFileName($tipoDocumento, $extension);

        // Mover archivo
        $file->move($directory, $fileName);

        // Ruta relativa para guardar en BD
        $relativePath = "uploads/proyectos/{$proyecto->id}/documentos/{$fileName}";

        // Crear registro
        return DocumentoProyecto::create([
            'proyecto_id' => $proyecto->id,
            'tipo_documento' => $tipoDocumento,
            'nombre_archivo' => $originalName,
            'ruta_archivo' => $relativePath,
            'tipo_mime' => $mimeType,
            'tamano_bytes' => $fileSize,
            'descripcion' => $descripcion,
            'subido_por' => $subidoPor ?? auth()->id(),
        ]);
    }

    /**
     * Subir imagen de proyecto
     */
    public function uploadImage(
        UploadedFile $file,
        Proyecto $proyecto,
        ?string $titulo = null,
        ?string $descripcion = null,
        bool $esPrincipal = false
    ): ImagenProyecto {
        // Guardar datos del archivo ANTES de moverlo
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Crear directorios si no existen
        $imageDirectory = $this->getImageDirectory($proyecto->id);
        $thumbnailDirectory = $this->getThumbnailDirectory($proyecto->id);
        $this->ensureDirectoryExists($imageDirectory);
        $this->ensureDirectoryExists($thumbnailDirectory);

        // Generar nombre único
        $baseName = Str::uuid();
        $fileName = "{$baseName}.{$extension}";
        $thumbnailName = "{$baseName}_thumb.{$extension}";

        // Mover archivo original
        $file->move($imageDirectory, $fileName);

        // Generar thumbnail
        $imagePath = "{$imageDirectory}/{$fileName}";
        $thumbnailPath = "{$thumbnailDirectory}/{$thumbnailName}";
        $this->generateThumbnail($imagePath, $thumbnailPath);

        // Rutas relativas para BD
        $relativeImagePath = "uploads/proyectos/{$proyecto->id}/imagenes/{$fileName}";
        $relativeThumbnailPath = "uploads/proyectos/{$proyecto->id}/imagenes/thumbnails/{$thumbnailName}";

        // Si es principal, desmarcar las demás
        if ($esPrincipal) {
            ImagenProyecto::where('proyecto_id', $proyecto->id)
                ->update(['es_principal' => false]);
        }

        // Obtener orden máximo
        $maxOrden = ImagenProyecto::where('proyecto_id', $proyecto->id)->max('orden') ?? 0;

        // Crear registro
        return ImagenProyecto::create([
            'proyecto_id' => $proyecto->id,
            'ruta_imagen' => $relativeImagePath,
            'thumbnail' => $relativeThumbnailPath,
            'titulo' => $titulo ?? $originalName,
            'descripcion' => $descripcion,
            'es_principal' => $esPrincipal,
            'orden' => $maxOrden + 1,
        ]);
    }

    /**
     * Eliminar documento
     */
    public function deleteDocument(DocumentoProyecto $documento): bool
    {
        // Eliminar archivo físico
        $fullPath = public_path($documento->ruta_archivo);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        // Eliminar registro
        return $documento->delete();
    }

    /**
     * Eliminar imagen
     */
    public function deleteImage(ImagenProyecto $imagen): bool
    {
        // Eliminar imagen original
        $imagePath = public_path($imagen->ruta_imagen);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Eliminar thumbnail
        if ($imagen->thumbnail) {
            $thumbnailPath = public_path($imagen->thumbnail);
            if (File::exists($thumbnailPath)) {
                File::delete($thumbnailPath);
            }
        }

        // Eliminar registro
        return $imagen->delete();
    }

    /**
     * Establecer imagen como principal
     */
    public function setImageAsPrincipal(ImagenProyecto $imagen): bool
    {
        // Desmarcar todas las demás
        ImagenProyecto::where('proyecto_id', $imagen->proyecto_id)
            ->where('id', '!=', $imagen->id)
            ->update(['es_principal' => false]);

        // Marcar esta como principal
        $imagen->es_principal = true;
        return $imagen->save();
    }

    /**
     * Generar thumbnail de imagen
     */
    protected function generateThumbnail(string $sourcePath, string $destinationPath): bool
    {
        // Verificar que GD esté disponible
        if (!extension_loaded('gd')) {
            // Si no hay GD, copiar imagen original como thumbnail
            return File::copy($sourcePath, $destinationPath);
        }

        // Obtener información de la imagen
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Crear imagen desde archivo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return File::copy($sourcePath, $destinationPath);
        }

        if (!$source) {
            return false;
        }

        // Calcular dimensiones manteniendo proporción
        $ratio = min(self::THUMBNAIL_WIDTH / $width, self::THUMBNAIL_HEIGHT / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Crear imagen redimensionada
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Redimensionar
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar thumbnail
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($thumbnail, $destinationPath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($thumbnail, $destinationPath, 8);
                break;
            case IMAGETYPE_WEBP:
                $result = imagewebp($thumbnail, $destinationPath, 85);
                break;
            default:
                $result = false;
        }

        // Liberar memoria
        imagedestroy($source);
        imagedestroy($thumbnail);

        return $result;
    }

    /**
     * Obtener directorio de documentos
     */
    protected function getDocumentDirectory(int $proyectoId): string
    {
        return public_path("uploads/proyectos/{$proyectoId}/documentos");
    }

    /**
     * Obtener directorio de imágenes
     */
    protected function getImageDirectory(int $proyectoId): string
    {
        return public_path("uploads/proyectos/{$proyectoId}/imagenes");
    }

    /**
     * Obtener directorio de thumbnails
     */
    protected function getThumbnailDirectory(int $proyectoId): string
    {
        return public_path("uploads/proyectos/{$proyectoId}/imagenes/thumbnails");
    }

    /**
     * Asegurar que el directorio exista
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Generar nombre de archivo único
     */
    protected function generateFileName(string $prefix, string $extension): string
    {
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);
        return "{$prefix}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Obtener URL pública de un archivo
     */
    public function getPublicUrl(string $relativePath): string
    {
        return asset($relativePath);
    }

    /**
     * Verificar si un archivo existe
     */
    public function fileExists(string $relativePath): bool
    {
        return File::exists(public_path($relativePath));
    }
}
