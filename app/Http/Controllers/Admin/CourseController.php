<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Category;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $categorias = Category::ordered()->get();

        $query = Course::with(['category', 'videos'])->withCount('videos')->orderBy('category_id')->orderBy('order');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Filtro por categoría (acepta 'categoria' o 'category')
        if ($request->filled('categoria')) {
            $query->where('category_id', $request->categoria);
        } elseif ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $cursos = $query->get();
        $stats = $this->courseService->getStats();

        return view('admin.courses.index', compact('cursos', 'categorias', 'stats'));
    }

    public function create()
    {
        $categorias = Category::active()->ordered()->get();
        return view('admin.courses.create', compact('categorias'));
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        $this->courseService->create($data);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso creado exitosamente.');
    }

    public function edit(Request $request, Course $course)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $course->id,
                'category_id' => $course->category_id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'thumbnail_url' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                'duration_hours' => $course->duration_hours,
                'is_published' => $course->is_published,
            ]);
        }

        $categorias = Category::ordered()->get();
        $course->load('videos');
        return view('admin.courses.edit', ['curso' => $course, 'categorias' => $categorias]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        $this->courseService->update($course, $data);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroy(Course $course)
    {
        $this->courseService->delete($course);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso eliminado exitosamente.');
    }

    public function togglePublish(Course $course)
    {
        $this->courseService->togglePublish($course);

        $status = $course->fresh()->is_published ? 'publicado' : 'despublicado';
        return redirect()->route('admin.cursos.index')
            ->with('success', "Curso {$status} exitosamente.");
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'order' => 'required|array',
            'order.*' => 'exists:courses,id',
        ]);

        $this->courseService->reorder($request->category_id, $request->order);

        return response()->json(['success' => true]);
    }
}
