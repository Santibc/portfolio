<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class DocumentUploadService
{
    /**
     * Extensiones permitidas
     */
    protected array $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    /**
     * MIME types permitidos
     */
    protected array $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    /**
     * Tamaño máximo en bytes (50MB)
     */
    protected int $maxSize = 52428800;

    /**
     * Crear un nuevo documento
     */
    public function create(Course $course, array $data): Document
    {
        $data['course_id'] = $course->id;

        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            // Obtener info ANTES de mover el archivo
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
            // Mover archivo DESPUÉS de obtener la info
            $data['file_path'] = $this->uploadDocument($file, $course);
            unset($data['file']);
        }

        return Document::create($data);
    }

    /**
     * Actualizar un documento
     */
    public function update(Document $document, array $data): Document
    {
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            // Obtener info ANTES de mover el archivo
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
            // Eliminar archivo anterior
            $this->deleteDocument($document->file_path);
            // Mover archivo DESPUÉS de obtener la info
            $data['file_path'] = $this->uploadDocument($file, $document->course);
            unset($data['file']);
        }

        $document->update($data);
        return $document->fresh();
    }

    /**
     * Eliminar un documento
     */
    public function delete(Document $document): bool
    {
        $this->deleteDocument($document->file_path);
        return $document->delete();
    }

    /**
     * Subir archivo de documento
     */
    protected function uploadDocument(UploadedFile $file, Course $course): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'documents/courses/' . $course->slug;

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $file->move(public_path($path), $filename);

        return $path . '/' . $filename;
    }

    /**
     * Eliminar archivo de documento
     */
    protected function deleteDocument(?string $filePath): void
    {
        if ($filePath && file_exists(public_path($filePath))) {
            unlink(public_path($filePath));
        }
    }

    /**
     * Reordenar documentos dentro de un curso
     */
    public function reorder(Course $course, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Document::where('id', $id)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }
    }

    /**
     * Obtener documentos de un curso ordenados
     */
    public function getDocumentsForCourse(Course $course): Collection
    {
        return $course->documents()->ordered()->get();
    }

    /**
     * Validar archivo de documento
     */
    public function validateDocumentFile(UploadedFile $file): array
    {
        $errors = [];

        // Verificar extensión
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $this->allowedExtensions)) {
            $errors[] = 'El archivo debe ser de tipo: ' . implode(', ', $this->allowedExtensions);
        }

        // Verificar tamaño
        if ($file->getSize() > $this->maxSize) {
            $errors[] = 'El archivo no debe superar los 50MB.';
        }

        // Verificar MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            $errors[] = 'El tipo de archivo no es válido.';
        }

        return $errors;
    }

    /**
     * Obtener estadísticas de documentos
     */
    public function getStats(): array
    {
        return [
            'total' => Document::count(),
            'total_size' => Document::sum('file_size'),
            'by_type' => Document::selectRaw('file_type, COUNT(*) as count')
                ->groupBy('file_type')
                ->pluck('count', 'file_type')
                ->toArray(),
        ];
    }
}
