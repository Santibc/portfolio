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
        $categoryFilter = $request->get('category');

        $query = Course::with(['category', 'videos'])->orderBy('category_id')->orderBy('order');

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        $cursos = $query->get();
        $stats = $this->courseService->getStats();

        return view('admin.courses.index', compact('cursos', 'categorias', 'stats', 'categoryFilter'));
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

    public function edit(Course $curso)
    {
        $categorias = Category::ordered()->get();
        $curso->load('videos');
        return view('admin.courses.edit', compact('curso', 'categorias'));
    }

    public function update(UpdateCourseRequest $request, Course $curso)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        $this->courseService->update($curso, $data);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroy(Course $curso)
    {
        $this->courseService->delete($curso);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso eliminado exitosamente.');
    }

    public function togglePublish(Course $curso)
    {
        $this->courseService->togglePublish($curso);

        $status = $curso->fresh()->is_published ? 'publicado' : 'despublicado';
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
