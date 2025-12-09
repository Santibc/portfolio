<?php

namespace App\Http\Controllers;

use App\Models\LandingPagina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPaginaController extends Controller
{
    public function edit($slug)
    {
        $pagina = LandingPagina::where('slug', $slug)->firstOrFail();

        // Determinar qué vista usar basado en el slug
        $vista = 'admin.landing.paginas.' . $slug;

        return view($vista, compact('pagina'));
    }

    public function update(Request $request, $slug)
    {
        $pagina = LandingPagina::where('slug', $slug)->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:200',
            'contenido' => 'required|array',
            'activo' => 'boolean',
        ]);

        // Procesar imágenes dentro del contenido
        $contenido = $request->contenido;

        // Lista de campos de imagen que pueden venir
        $imageFields = [
            'imagen_seccion_principal',
            'imagen_better_together',
            'imagen_beneficios',
            'mision_imagen',
            'vision_imagen',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $contenido[$field] = $this->uploadImage(
                    $request->file($field),
                    'images/landing/',
                    $pagina->contenido[$field] ?? null
                );
            }
        }

        $pagina->titulo = $request->titulo;
        $pagina->contenido = $contenido;
        $pagina->activo = $request->boolean('activo', true);
        $pagina->save();

        return redirect()->route('admin.landing.paginas.edit', $slug)
                        ->with('success', 'Página actualizada correctamente');
    }

    private function uploadImage($file, $directory, $oldPath = null)
    {
        // Eliminar imagen anterior si existe
        if ($oldPath && file_exists(public_path($oldPath))) {
            unlink(public_path($oldPath));
        }

        // Crear directorio si no existe
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Subir nueva imagen
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $filename);

        return $directory . $filename;
    }
}
