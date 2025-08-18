<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\CarruselEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmpresasController extends Controller
{
    /**
     * Mostrar el perfil de la empresa o redirigir al formulario de creación
     */
    public function index()
    {
        $empresa = auth()->user()->empresa;

        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('info', 'Primero debe crear su empresa para continuar.');
        }

        // Cargar imágenes del carrusel
        $empresa->load('carruselImagenesActivas');

        // Estadísticas básicas
        $estadisticas = [
            'total_productos'   => $empresa->productos()->count(),
            'productos_activos' => $empresa->productos()->where('activo', true)->count(),
            'total_compras'     => $empresa->compras()->count(),
            'compras_mes'       => $empresa->compras()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_clientes'    => $empresa->clientes()->count(),
            'ventas_mes'        => $empresa->compras()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('estado', 'pagada')
                ->sum('total'),
        ];

        return view('empresa.perfil', compact('empresa', 'estadisticas'));
    }

    /**
     * Mostrar formulario de creación/edición
     */
    public function form()
    {
        $empresa = auth()->user()->empresa;

        // Si ya tiene empresa, es edición
        if ($empresa) {
            $empresa->load('carruselImagenes');
        } else {
            $empresa = new Empresa();
        }

        return view('empresa.form', compact('empresa'));
    }

    /**
     * Guardar o actualizar la empresa
     * - Guarda archivos directamente en /public sin usar storage:link
     */
    public function guardar(Request $request)
    {
        $empresa = auth()->user()->empresa ?? new Empresa();

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'slug'   => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('empresas')->ignore($empresa->id),
            ],
            'descripcion'    => ['nullable', 'string', 'max:500'],
            'email'          => ['nullable', 'email', 'max:255'],
            'telefono'       => ['nullable', 'string', 'max:255'],
            'direccion'      => ['nullable', 'string', 'max:255'],
            'instagram_url'  => ['nullable', 'url', 'max:255'],
            'facebook_url'   => ['nullable', 'url', 'max:255'],
            'twitter_url'    => ['nullable', 'url', 'max:255'],
            'whatsapp'       => ['nullable', 'string', 'max:255'],
            'logo'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            // Horario de atención
            'horario_atencion'               => ['nullable', 'array'],
            'horario_atencion.*.apertura'    => ['nullable', 'date_format:H:i', 'exclude_if:horario_atencion.*.cerrado,1'],
            'horario_atencion.*.cierre'      => ['nullable', 'date_format:H:i', 'exclude_if:horario_atencion.*.cerrado,1', 'after:horario_atencion.*.apertura'],
            'horario_atencion.*.cerrado'     => ['nullable', 'boolean'],


            // Carrusel
            'carrusel.*.imagen'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'carrusel.*.titulo'      => ['nullable', 'string', 'max:255'],
            'carrusel.*.descripcion' => ['nullable', 'string'],
            'carrusel.*.link'        => ['nullable', 'url', 'max:255'],
            'carrusel.*.orden'       => ['nullable', 'integer'],
            'carrusel.*.fecha_inicio'=> ['nullable', 'date'],
            'carrusel.*.fecha_fin'   => ['nullable', 'date', 'after_or_equal:carrusel.*.fecha_inicio'],
        ];

        $messages = [
            'nombre.required'              => 'El nombre de la empresa es obligatorio.',
            'slug.unique'                  => 'Esta URL ya está en uso.',
            'email.email'                  => 'Ingrese un correo electrónico válido.',
            'logo.image'                   => 'El logo debe ser una imagen.',
            'logo.max'                     => 'El logo no debe superar 2MB.',
            'imagen_portada.max'           => 'La imagen de portada no debe superar 4MB.',
            'instagram_url.url'            => 'Ingrese una URL válida de Instagram.',
            'facebook_url.url'             => 'Ingrese una URL válida de Facebook.',
            'twitter_url.url'              => 'Ingrese una URL válida de Twitter.',
            'carrusel.*.imagen.image'      => 'El archivo debe ser una imagen.',
            'carrusel.*.imagen.max'        => 'La imagen del carrusel no debe superar 4MB.',
            'carrusel.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'horario_atencion.*.apertura.date_format' => 'Ingrese una hora de apertura válida (HH:MM).',
            'horario_atencion.*.cierre.date_format'   => 'Ingrese una hora de cierre válida (HH:MM).',
            'horario_atencion.*.cierre.after'         => 'La hora de cierre debe ser posterior a la hora de apertura.',

        ];

        $data = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // Generar slug si no se proporciona
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['nombre']);

                // Asegurar que sea único
                $count = Empresa::where('slug', 'like', $data['slug'] . '%')
                    ->when($empresa->id, fn($q) => $q->where('id', '!=', $empresa->id))
                    ->count();

                if ($count > 0) {
                    $data['slug'] = $data['slug'] . '-' . ($count + 1);
                }
            }

            // Procesar horario de atención (array a estructura guardable)
            if ($request->has('horario_atencion')) {
                $horario = [];
                foreach ($request->horario_atencion as $dia => $info) {
                    if (!isset($info['cerrado']) || !$info['cerrado']) {
                        $horario[$dia] = [
                            'apertura' => $info['apertura'] ?? '09:00',
                            'cierre'   => $info['cierre'] ?? '18:00',
                            'cerrado'  => false,
                        ];
                    } else {
                        $horario[$dia] = ['cerrado' => true];
                    }
                }
                $data['horario_atencion'] = $horario;
            }

            // Asignar usuario si es nueva
            $esNueva = !$empresa->exists;
            if ($esNueva) {
                $data['usuario_id'] = auth()->id();
                $data['activo'] = true;
                $data['porcentaje_comision'] = 10.00; // Valor por defecto
            }

            // No intentes guardar archivos en $data antes de moverlos
            unset($data['logo'], $data['imagen_portada'], $data['carrusel'], $data['carrusel_existente']);

            // Guardar/actualizar datos básicos primero para tener ID
            $empresa->fill($data)->save();

            // Rutas base en /public
            $baseDir = public_path('imagenes/empresas/' . $empresa->id);
            $logoDir = $baseDir . '/logo';
            $portadaDir = $baseDir . '/portada';
            $carruselDir = $baseDir . '/carrusel';

            // Crear directorios si no existen
            foreach ([$baseDir, $logoDir, $portadaDir, $carruselDir] as $dir) {
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
            }

            // ---- LOGO (mover a /public/imagenes/empresas/{id}/logo) ----
            if ($request->hasFile('logo')) {
                // Eliminar logo anterior si existe (intenta en public y en storage público)
                if (!empty($empresa->logo)) {
                    $posiblesRutas = [
                        public_path($empresa->logo),
                        storage_path('app/public/' . ltrim($empresa->logo, '/')),
                    ];
                    foreach ($posiblesRutas as $ruta) {
                        if (File::exists($ruta)) {
                            File::delete($ruta);
                        }
                    }
                }

                $logo = $request->file('logo');
                $logoFilename = time() . '_' . uniqid() . '_' . preg_replace('/\s+/', '_', $logo->getClientOriginalName());
                $logo->move($logoDir, $logoFilename);

                // Guardar ruta relativa a /public
                $empresa->logo = 'imagenes/empresas/' . $empresa->id . '/logo/' . $logoFilename;
                $empresa->save();
            }

            // ---- IMAGEN DE PORTADA (mover a /public/imagenes/empresas/{id}/portada) ----
            if ($request->hasFile('imagen_portada')) {
                if (!empty($empresa->imagen_portada)) {
                    $posiblesRutas = [
                        public_path($empresa->imagen_portada),
                        storage_path('app/public/' . ltrim($empresa->imagen_portada, '/')),
                    ];
                    foreach ($posiblesRutas as $ruta) {
                        if (File::exists($ruta)) {
                            File::delete($ruta);
                        }
                    }
                }

                $portada = $request->file('imagen_portada');
                $portadaFilename = time() . '_' . uniqid() . '_' . preg_replace('/\s+/', '_', $portada->getClientOriginalName());
                $portada->move($portadaDir, $portadaFilename);

                $empresa->imagen_portada = 'imagenes/empresas/' . $empresa->id . '/portada/' . $portadaFilename;
                $empresa->save();
            }

            // ---- IMÁGENES NUEVAS DEL CARRUSEL (mover a /public/imagenes/empresas/{id}/carrusel) ----
            if ($request->has('carrusel')) {
                foreach ($request->carrusel as $index => $carruselData) {
                    if (isset($carruselData['imagen']) && $carruselData['imagen']) {
                        $imagen = $carruselData['imagen'];

                        $filename = time() . '_' . uniqid() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                        $imagen->move($carruselDir, $filename);

                        $path = 'imagenes/empresas/' . $empresa->id . '/carrusel/' . $filename;

                        CarruselEmpresa::create([
                            'empresa_id'   => $empresa->id,
                            'imagen'       => $path,
                            'titulo'       => $carruselData['titulo'] ?? null,
                            'descripcion'  => $carruselData['descripcion'] ?? null,
                            'link'         => $carruselData['link'] ?? null,
                            'orden'        => $carruselData['orden'] ?? $index,
                            'fecha_inicio' => $carruselData['fecha_inicio'] ?? null,
                            'fecha_fin'    => $carruselData['fecha_fin'] ?? null,
                            'activo'       => true,
                        ]);
                    }
                }
            }

            // ---- ACTUALIZAR/ELIMINAR IMÁGENES EXISTENTES DEL CARRUSEL ----
            if ($request->has('carrusel_existente')) {
                foreach ($request->carrusel_existente as $id => $carruselData) {
                    $carruselImagen = CarruselEmpresa::find($id);

                    if ($carruselImagen && $carruselImagen->empresa_id == $empresa->id) {
                        if (isset($carruselData['eliminar']) && $carruselData['eliminar']) {
                            // Eliminar archivo físico
                            $posiblesRutas = [
                                public_path($carruselImagen->imagen),
                                storage_path('app/public/' . ltrim($carruselImagen->imagen, '/')),
                            ];
                            foreach ($posiblesRutas as $ruta) {
                                if (File::exists($ruta)) {
                                    File::delete($ruta);
                                }
                            }

                            $carruselImagen->delete();
                        } else {
                            // Nota: Si quisieras permitir reemplazar imagen aquí, podrías
                            // chequear $carruselData['imagen'] como UploadedFile y moverla.
                            $carruselImagen->update([
                                'titulo'       => $carruselData['titulo'] ?? $carruselImagen->titulo,
                                'descripcion'  => $carruselData['descripcion'] ?? $carruselImagen->descripcion,
                                'link'         => $carruselData['link'] ?? $carruselImagen->link,
                                'orden'        => $carruselData['orden'] ?? $carruselImagen->orden,
                                'fecha_inicio' => $carruselData['fecha_inicio'] ?? $carruselImagen->fecha_inicio,
                                'fecha_fin'    => $carruselData['fecha_fin'] ?? $carruselImagen->fecha_fin,
                                'activo'       => array_key_exists('activo', $carruselData) ? (bool)$carruselData['activo'] : $carruselImagen->activo,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $mensaje = $esNueva
                ? '¡Empresa creada exitosamente! Ahora puede agregar productos.'
                : 'Empresa actualizada correctamente.';

            return redirect()->route('empresa.index')->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al guardar la empresa: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de la empresa (activar/desactivar)
     */
    public function cambiarEstado(Request $request)
    {
        $empresa = auth()->user()->empresa;

        if (!$empresa) {
            return response()->json(['error' => 'No tiene empresa registrada'], 404);
        }

        $empresa->activo = !$empresa->activo;
        $empresa->save();

        return response()->json([
            'success' => true,
            'activo'  => $empresa->activo,
            'mensaje' => $empresa->activo ? 'Empresa activada' : 'Empresa desactivada',
        ]);
    }

    /**
     * Vista previa de la tienda
     */
    public function preview()
    {
        $empresa = auth()->user()->empresa;

        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('error', 'Debe crear su empresa primero.');
        }

        return redirect()
            ->route('tienda.empresa', $empresa->slug)
            ->with('info', 'Esta es la vista previa de su tienda.');
    }
}
