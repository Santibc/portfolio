<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ImagenProducto;
use App\Models\PrecioProducto;
use App\Models\ListaPrecio;
use App\Models\VarianteProducto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Producto::with(['categoria', 'imagenPrincipal', 'stockPrincipal'])
                            ->select('productos.*');

            return DataTables::of($query)
                ->addColumn('categoria', fn($p) => $p->categoria?->nombre)
                ->addColumn('imagen', function($p) {
                    $url = $p->imagenPrincipal 
                        ? asset($p->imagenPrincipal->ruta_imagen)
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
                ->addColumn('variantes', fn($p) => $p->tiene_variantes ? 'Sí' : 'No')
                ->addColumn('activo', fn($p) => $p->activo ? 'Sí' : 'No')
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
                    
                    $buttons .= '</div>';
                    
                    return $buttons;
                })
                ->rawColumns(['imagen', 'stock', 'action'])
                ->make(true);
        }

        return view('productos.productos_index');
    }

    public function form(Producto $producto = null)
    {
        $producto = $producto ?? new Producto();
        $categorias = Categoria::activas()->pluck('nombre', 'id');
        $listas = ListaPrecio::activas()->get();
        
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
        
        return view('productos.productos_form', compact('producto', 'categorias', 'listas', 'stocks'));
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
            'categoria_id' => ['required','exists:categorias,id'],
            'controlar_stock' => ['boolean'],  // NUEVO
            'permitir_venta_sin_stock' => ['boolean'],  // NUEVO
            'imagenes.*' => ['nullable','image','mimes:jpeg,png,jpg,webp','max:2048'],
            'variantes.*.talla' => ['nullable','string','max:50'],
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
            'ubicacion_stock' => ['nullable','string','max:255'],  // NUEVO
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
            
            // Guardar variantes
            if ($producto->tiene_variantes && $request->has('variantes')) {
                // Si es edición, eliminar variantes anteriores
                if ($request->id) {
                    // Eliminar stock de variantes eliminadas (NUEVO)
                    $variantesIds = $producto->variantes()->pluck('id');
                    StockProducto::whereIn('variante_producto_id', $variantesIds)
                                 ->where('producto_id', $producto->id)
                                 ->delete();
                    $producto->variantes()->delete();
                }
                
                foreach ($request->variantes as $index => $varianteData) {
                    if (!empty($varianteData['talla']) || !empty($varianteData['color']) || !empty($varianteData['sku'])) {
                        // Generar SKU si no se proporciona
                        $sku = $varianteData['sku'];
                        if (empty($sku)) {
                            $sku = $producto->referencia;
                            if (!empty($varianteData['talla'])) {
                                $sku .= '-' . strtoupper(str_replace(' ', '', $varianteData['talla']));
                            }
                            if (!empty($varianteData['color'])) {
                                $sku .= '-' . strtoupper(str_replace(' ', '', $varianteData['color']));
                            }
                            if (empty($varianteData['talla']) && empty($varianteData['color'])) {
                                $count = $producto->variantes()->count() + 1;
                                $sku .= '-VAR' . $count;
                            }
                        }
                        
                        $variante = $producto->variantes()->create([
                            'talla' => $varianteData['talla'],
                            'color' => $varianteData['color'],
                            'sku' => $sku,
                            'activo' => true
                        ]);
                        
                        // Crear registro de stock para la variante si se controla stock (NUEVO)
                        if ($producto->controlar_stock) {
                            $stockInicial = $varianteData['stock_inicial'] ?? 0;
                            $stock = StockProducto::create([
                                'producto_id' => $producto->id,
                                'variante_producto_id' => $variante->id,
                                'cantidad_disponible' => $stockInicial,
                                'cantidad_reservada' => 0,
                                'stock_minimo' => $varianteData['stock_minimo'] ?? 0,
                                'stock_maximo' => $varianteData['stock_maximo'] ?? null,
                                'ubicacion' => $varianteData['ubicacion'] ?? null,
                                'alerta_stock_bajo' => true
                            ]);
                            
                            // Registrar movimiento inicial si hay stock
                            if ($stockInicial > 0) {
                                MovimientoStock::create([
                                    'producto_id' => $producto->id,
                                    'variante_producto_id' => $variante->id,
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
            } else if ($producto->controlar_stock && !$producto->tiene_variantes) {
                // Producto sin variantes - crear o actualizar stock principal (NUEVO)
                $stockInicial = $request->input('stock_inicial', 0);
                
                $stock = StockProducto::firstOrNew([
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null
                ]);
                
                // Si es nuevo o si cambió el stock
                if (!$stock->exists || ($esNuevo && $stockInicial > 0)) {
                    $stockAnterior = $stock->cantidad_disponible ?? 0;
                    
                    $stock->fill([
                        'cantidad_disponible' => $esNuevo ? $stockInicial : $stock->cantidad_disponible,
                        'cantidad_reservada' => $stock->cantidad_reservada ?? 0,
                        'stock_minimo' => $request->input('stock_minimo', 0),
                        'stock_maximo' => $request->input('stock_maximo'),
                        'ubicacion' => $request->input('ubicacion_stock'),
                        'alerta_stock_bajo' => true
                    ])->save();
                    
                    // Registrar movimiento si es nuevo con stock inicial
                    if ($esNuevo && $stockInicial > 0) {
                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'variante_producto_id' => null,
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
                    // Solo actualizar configuración
                    $stock->update([
                        'stock_minimo' => $request->input('stock_minimo', 0),
                        'stock_maximo' => $request->input('stock_maximo'),
                        'ubicacion' => $request->input('ubicacion_stock')
                    ]);
                }
            }
            
            // Guardar imágenes nuevas
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
                    $producto->imagenes()->create([
                        'ruta_imagen' => $path,
                        'texto_alternativo' => $producto->nombre,
                        'es_principal' => $index == $imagenPrincipalNueva,
                        'orden' => $orden
                    ]);
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
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        // TODO: Implementar lógica de actualización masiva de precios desde Excel
        
        return back()->with('success', 'Precios actualizados desde Excel.');
    }

    // Métodos AJAX para los modales
    public function variantesAjax(Producto $producto)
    {
        $variantes = $producto->variantes()->get();
        
        $html = '<div class="table-responsive">';
        
        if ($variantes->isEmpty()) {
            $html .= '<p class="text-center text-muted">Este producto no tiene variantes configuradas.</p>';
        } else {
            $html .= '<table class="table table-striped">';
            $html .= '<thead><tr><th>SKU</th><th>Talla</th><th>Color</th><th>Estado</th></tr></thead>';
            $html .= '<tbody>';
            
            foreach ($variantes as $variante) {
                $html .= '<tr>';
                $html .= '<td><code>' . $variante->sku . '</code></td>';
                $html .= '<td>' . ($variante->talla ?: '-') . '</td>';
                $html .= '<td>' . ($variante->color ?: '-') . '</td>';
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
        $imagenes = $producto->imagenes()->orderBy('orden')->get();
        
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
                    $html .= '<span class="badge bg-success">Principal</span>';
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
        
        return response($html);
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
                $html .= '<td>' . ($stock->ubicacion ?: '-') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
        }
        
        $html .= '</div>';
        
        return response($html);
    }
}