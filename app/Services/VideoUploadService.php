<?php

namespace App\Services;

use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class VideoUploadService
{
    /**
     * Crear un nuevo video
     */
    public function create(Course $course, array $data): Video
    {
        $data['course_id'] = $course->id;

        if (isset($data['video']) && $data['video'] instanceof UploadedFile) {
            $data['video_path'] = $this->uploadVideo($data['video'], $course);
            $data['duration_seconds'] = $this->getVideoDuration($data['video']);
            unset($data['video']);
        }

        return Video::create($data);
    }

    /**
     * Actualizar un video
     */
    public function update(Video $video, array $data): Video
    {
        if (isset($data['video']) && $data['video'] instanceof UploadedFile) {
            // Eliminar video anterior
            $this->deleteVideo($video->video_path);

            $data['video_path'] = $this->uploadVideo($data['video'], $video->course);
            $data['duration_seconds'] = $this->getVideoDuration($data['video']);
            unset($data['video']);
        }

        $video->update($data);
        return $video->fresh();
    }

    /**
     * Eliminar un video
     */
    public function delete(Video $video): bool
    {
        $this->deleteVideo($video->video_path);
        return $video->delete();
    }

    /**
     * Subir archivo de video
     */
    protected function uploadVideo(UploadedFile $file, Course $course): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'videos/courses/' . $course->slug;

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $file->move(public_path($path), $filename);

        return $path . '/' . $filename;
    }

    /**
     * Eliminar archivo de video
     */
    protected function deleteVideo(?string $videoPath): void
    {
        if ($videoPath && file_exists(public_path($videoPath))) {
            unlink(public_path($videoPath));
        }
    }

    /**
     * Obtener duración del video (aproximada basada en tamaño)
     * Nota: Para una implementación más precisa, usar FFmpeg
     */
    protected function getVideoDuration(UploadedFile $file): int
    {
        // Intentar usar getID3 si está disponible
        if (class_exists('getID3')) {
            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze($file->getRealPath());
            if (isset($fileInfo['playtime_seconds'])) {
                return (int) round($fileInfo['playtime_seconds']);
            }
        }

        // Fallback: estimar duración basada en tamaño (muy aproximado)
        // Asumiendo ~1MB por minuto para video comprimido
        $sizeInMB = $file->getSize() / (1024 * 1024);
        return (int) round($sizeInMB * 60);
    }

    /**
     * Reordenar videos dentro de un curso
     */
    public function reorder(Course $course, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Video::where('id', $id)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }
    }

    /**
     * Obtener videos de un curso ordenados
     */
    public function getVideosForCourse(Course $course): Collection
    {
        return $course->videos()->ordered()->get();
    }

    /**
     * Validar archivo de video
     */
    public function validateVideoFile(UploadedFile $file): array
    {
        $errors = [];

        // Verificar extensión
        $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'El archivo debe ser un video válido (mp4, webm, ogg, mov, avi).';
        }

        // Verificar tamaño (máximo 500MB)
        $maxSize = 500 * 1024 * 1024; // 500MB en bytes
        if ($file->getSize() > $maxSize) {
            $errors[] = 'El archivo de video no debe superar los 500MB.';
        }

        // Verificar MIME type
        $allowedMimeTypes = [
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime',
            'video/x-msvideo',
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            $errors[] = 'El tipo de archivo no es válido.';
        }

        return $errors;
    }

    /**
     * Obtener estadísticas de videos
     */
    public function getStats(): array
    {
        return [
            'total' => Video::count(),
            'total_duration' => Video::sum('duration_seconds'),
            'average_duration' => (int) Video::avg('duration_seconds'),
        ];
    }
}
