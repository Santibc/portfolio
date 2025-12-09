<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categorias = $this->categoryService->getAllOrdered();
        $stats = $this->categoryService->getStats();
        return view('admin.categories.index', compact('categorias', 'stats'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $this->categoryService->create($data);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Request $request, Category $category)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'color' => $category->color,
                'is_active' => $category->is_active,
            ]);
        }

        return view('admin.categories.edit', ['categoria' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $this->categoryService->update($category, $data);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Category $category)
    {
        // Verificar si tiene cursos
        if ($category->courses()->count() > 0) {
            return redirect()->route('admin.categorias.index')
                ->with('error', 'No se puede eliminar una categoría que tiene cursos asociados.');
        }

        $this->categoryService->delete($category);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:categories,id',
        ]);

        $this->categoryService->reorder($request->order);

        return response()->json(['success' => true]);
    }

    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activada' : 'desactivada';
        return redirect()->route('admin.categorias.index')
            ->with('success', "Categoría {$status} exitosamente.");
    }
}
