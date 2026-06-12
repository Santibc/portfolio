<?php

namespace App\Http\Controllers;
use Excel;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ImagenProducto;
use App\Models\PrecioProducto;
use App\Models\ActualizacionPrecio;
use App\Imports\PreciosImport;
use App\Models\ListaPrecio;
use App\Models\VarianteProducto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use App\Models\Ubicacion;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\Siigo\SiigoConfigService;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Producto::with(['categoria', 'imagenPrincipal', 'todasImagenes', 'stockPrincipal.ubicacionRelacion'])
                            ->where('eliminado', false)
                            ->select('productos.*');

            return DataTables::of($query)
                ->addColumn('marca', fn($p) => $p->marca ?: '-')
                ->addColumn('categoria', fn($p) => $p->categoria?->nombre)
                ->addColumn('imagen', function($p) {
                    $imagen = $p->mejor_imagen;
                    $url = $imagen
                        ? asset($imagen->ruta_imagen)
                        : asset('images/no-image.png');
                    return '<img src="'.$url.'" class="img-thumbnail" style="width:50px;">';
                })
                ->addColumn('stock', function($p) {
                    if (!$p->controlar_stock) {
                        return '<span class="badge bg-secondary">No controlado</span>';
                    }

                    $stockDisponible = $p->stock_disponible;
                    $badge = 'success';

                    if ($stockDisponible <= 0) {
                        $badge = 'danger';
                    } elseif ($p->tiene_stock_bajo) {
                        $badge = 'warning';
                    }

                    return '<span class="badge bg-'.$badge.'">' . $stockDisponible . '</span>';
                })
                ->addColumn('ubicacion', function($p) {
                    if ($p->stockPrincipal && $p->stockPrincipal->ubicacionRelacion) {
                        return $p->stockPrincipal->ubicacionRelacion->nombre;
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('variantes', fn($p) => $p->tiene_variantes ? 'Sí' : 'No')
                ->addColumn('activo', fn($p) => $p->activo ? 'Sí' : 'No')
                ->addColumn('fecha_vencimiento_fmt', function($p) {
                    return $p->fecha_vencimiento ? $p->fecha_vencimiento->format('d/m/Y') : '-';
                })
                ->addColumn('action', function($p) {
                    $url = route('productos.form', $p->id);

                    $buttons = '<div class="d-flex justify-content-center gap-1">';
                    $buttons .= '<a href="'.$url.'" class="btn btn-outline-info btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>';

                    // Botón de variantes si tiene
                    if ($p->tiene_variantes) {
                        $buttons .= '<button type="button" class="btn btn-outline-secondary btn-sm" title="Ver Variantes" onclick="verVariantes('.$p->id.')"><i class="bi bi-list-ul"></i></button>';
                    }

                    // Botón de imágenes
                    $buttons .= '<button type="button" class="btn btn-outline-primary btn-sm" title="Ver Imágenes" onclick="verImagenes('.$p->id.')"><i class="bi bi-image"></i></button>';

                    // Botón de precios
                    $buttons .= '<button type="button" class="btn btn-outline-success btn-sm" title="Ver Precios" onclick="verPrecios('.$p->id.')"><i class="bi bi-currency-dollar"></i></button>';

                    // Botón de stock (NUEVO)
                    if ($p->controlar_stock) {
                        $buttons .= '<button type="button" class="btn btn-outline-warning btn-sm" title="Ver Stock" onclick="verStock('.$p->id.')"><i class="bi bi-box-seam"></i></button>';
                    }

                    // Botón de eliminar
                    $buttons .= '<button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="eliminarProducto('.$p->id.')"><i class="bi bi-trash"></i></button>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->filterColumn('categoria', function($query, $keyword) {
                    $query->whereHas('categoria', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('marca', function($query, $keyword) {
                    $query->where('marca', 'like', "%{$keyword}%");
                })
                ->filterColumn('ubicacion', function($query, $keyword) {
                    $query->whereHas('stockPrincipal.ubicacionRelacion', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['imagen', 'stock', 'ubicacion', 'action'])
                ->make(true);
        }

        return view('productos.productos_index');
    }

    public function form(Producto $producto = null)
    {
        $producto = $producto ?? new Producto();
        $categorias = Categoria::activas()->pluck('nombre', 'id');
        $listas = ListaPrecio::activas()->get();
        $ubicaciones = Ubicacion::activas()->get();

        // Cargar stock si el producto existe (NUEVO)
        $stocks = [];
        if ($producto->exists) {
            if ($producto->tiene_variantes) {
                $stocks = $producto->stock()->with('variante')->get();
            } else {
                $stock = $producto->stockPrincipal;
                if ($stock) {
                    $stocks = [$stock];
                }
            }
        }

        return view('productos.productos_form', compact('producto', 'categorias', 'listas', 'stocks', 'ubicaciones'));
    }

    public function guardar(Request $request)
    {
        $producto = $request->id
                  ? Producto::findOrFail($request->id)
                  : new Producto();

        $rules = [
            'referencia' => [
                'required','string','max:255',
                Rule::unique('productos')->ignore($producto->id)
            ],
            'nombre' => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'unidad_venta' => ['required','string','max:100'],
            'unidad_empaque' => ['required','string','max:100'],
            'extension' => ['nullable','string','max:100'],
            'fecha_vencimiento' => ['nullable','date'],
            'categoria_id' => ['required','exists:categorias,id'],
            'controlar_stock' => ['boolean'],  // NUEVO
            'permitir_venta_sin_stock' => ['boolean'],  // NUEVO
            'imagenes.*' => ['nullable','image','mimes:jpeg,png,jpg,webp','max:2048'],
            'variantes.*.id' => ['nullable','integer','exists:variantes_productos,id'],
            'variantes.*.referencia_variante' => ['nullable','string','max:50'],
            'variantes.*.color' => ['nullable','string','max:50'],
            'variantes.*.sku' => ['nullable','string','max:255'],
            'variantes.*.stock_inicial' => ['nullable','integer','min:0'],  // NUEVO
            'variantes.*.stock_minimo' => ['nullable','integer','min:0'],  // NUEVO
            'variantes.*.stock_maximo' => ['nullable','integer','min:0'],  // NUEVO
            'variantes.*.ubicacion' => ['nullable','string','max:255'],  // NUEVO
            'precios.*' => ['nullable','numeric','min:0'],
            'stock_inicial' => ['nullable','integer','min:0'],  // NUEVO
            'stock_minimo' => ['nullable','integer','min:0'],  // NUEVO
            'stock_maximo' => ['nullable','integer','min:0'],  // NUEVO
            'ubicacion_id' => ['nullable','exists:ubicaciones,id'],  // Ubicación seleccionada
            'ubicacion' => ['nullable','string','max:255'],  // Ubicación específica
        ];

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'max' => 'No debe superar los :max caracteres.',
            'unique' => 'Ya existe un producto con esta referencia.',
            'exists' => 'La categoría seleccionada no es válida.',
            'imagenes.*.image' => 'El archivo debe ser una imagen.',
            'imagenes.*.mimes' => 'La imagen debe ser JPG, PNG o WebP.',
            'imagenes.*.max' => 'La imagen no debe superar 2MB.',
            'precios.*.numeric' => 'El precio debe ser un número.',
            'precios.*.min' => 'El precio no puede ser negativo.',
            'stock_inicial.integer' => 'El stock debe ser un número entero.',  // NUEVO
            'stock_inicial.min' => 'El stock no puede ser negativo.',  // NUEVO
        ];

        $data = $request->validate($rules, $messages);
        
        DB::beginTransaction();
        
        try {
            // Guardar datos básicos del producto
            $data['tiene_variantes'] = $request->input('tiene_variantes', 0) == 1;
            $data['controlar_stock'] = $request->input('controlar_stock', 1) == 1;  // NUEVO
            $data['permitir_venta_sin_stock'] = $request->input('permitir_venta_sin_stock', 0) == 1;  // NUEVO
            $data['activo'] = true;
            
            $esNuevo = !$producto->exists;  // NUEVO
            $producto->fill($data)->save();
            
            // Si el producto dejó de tener variantes, eliminar las existentes
            if (!$producto->tiene_variantes && $request->id) {
                $variantesExistentes = $producto->variantes()->pluck('id');
                if ($variantesExistentes->isNotEmpty()) {
                    // Desasociar imágenes de las variantes
                    ImagenProducto::where('producto_id', $producto->id)
                        ->whereIn('variante_producto_id', $variantesExistentes)
                        ->update(['variante_producto_id' => null]);

                    // Eliminar stock de variantes
                    StockProducto::whereIn('variante_producto_id', $variantesExistentes)
                        ->where('producto_id', $producto->id)
                        ->delete();

                    // Eliminar las variantes
                    VarianteProducto::whereIn('id', $variantesExistentes)->delete();
                }
            }

            // Guardar variantes
            if ($producto->tiene_variantes && $request->has('variantes')) {
                // Recoger IDs de variantes enviadas desde el formulario
                $idsEnviados = collect($request->variantes)
                    ->pluck('id')
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->values();

                // Si es edición, sincronizar variantes (preservando stock)
                if ($request->id) {
                    $idsExistentes = $producto->variantes()->pluck('id');
                    $idsAEliminar = $idsExistentes->diff($idsEnviados);

                    // Eliminar solo las variantes que el usuario quitó
                    if ($idsAEliminar->isNotEmpty()) {
                        ImagenProducto::where('producto_id', $producto->id)
                            ->whereIn('variante_producto_id', $idsAEliminar)
                            ->update(['variante_producto_id' => null]);

                        StockProducto::whereIn('variante_producto_id', $idsAEliminar)
                                     ->where('producto_id', $producto->id)
                                     ->delete();

                        VarianteProducto::whereIn('id', $idsAEliminar)->delete();
                    }
                }

                foreach ($request->variantes as $index => $varianteData) {
                    if (!empty($varianteData['referencia_variante']) || !empty($varianteData['color']) || !empty($varianteData['sku'])) {
                        // Generar SKU si no se proporciona
                        $sku = $varianteData['sku'];
                        if (empty($sku)) {
                            $sku = $producto->referencia;
                            if (!empty($varianteData['referencia_variante'])) {
                                $sku .= '-' . strtoupper(str_replace(' ', '', $varianteData['referencia_variante']));
                            }
                            if (!empty($varianteData['color'])) {
                                $sku .= '-' . strtoupper(str_replace(' ', '', $varianteData['color']));
                            }
                            if (empty($varianteData['referencia_variante']) && empty($varianteData['color'])) {
                                $count = $producto->variantes()->count() + 1;
                                $sku .= '-VAR' . $count;
                            }
                        }

                        // Si tiene ID, es variante existente → actualizar sin tocar stock
                        if (!empty($varianteData['id'])) {
                            $variante = VarianteProducto::find($varianteData['id']);
                            if ($variante && $variante->producto_id == $producto->id) {
                                $variante->update([
                                    'referencia_variante' => $varianteData['referencia_variante'],
                                    'color' => $varianteData['color'],
                                    'sku' => $sku,
                                ]);
                            }
                        } else {
                            // Variante nueva → crear con stock
                            $variante = $producto->variantes()->create([
                                'referencia_variante' => $varianteData['referencia_variante'],
                                'color' => $varianteData['color'],
                                'sku' => $sku,
                                'activo' => true
                            ]);

                            if ($producto->controlar_stock) {
                                $stockInicial = $varianteData['stock_inicial'] ?? 0;
                                // Sin ubicación explícita, el stock inicial va a la bodega principal (nunca NULL).
                                $ubicacionId = $varianteData['ubicacion_id'] ?? optional(\App\Models\Ubicacion::principal())->id;
                                StockProducto::create([
                                    'producto_id' => $producto->id,
                                    'variante_producto_id' => $variante->id,
                                    'ubicacion_id' => $ubicacionId,
                                    'cantidad_disponible' => $stockInicial,
                                    'cantidad_reservada' => 0,
                                    'stock_minimo' => $varianteData['stock_minimo'] ?? 0,
                                    'stock_maximo' => $varianteData['stock_maximo'] ?? null,
                                    'ubicacion' => $varianteData['ubicacion'] ?? null,
                                    'alerta_stock_bajo' => true
                                ]);

                                if ($stockInicial > 0) {
                                    MovimientoStock::create([
                                        'producto_id' => $producto->id,
                                        'variante_producto_id' => $variante->id,
                                        'ubicacion_id' => $ubicacionId,
                                        'tipo_movimiento' => 'entrada',
                                        'cantidad' => $stockInicial,
                                        'stock_anterior' => 0,
                                        'stock_nuevo' => $stockInicial,
                                        'origen' => 'ajuste_inventario',
                                        'motivo' => 'Stock inicial',
                                        'usuario_id' => auth()->id() ?? 1
                                    ]);
                                }
                            }
                        }
                    }
                }
                // Reasociar imágenes existentes con las variantes
                if ($request->has('imagen_variante')) {
                    $todasVariantes = $producto->variantes()->orderBy('id')->get()->values();
                    foreach ($request->imagen_variante as $imagenId => $varianteRowIndex) {
                        $varianteId = null;
                        if ($varianteRowIndex !== '' && isset($todasVariantes[(int)$varianteRowIndex])) {
                            $varianteId = $todasVariantes[(int)$varianteRowIndex]->id;
                        }
                        ImagenProducto::where('id', $imagenId)
                            ->where('producto_id', $producto->id)
                            ->update(['variante_producto_id' => $varianteId]);
                    }
                }
            } else if ($producto->controlar_stock && !$producto->tiene_variantes) {
                // Producto sin variantes - crear o actualizar stock principal (NUEVO)
                $stockInicial = $request->input('stock_inicial', 0);

                $stock = StockProducto::firstOrNew([
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null
                ]);

                $stockAnterior = $stock->cantidad_disponible ?? 0;

                // Si no existe el registro de stock (primera vez activando control de stock)
                if (!$stock->exists) {
                    // Sin ubicación explícita, el stock inicial va a la bodega principal (nunca NULL).
                    $ubicacionId = $request->input('ubicacion_id') ?? optional(\App\Models\Ubicacion::principal())->id;
                    $stock->fill([
                        'cantidad_disponible' => $stockInicial,
                        'cantidad_reservada' => 0,
                        'stock_minimo' => $request->input('stock_minimo', 0),
                        'stock_maximo' => $request->input('stock_maximo'),
                        'ubicacion_id' => $ubicacionId,
                        'ubicacion' => $request->input('ubicacion'),
                        'alerta_stock_bajo' => true
                    ])->save();

                    // Registrar movimiento si hay stock inicial
                    if ($stockInicial > 0) {
                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'variante_producto_id' => null,
                            'ubicacion_id' => $ubicacionId,
                            'tipo_movimiento' => 'entrada',
                            'cantidad' => $stockInicial,
                            'stock_anterior' => 0,
                            'stock_nuevo' => $stockInicial,
                            'origen' => 'ajuste_inventario',
                            'motivo' => 'Stock inicial',
                            'usuario_id' => auth()->id() ?? 1
                        ]);
                    }
                } else {
                    // El registro ya existe, solo actualizar configuración (no modificar cantidad_disponible)
                    $stock->update([
                        'stock_minimo' => $request->input('stock_minimo', 0),
                        'stock_maximo' => $request->input('stock_maximo'),
                        'ubicacion_id' => $request->input('ubicacion_id'),
                        'ubicacion' => $request->input('ubicacion')
                    ]);
                }
            }
            
            // Guardar imágenes nuevas
            $newImageIds = [];
            if ($request->hasFile('imagenes')) {
                $directory = public_path('imagenes/productos/' . $producto->id);
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $orden = $producto->imagenes()->max('orden') ?? 0;
                $imagenPrincipalNueva = $request->input('imagen_principal_nueva', 0);

                foreach ($request->file('imagenes') as $index => $imagen) {
                    $filename = time() . '_' . uniqid() . '_' . $imagen->getClientOriginalName();
                    $imagen->move($directory, $filename);
                    $path = 'imagenes/productos/' . $producto->id . '/' . $filename;

                    $orden++;
                    $newImg = $producto->imagenes()->create([
                        'ruta_imagen' => $path,
                        'texto_alternativo' => $producto->nombre,
                        'es_principal' => $index == $imagenPrincipalNueva,
                        'orden' => $orden
                    ]);
                    $newImageIds[$index] = $newImg->id;
                }
            }

            // Asociar imágenes nuevas con variantes
            if (!empty($newImageIds) && $request->has('imagen_variante_nueva') && $producto->tiene_variantes) {
                $nuevasVariantes = $producto->variantes()->orderBy('id')->get()->values();
                foreach ($request->imagen_variante_nueva as $imgIndex => $varianteRowIndex) {
                    if ($varianteRowIndex !== '' && isset($newImageIds[(int)$imgIndex]) && isset($nuevasVariantes[(int)$varianteRowIndex])) {
                        ImagenProducto::where('id', $newImageIds[(int)$imgIndex])
                            ->update(['variante_producto_id' => $nuevasVariantes[(int)$varianteRowIndex]->id]);
                    }
                }
            }
            
            // Actualizar imagen principal existente
            if ($request->has('imagen_principal_existente')) {
                // Quitar principal de todas
                $producto->imagenes()->update(['es_principal' => false]);
                // Establecer la nueva principal
                $producto->imagenes()
                        ->where('id', $request->imagen_principal_existente)
                        ->update(['es_principal' => true]);
            }
            
            // Eliminar imágenes marcadas
            if ($request->has('eliminar_imagenes')) {
                foreach ($request->eliminar_imagenes as $imagenId) {
                    $imagen = ImagenProducto::find($imagenId);
                    if ($imagen && $imagen->producto_id == $producto->id) {
                        // Eliminar archivo físico
                        $filePath = public_path($imagen->ruta_imagen);
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                        }
                        $imagen->delete();
                    }
                }
            }
            
            // Guardar precios
            if ($request->has('precios')) {
                foreach ($request->precios as $listaId => $precio) {
                    if (!empty($precio)) {
                        $producto->precios()->updateOrCreate(
                            ['lista_precio_id' => $listaId],
                            ['precio' => $precio, 'activo' => true]
                        );
                    } else {
                        $producto->precios()
                                ->where('lista_precio_id', $listaId)
                                ->delete();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('productos')
                           ->with('success', $request->id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                         ->with('error', 'Error al guardar el producto: ' . $e->getMessage());
        }
    }

public function actualizarPreciosExcel(Request $request)
{
    // 1. Validación
    $request->validate([
        'archivo' => 'required|mimes:xlsx,xls,csv|max:10240'
    ], [
        'archivo.required' => 'Debe seleccionar un archivo',
        'archivo.mimes'    => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
        'archivo.max'      => 'El archivo no debe superar los 10MB'
    ]);

    DB::beginTransaction();

    try {
        // 2. Obtener archivo y nombres
        $archivo        = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreArchivo  = time() . '_' . $nombreOriginal;

        // 3. Directorio en public
        $rutaPublic = public_path('uploads/actualizaciones_precios');
        if (! File::exists($rutaPublic)) {
            File::makeDirectory($rutaPublic, 0755, true);
        }

        // 4. Mover archivo y construir ruta relativa
        $archivo->move($rutaPublic, $nombreArchivo);
        $rutaArchivo = 'uploads/actualizaciones_precios/' . $nombreArchivo;

        // 5. Registrar en base de datos
        $actualizacion = ActualizacionPrecio::create([
            'usuario_id'               => auth()->id(),
            'estado'                   => 'procesando',
            'nombre_archivo'           => $nombreOriginal,
            'ruta_archivo'             => $rutaArchivo,
            'total_filas'              => 0,
            'actualizaciones_exitosas' => 0,
            'actualizaciones_fallidas' => 0,
            'errores'                  => [],
            'detalles_procesados'      => []
        ]);

        // 6. Log de inicio
        Log::info('Iniciando actualización de precios', [
            'usuario'          => auth()->user()->name,
            'archivo'          => $nombreArchivo,
            'actualizacion_id' => $actualizacion->id,
        ]);

        // 7. Importar usando la ruta en public
        $pathImport = public_path($rutaArchivo);
        Excel::import(new PreciosImport($actualizacion), $pathImport);

        DB::commit();

        // 8. Preparar mensaje de resultado
        $actualizacion->refresh();
        if ($actualizacion->actualizaciones_fallidas === 0 && $actualizacion->actualizaciones_exitosas > 0) {
            return back()->with('success', "Actualización completada: {$actualizacion->actualizaciones_exitosas} productos actualizados.");
        } elseif ($actualizacion->actualizaciones_exitosas > 0) {
            return back()->with('warning', "Actualización parcial: {$actualizacion->actualizaciones_exitosas} éxitosas, {$actualizacion->actualizaciones_fallidas} con errores.");
        } else {
            return back()->with('error', 'No se pudo actualizar ningún producto. Revisa el reporte de errores.');
        }
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error en actualización de precios', [
            'mensaje' => $e->getMessage()
        ]);
        return back()->with('error', 'Ocurrió un error procesando el archivo.');
    }
}


    // Métodos AJAX para los modales
    public function variantesAjax(Producto $producto)
    {
        $variantes = $producto->variantes()->with('imagenes')->get();

        $html = '<div class="table-responsive">';

        if ($variantes->isEmpty()) {
            $html .= '<p class="text-center text-muted">Este producto no tiene variantes configuradas.</p>';
        } else {
            $html .= '<table class="table table-striped">';
            $html .= '<thead><tr><th>SKU</th><th>Referencia</th><th>Color</th><th>Imágenes</th><th>Estado</th></tr></thead>';
            $html .= '<tbody>';

            foreach ($variantes as $variante) {
                $html .= '<tr>';
                $html .= '<td><code>' . $variante->sku . '</code></td>';
                $html .= '<td>' . ($variante->referencia_variante ?: '-') . '</td>';
                $html .= '<td>' . ($variante->color ?: '-') . '</td>';

                // Thumbnails de imágenes de la variante
                $html .= '<td>';
                if ($variante->imagenes->count()) {
                    foreach ($variante->imagenes->take(3) as $img) {
                        $html .= '<img src="' . asset($img->ruta_imagen) . '" style="width:40px;height:40px;object-fit:cover;margin-right:2px;" class="rounded">';
                    }
                    if ($variante->imagenes->count() > 3) {
                        $html .= '<span class="badge bg-secondary">+' . ($variante->imagenes->count() - 3) . '</span>';
                    }
                } else {
                    $html .= '<span class="text-muted small">Sin imágenes</span>';
                }
                $html .= '</td>';

                $html .= '<td>' . ($variante->activo ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        }

        $html .= '</div>';

        return response($html);
    }

    public function imagenesAjax(Producto $producto)
    {
        $imagenes = $producto->imagenes()->with('varianteProducto')->orderBy('variante_producto_id')->orderBy('orden')->get();

        $html = '<div class="row">';

        if ($imagenes->isEmpty()) {
            $html .= '<p class="text-center text-muted">Este producto no tiene imágenes.</p>';
        } else {
            foreach ($imagenes as $imagen) {
                $html .= '<div class="col-md-3 mb-3">';
                $html .= '<div class="card">';
                $html .= '<img src="' . asset($imagen->ruta_imagen) . '" class="card-img-top" style="height: 200px; object-fit: cover;">';
                $html .= '<div class="card-body p-2 text-center">';

                if ($imagen->es_principal) {
                    $html .= '<span class="badge bg-success me-1">Principal</span>';
                }

                if ($imagen->varianteProducto) {
                    $html .= '<span class="badge bg-info">' . e($imagen->varianteProducto->nombre_variante) . '</span>';
                } else {
                    $html .= '<span class="badge bg-secondary">General</span>';
                }

                $html .= '</div></div></div>';
            }
        }

        $html .= '</div>';

        return response($html);
    }

    public function preciosAjax(Producto $producto)
    {
        $precios = $producto->precios()->with('listaPrecio')->get();

        $html = '<div class="table-responsive">';

        if ($precios->isEmpty()) {
            $html .= '<p class="text-center text-muted">Este producto no tiene precios configurados.</p>';
        } else {
            $html .= '<table class="table table-striped">';
            $html .= '<thead><tr><th>Lista de Precios</th><th>Código</th><th>Precio</th><th>Estado</th></tr></thead>';
            $html .= '<tbody>';

            foreach ($precios as $precio) {
                $html .= '<tr>';
                $html .= '<td>' . $precio->listaPrecio->nombre . '</td>';
                $html .= '<td><code>' . $precio->listaPrecio->codigo . '</code></td>';
                $html .= '<td>$' . number_format($precio->precio, 2) . '</td>';
                $html .= '<td>' . ($precio->activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        }

        $html .= '</div>';

        return response($html)->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                              ->header('Pragma', 'no-cache')
                              ->header('Expires', '0');
    }

    // Método AJAX para ver stock (NUEVO)
    public function stockAjax(Producto $producto)
    {
        $stocks = $producto->stock()->with('variante')->get();
        
        $html = '<div class="table-responsive">';
        
        if ($stocks->isEmpty()) {
            $html .= '<p class="text-center text-muted">Este producto no tiene stock configurado.</p>';
        } else {
            $html .= '<table class="table table-striped">';
            $html .= '<thead><tr><th>Producto/Variante</th><th>Disponible</th><th>Reservado</th><th>Stock Real</th><th>Mín/Máx</th><th>Ubicación</th></tr></thead>';
            $html .= '<tbody>';
            
            foreach ($stocks as $stock) {
                $badge = 'success';
                if ($stock->stock_real <= 0) {
                    $badge = 'danger';
                } elseif ($stock->stock_bajo) {
                    $badge = 'warning';
                }
                
                $html .= '<tr>';
                $html .= '<td>' . ($stock->variante ? $stock->variante->nombre_variante : 'Principal') . '</td>';
                $html .= '<td>' . $stock->cantidad_disponible . '</td>';
                $html .= '<td>' . $stock->cantidad_reservada . '</td>';
                $html .= '<td><span class="badge bg-' . $badge . '">' . $stock->stock_real . '</span></td>';
                $html .= '<td>' . $stock->stock_minimo . '/' . ($stock->stock_maximo ?: '∞') . '</td>';
                $html .= '<td>' . ($stock->nombre_ubicacion ?: '-') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        $html .= '</div>';
        
        return response($html);
    }

    /**
     * Listar productos del catálogo de SIIGO para el modal de homologación.
     *
     * Consulta la tabla local `siigo_productos_cache` con LIKE en code, name y reference.
     * Si la tabla está vacía, dispara una sincronización inicial.
     */
    public function siigoProductosAjax(Request $request, SiigoConfigService $siigoConfig)
    {
        try {
            $q = trim((string) $request->input('q', ''));
            $page = max(1, (int) $request->input('page', 1));
            $pageSize = (int) $request->input('page_size', 25);
            $pageSize = max(1, min($pageSize, 100));

            // Si la tabla está vacía, hacer sync inicial bloqueante
            if (\App\Models\SiigoProductoCache::count() === 0) {
                $siigoConfig->sincronizarCatalogoSiigo();
            }

            $query = \App\Models\SiigoProductoCache::query()->where('active', true);

            if ($q !== '') {
                $like = '%' . $q . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('code', 'like', $like)
                      ->orWhere('name', 'like', $like)
                      ->orWhere('reference', 'like', $like);
                });
            }

            $total = (clone $query)->count();
            $registros = $query->orderBy('code')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get();

            $results = $registros->map(function ($r) {
                return [
                    'id' => $r->siigo_id,
                    'code' => $r->code,
                    'name' => $r->name ?? '',
                    'reference' => $r->reference,
                    'account_group' => $r->account_group_name,
                    'type' => $r->type,
                    'active' => $r->active,
                ];
            })->all();

            return response()->json([
                'success' => true,
                'results' => $results,
                'page' => $page,
                'page_size' => $pageSize,
                'total_results' => $total,
            ]);
        } catch (\Throwable $e) {
            Log::error('SIIGO obtenerProductos error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo consultar el catálogo de SIIGO: ' . $e->getMessage(),
                'results' => [],
            ], 500);
        }
    }

    /**
     * Sincronizar (recargar) el catálogo de productos de SIIGO contra la tabla local.
     */
    public function siigoSincronizarCatalogo(SiigoConfigService $siigoConfig)
    {
        try {
            $resultado = $siigoConfig->sincronizarCatalogoSiigo();

            return response()->json([
                'success' => true,
                'message' => "Catálogo SIIGO actualizado. Insertados: {$resultado['insertados']}, actualizados: {$resultado['actualizados']}, total: {$resultado['total']}.",
                'data' => $resultado,
            ]);
        } catch (\Throwable $e) {
            Log::error('SIIGO sincronizarCatalogo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo sincronizar el catálogo de SIIGO: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Devolver el estado actual de homologación SIIGO de un producto y sus variantes.
     */
    public function siigoHomologacionAjax(Producto $producto)
    {
        $producto->loadMissing('variantes');

        return response()->json([
            'producto' => [
                'id' => $producto->id,
                'referencia' => $producto->referencia,
                'nombre' => $producto->nombre,
                'tiene_variantes' => (bool) $producto->tiene_variantes,
                'siigo_product_code' => $producto->siigo_product_code,
            ],
            'variantes' => $producto->variantes->map(function ($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'referencia_variante' => $v->referencia_variante,
                    'color' => $v->color,
                    'siigo_product_code' => $v->siigo_product_code,
                ];
            })->values(),
        ]);
    }

    /**
     * Persistir el código SIIGO en el producto (sin variantes) o en la variante indicada.
     */
    public function siigoHomologarProducto(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'variante_id' => 'nullable|integer|exists:variantes_productos,id',
            'siigo_code' => 'nullable|string|max:50',
        ]);

        $codigo = $data['siigo_code'] !== null ? trim($data['siigo_code']) : null;
        $codigo = $codigo === '' ? null : $codigo;

        if ($producto->tiene_variantes) {
            if (empty($data['variante_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto tiene variantes; debe especificar la variante a homologar.',
                ], 422);
            }

            $variante = VarianteProducto::where('id', $data['variante_id'])
                ->where('producto_id', $producto->id)
                ->firstOrFail();

            $variante->siigo_product_code = $codigo;
            $variante->save();

            return response()->json([
                'success' => true,
                'message' => 'Variante homologada con SIIGO correctamente.',
                'variante_id' => $variante->id,
                'siigo_product_code' => $variante->siigo_product_code,
            ]);
        }

        if (!empty($data['variante_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Este producto no tiene variantes; no debe enviarse variante_id.',
            ], 422);
        }

        $producto->siigo_product_code = $codigo;
        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto homologado con SIIGO correctamente.',
            'producto_id' => $producto->id,
            'siigo_product_code' => $producto->siigo_product_code,
        ]);
    }

    public function eliminar($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->eliminado = true;
            $producto->save();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar productos con imágenes a Excel
     */
    public function exportarConImagenes(Request $request)
    {
        $categoriaId = $request->get('categoria_id');
        $incluirImagenes = $request->get('incluir_imagenes', true);

        $nombreArchivo = 'productos_catalogo_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\ProductosConImagenesExport($categoriaId, $incluirImagenes),
            $nombreArchivo
        );
    }
}