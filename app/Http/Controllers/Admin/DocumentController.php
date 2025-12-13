<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Course;
use App\Models\Document;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected DocumentUploadService $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index(Course $course)
    {
        $curso = $course;
        $documents = $this->documentService->getDocumentsForCourse($course);
        return view('admin.documents.index', compact('curso', 'documents'));
    }

    public function create(Course $course)
    {
        $curso = $course;
        return view('admin.documents.create', compact('curso'));
    }

    public function store(StoreDocumentRequest $request, Course $course)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file');
        }

        try {
            $this->documentService->create($course, $data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Documento subido exitosamente.']);
            }

            return redirect()->route('admin.cursos.documents.index', $course)
                ->with('success', 'Documento subido exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al subir el documento: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error al subir el documento.');
        }
    }

    public function edit(Course $course, Document $document)
    {
        $curso = $course;
        return view('admin.documents.edit', compact('document', 'curso'));
    }

    public function update(UpdateDocumentRequest $request, Course $course, Document $document)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file');
        }

        $this->documentService->update($document, $data);

        return redirect()->route('admin.cursos.documents.index', $course)
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Course $course, Document $document)
    {
        $this->documentService->delete($document);

        return redirect()->route('admin.cursos.documents.index', $course)
            ->with('success', 'Documento eliminado exitosamente.');
    }

    public function reorder(Request $request, Course $course)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:documents,id',
        ]);

        $this->documentService->reorder($course, $request->order);

        return response()->json(['success' => true]);
    }

    public function download(Course $course, Document $document)
    {
        $filePath = public_path($document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($filePath, $document->file_name);
    }
}
