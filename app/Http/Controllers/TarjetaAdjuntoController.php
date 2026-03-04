<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use App\Models\TarjetaAdjunto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TarjetaAdjuntoController extends Controller
{
    public function store(Request $request, Tarjeta $tarjeta)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240', // 10MB
        ]);

        $archivo = $request->file('archivo');
        $tableroId = $tarjeta->columna->tablero_id;
        $directorio = "tableros/{$tableroId}/{$tarjeta->id}";
        $rutaBase = public_path("uploads/{$directorio}");

        if (!File::exists($rutaBase)) {
            File::makeDirectory($rutaBase, 0755, true);
        }

        // Capture file info before move (move invalidates UploadedFile)
        $nombreOriginal = $archivo->getClientOriginalName();
        $mimeType = $archivo->getClientMimeType();
        $tamano = $archivo->getSize();
        $nombreArchivo = time() . '_' . $nombreOriginal;

        $archivo->move($rutaBase, $nombreArchivo);

        $adjunto = $tarjeta->adjuntos()->create([
            'user_id' => auth()->id(),
            'nombre_original' => $nombreOriginal,
            'ruta_archivo' => "{$directorio}/{$nombreArchivo}",
            'mime_type' => $mimeType,
            'tamano' => $tamano,
        ]);

        return response()->json([
            'success' => true,
            'adjunto' => $adjunto,
            'url' => $adjunto->url,
        ]);
    }

    public function destroy(TarjetaAdjunto $adjunto)
    {
        $rutaCompleta = public_path("uploads/{$adjunto->ruta_archivo}");
        if (File::exists($rutaCompleta)) {
            File::delete($rutaCompleta);
        }

        $adjunto->delete();
        return response()->json(['success' => true]);
    }

    public function descargar(TarjetaAdjunto $adjunto)
    {
        $rutaCompleta = public_path("uploads/{$adjunto->ruta_archivo}");

        if (!File::exists($rutaCompleta)) {
            abort(404);
        }

        return response()->download($rutaCompleta, $adjunto->nombre_original);
    }
}
