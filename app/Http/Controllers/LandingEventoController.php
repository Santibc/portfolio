<?php

namespace App\Http\Controllers;

use App\Models\LandingEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingEventoController extends Controller
{
    public function index()
    {
        $eventos = LandingEvento::ordenados()->paginate(10);
        return view('admin.landing.eventos.index', compact('eventos'));
    }

    public function create()
    {
        $evento = new LandingEvento();
        return view('admin.landing.eventos.form', compact('evento'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'slug' => 'nullable|string|max:200|unique:landing_eventos,slug',
            'extracto' => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen' => 'nullable|image|max:3072',
            'fecha' => 'nullable|string|max:100',
            'hora' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:255',
            'precio' => 'nullable|string|max:100',
            'estado' => 'required|in:proximo,activo,finalizado',
            'incluye' => 'nullable|array',
            'galeria.*' => 'nullable|image|max:3072',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $evento = new LandingEvento();
        $evento->titulo = $request->titulo;
        $evento->slug = $request->slug ?: Str::slug($request->titulo);
        $evento->extracto = $request->extracto;
        $evento->contenido = $request->contenido;
        $evento->fecha = $request->fecha;
        $evento->hora = $request->hora;
        $evento->ubicacion = $request->ubicacion;
        $evento->precio = $request->precio;
        $evento->estado = $request->estado;
        $evento->incluye = $request->incluye ? array_filter($request->incluye) : null;
        $evento->orden = $request->orden ?? 0;
        $evento->activo = $request->boolean('activo', true);

        // Procesar imagen principal
        if ($request->hasFile('imagen')) {
            $evento->imagen = $this->uploadImage($request->file('imagen'), 'images/landing/eventos/');
        }

        // Procesar galería
        if ($request->hasFile('galeria')) {
            $galeria = [];
            foreach ($request->file('galeria') as $file) {
                $galeria[] = $this->uploadImage($file, 'images/landing/eventos/galeria/');
            }
            $evento->galeria = $galeria;
        }

        $evento->save();

        return redirect()->route('admin.landing.eventos.index')
                        ->with('success', 'Evento creado correctamente');
    }

    public function edit($id)
    {
        $evento = LandingEvento::findOrFail($id);
        return view('admin.landing.eventos.form', compact('evento'));
    }

    public function update(Request $request, $id)
    {
        $evento = LandingEvento::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'slug' => 'nullable|string|max:200|unique:landing_eventos,slug,' . $id,
            'extracto' => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen' => 'nullable|image|max:3072',
            'fecha' => 'nullable|string|max:100',
            'hora' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:255',
            'precio' => 'nullable|string|max:100',
            'estado' => 'required|in:proximo,activo,finalizado',
            'incluye' => 'nullable|array',
            'galeria.*' => 'nullable|image|max:3072',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $evento->titulo = $request->titulo;
        $evento->slug = $request->slug ?: Str::slug($request->titulo);
        $evento->extracto = $request->extracto;
        $evento->contenido = $request->contenido;
        $evento->fecha = $request->fecha;
        $evento->hora = $request->hora;
        $evento->ubicacion = $request->ubicacion;
        $evento->precio = $request->precio;
        $evento->estado = $request->estado;
        $evento->incluye = $request->incluye ? array_filter($request->incluye) : null;
        $evento->orden = $request->orden ?? 0;
        $evento->activo = $request->boolean('activo', true);

        // Procesar imagen principal
        if ($request->hasFile('imagen')) {
            $evento->imagen = $this->uploadImage($request->file('imagen'), 'images/landing/eventos/', $evento->imagen);
        }

        // Procesar nuevas imágenes de galería
        if ($request->hasFile('galeria')) {
            $galeria = $evento->galeria ?? [];
            foreach ($request->file('galeria') as $file) {
                $galeria[] = $this->uploadImage($file, 'images/landing/eventos/galeria/');
            }
            $evento->galeria = $galeria;
        }

        // Eliminar imágenes de galería marcadas
        if ($request->has('eliminar_galeria')) {
            $galeria = $evento->galeria ?? [];
            foreach ($request->eliminar_galeria as $index) {
                if (isset($galeria[$index])) {
                    if (file_exists(public_path($galeria[$index]))) {
                        unlink(public_path($galeria[$index]));
                    }
                    unset($galeria[$index]);
                }
            }
            $evento->galeria = array_values($galeria);
        }

        $evento->save();

        return redirect()->route('admin.landing.eventos.index')
                        ->with('success', 'Evento actualizado correctamente');
    }

    public function destroy($id)
    {
        $evento = LandingEvento::findOrFail($id);

        // Eliminar imagen principal
        if ($evento->imagen && file_exists(public_path($evento->imagen))) {
            unlink(public_path($evento->imagen));
        }

        // Eliminar galería
        if ($evento->galeria) {
            foreach ($evento->galeria as $img) {
                if (file_exists(public_path($img))) {
                    unlink(public_path($img));
                }
            }
        }

        $evento->delete();

        return redirect()->route('admin.landing.eventos.index')
                        ->with('success', 'Evento eliminado correctamente');
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
