<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    /**
     * Obtener todos los cursos ordenados
     */
    public function getAllOrdered(): Collection
    {
        return Course::with('category')->orderBy('category_id')->orderBy('order')->get();
    }

    /**
     * Obtener cursos publicados ordenados
     */
    public function getPublishedOrdered(): Collection
    {
        return Course::with('category')
            ->published()
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();
    }

    /**
     * Obtener cursos de una categoría
     */
    public function getByCategory(Category $category, bool $onlyPublished = true): Collection
    {
        $query = $category->courses()->with('videos');

        if ($onlyPublished) {
            $query->published();
        }

        return $query->ordered()->get();
    }

    /**
     * Crear un nuevo curso
     */
    public function create(array $data): Course
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $data['thumbnail'] = $this->uploadThumbnail($data['thumbnail']);
        }

        return Course::create($data);
    }

    /**
     * Actualizar un curso
     */
    public function update(Course $course, array $data): Course
    {
        if (isset($data['title']) && $data['title'] !== $course->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $course->id);
        }

        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            // Eliminar thumbnail anterior
            $this->deleteThumbnail($course->thumbnail);
            $data['thumbnail'] = $this->uploadThumbnail($data['thumbnail']);
        }

        $course->update($data);
        return $course->fresh();
    }

    /**
     * Eliminar un curso
     */
    public function delete(Course $course): bool
    {
        // Eliminar thumbnail si existe
        $this->deleteThumbnail($course->thumbnail);

        return $course->delete();
    }

    /**
     * Toggle estado de publicación
     */
    public function togglePublish(Course $course): Course
    {
        $course->update(['is_published' => !$course->is_published]);
        return $course->fresh();
    }

    /**
     * Reordenar cursos dentro de una categoría
     */
    public function reorder(int $categoryId, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Course::where('id', $id)
                ->where('category_id', $categoryId)
                ->update(['order' => $index + 1]);
        }
    }

    /**
     * Subir thumbnail del curso
     */
    protected function uploadThumbnail(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'images/courses';

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $file->move(public_path($path), $filename);

        return $path . '/' . $filename;
    }

    /**
     * Eliminar thumbnail del curso
     */
    protected function deleteThumbnail(?string $thumbnailPath): void
    {
        if ($thumbnailPath && file_exists(public_path($thumbnailPath))) {
            unlink(public_path($thumbnailPath));
        }
    }

    /**
     * Generar slug único
     */
    protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = Course::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;

            $query = Course::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Obtener cursos con progreso para un usuario
     */
    public function getCoursesWithProgressForUser(User $user, ?Category $category = null): Collection
    {
        $query = Course::with(['category', 'videos'])->published();

        if ($category) {
            $query->where('category_id', $category->id);
        }

        $courses = $query->orderBy('category_id')->orderBy('order')->get();

        return $courses->map(function ($course) use ($user) {
            $course->user_progress = $user->getCourseProgressPercentage($course);
            $course->is_completed = $user->hasCourseCompleted($course);
            $course->can_access = $user->canAccessCourse($course);
            return $course;
        });
    }

    /**
     * Obtener estadísticas de cursos
     */
    public function getStats(): array
    {
        return [
            'total' => Course::count(),
            'published' => Course::published()->count(),
            'unpublished' => Course::where('is_published', false)->count(),
            'with_videos' => Course::has('videos')->count(),
        ];
    }

    /**
     * Buscar cursos
     */
    public function search(string $query): Collection
    {
        return Course::with('category')
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('title')
            ->get();
    }
}
