<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Models\Course;
use App\Models\Video;
use App\Services\VideoUploadService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    protected VideoUploadService $videoService;

    public function __construct(VideoUploadService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index(Course $course)
    {
        $curso = $course;
        $videos = $this->videoService->getVideosForCourse($course);
        return view('admin.videos.index', compact('curso', 'videos'));
    }

    public function create(Course $course)
    {
        $curso = $course;
        return view('admin.videos.create', compact('curso'));
    }

    public function store(StoreVideoRequest $request, Course $course)
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }

        $this->videoService->create($course, $data);

        return redirect()->route('admin.cursos.videos.index', $course)
            ->with('success', 'Video subido exitosamente.');
    }

    public function edit(Course $course, Video $video)
    {
        $curso = $course;
        return view('admin.videos.edit', compact('video', 'curso'));
    }

    public function update(UpdateVideoRequest $request, Course $course, Video $video)
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }

        $this->videoService->update($video, $data);

        return redirect()->route('admin.cursos.videos.index', $course)
            ->with('success', 'Video actualizado exitosamente.');
    }

    public function destroy(Course $course, Video $video)
    {
        $this->videoService->delete($video);

        return redirect()->route('admin.cursos.videos.index', $course)
            ->with('success', 'Video eliminado exitosamente.');
    }

    public function reorder(Request $request, Course $course)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:videos,id',
        ]);

        $this->videoService->reorder($course, $request->order);

        return response()->json(['success' => true]);
    }
}
