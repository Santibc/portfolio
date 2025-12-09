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

    public function index(Course $curso)
    {
        $videos = $this->videoService->getVideosForCourse($curso);
        return view('admin.videos.index', compact('curso', 'videos'));
    }

    public function create(Course $curso)
    {
        return view('admin.videos.create', compact('curso'));
    }

    public function store(StoreVideoRequest $request, Course $curso)
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }

        $this->videoService->create($curso, $data);

        return redirect()->route('admin.cursos.videos.index', $curso)
            ->with('success', 'Video subido exitosamente.');
    }

    public function edit(Video $video)
    {
        $curso = $video->course;
        return view('admin.videos.edit', compact('video', 'curso'));
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }

        $this->videoService->update($video, $data);

        return redirect()->route('admin.cursos.videos.index', $video->course)
            ->with('success', 'Video actualizado exitosamente.');
    }

    public function destroy(Video $video)
    {
        $curso = $video->course;
        $this->videoService->delete($video);

        return redirect()->route('admin.cursos.videos.index', $curso)
            ->with('success', 'Video eliminado exitosamente.');
    }

    public function reorder(Request $request, Course $curso)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:videos,id',
        ]);

        $this->videoService->reorder($curso, $request->order);

        return response()->json(['success' => true]);
    }
}
