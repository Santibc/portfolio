<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Obtener todas las categorías ordenadas
     */
    public function getAllOrdered(): Collection
    {
        return Category::ordered()->get();
    }

    /**
     * Obtener categorías activas ordenadas
     */
    public function getActiveOrdered(): Collection
    {
        return Category::active()->ordered()->get();
    }

    /**
     * Crear una nueva categoría
     */
    public function create(array $data): Category
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return Category::create($data);
    }

    /**
     * Actualizar una categoría
     */
    public function update(Category $category, array $data): Category
    {
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Eliminar imagen anterior
            $this->deleteImage($category->image);
            $data['image'] = $this->uploadImage($data['image']);
        }

        $category->update($data);
        return $category->fresh();
    }

    /**
     * Eliminar una categoría
     */
    public function delete(Category $category): bool
    {
        // Eliminar imagen si existe
        $this->deleteImage($category->image);

        return $category->delete();
    }

    /**
     * Reordenar categorías
     */
    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Category::where('id', $id)->update(['order' => $index + 1]);
        }
    }

    /**
     * Subir imagen de categoría
     */
    protected function uploadImage(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'images/categories';

        // Asegurar que el directorio existe
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $file->move(public_path($path), $filename);

        return $path . '/' . $filename;
    }

    /**
     * Eliminar imagen de categoría
     */
    protected function deleteImage(?string $imagePath): void
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
    }

    /**
     * Generar slug único
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = Category::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;

            $query = Category::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Obtener estadísticas de categorías
     */
    public function getStats(): array
    {
        return [
            'total' => Category::count(),
            'active' => Category::active()->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'with_courses' => Category::has('courses')->count(),
        ];
    }
}
