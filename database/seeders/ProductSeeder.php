<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ImagenProducto;
use App\Models\VarianteProducto;
use App\Models\PrecioProducto;
use App\Models\PrecioVariante;
use App\Models\StockProducto;
use App\Models\ListaPrecio;
use App\Models\Empresa;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener la empresa y lista de precios existentes
        $empresa = Empresa::first();
        $listaPrecio = ListaPrecio::first();

        if (!$empresa || !$listaPrecio) {
            $this->command->error('No se encontró empresa o lista de precios. Ejecuta primero los seeders base.');
            return;
        }

        // Crear categorías
        $categorias = [
            [
                'nombre' => 'Ramos de Flores',
                'descripcion' => 'Hermosos ramos de flores frescas para toda ocasión',
                'imagen' => 'categorias/ramos.jpg',
                'activo' => true,
            ],
            [
                'nombre' => 'Arreglos Florales',
                'descripcion' => 'Arreglos florales únicos y elegantes',
                'imagen' => 'categorias/arreglos.jpg',
                'activo' => true,
            ],
            [
                'nombre' => 'Plantas',
                'descripcion' => 'Plantas decorativas y de interior',
                'imagen' => 'categorias/plantas.jpg',
                'activo' => true,
            ],
            [
                'nombre' => 'Ocasiones Especiales',
                'descripcion' => 'Flores perfectas para celebraciones especiales',
                'imagen' => 'categorias/especiales.jpg',
                'activo' => true,
            ],
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $orden => $categoriaData) {
            $categoriasCreadas[$categoriaData['nombre']] = Categoria::firstOrCreate(
                [
                    'empresa_id' => $empresa->id,
                    'nombre' => $categoriaData['nombre'],
                ],
                [
                    'descripcion' => $categoriaData['descripcion'],
                    'imagen' => $categoriaData['imagen'],
                    'activo' => $categoriaData['activo'],
                    'orden' => $orden,
                ]
            );
        }

        // Productos de ejemplo con detalles realistas
        $productos = [
            // RAMOS
            [
                'categoria' => 'Ramos de Flores',
                'nombre' => 'Ramo de Rosas Rojas',
                'descripcion' => 'Clásico ramo de 12 rosas rojas frescas, símbolo perfecto de amor y pasión. Incluye envoltura elegante y tarjeta personalizada.',
                'precio_base' => 45000,
                'imagen_principal' => 'productos/ramo-rosas-rojas.jpg',
                'imagenes_adicionales' => ['productos/ramo-rosas-rojas-2.jpg'],
                'variantes' => [
                    ['talla' => 'Pequeño (6 rosas)', 'sku' => 'RRR-P-001', 'precio_ajuste' => -15000, 'stock' => 25],
                    ['talla' => 'Mediano (12 rosas)', 'sku' => 'RRR-M-001', 'precio_ajuste' => 0, 'stock' => 30],
                    ['talla' => 'Grande (24 rosas)', 'sku' => 'RRR-G-001', 'precio_ajuste' => 40000, 'stock' => 15],
                ],
            ],
            [
                'categoria' => 'Ramos de Flores',
                'nombre' => 'Ramo Mixto Primaveral',
                'descripcion' => 'Alegre combinación de gerberas, lirios y margaritas en tonos pastel. Perfecto para alegrar cualquier día.',
                'precio_base' => 38000,
                'imagen_principal' => 'productos/ramo-mixto-primaveral.jpg',
                'imagenes_adicionales' => ['productos/ramo-mixto-primaveral-2.jpg', 'productos/ramo-mixto-primaveral-3.jpg'],
                'variantes' => [
                    ['talla' => 'Pequeño', 'sku' => 'RMP-P-002', 'precio_ajuste' => -12000, 'stock' => 20],
                    ['talla' => 'Mediano', 'sku' => 'RMP-M-002', 'precio_ajuste' => 0, 'stock' => 28],
                    ['talla' => 'Grande', 'sku' => 'RMP-G-002', 'precio_ajuste' => 30000, 'stock' => 12],
                ],
            ],
            [
                'categoria' => 'Ramos de Flores',
                'nombre' => 'Ramo de Tulipanes',
                'descripcion' => 'Elegantes tulipanes holandeses en colores vibrantes. Disponibles en rojo, amarillo, rosa y blanco.',
                'precio_base' => 42000,
                'imagen_principal' => 'productos/ramo-tulipanes.jpg',
                'imagenes_adicionales' => [],
                'variantes' => [
                    ['talla' => 'Pequeño (10 tulipanes)', 'sku' => 'RTU-P-003', 'precio_ajuste' => -14000, 'stock' => 18],
                    ['talla' => 'Grande (20 tulipanes)', 'sku' => 'RTU-G-003', 'precio_ajuste' => 35000, 'stock' => 10],
                ],
            ],

            // ARREGLOS FLORALES
            [
                'categoria' => 'Arreglos Florales',
                'nombre' => 'Arreglo en Caja Premium',
                'descripcion' => 'Sofisticado arreglo en caja de lujo con rosas, orquídeas y eucalipto. Ideal para ocasiones especiales y regalos corporativos.',
                'precio_base' => 75000,
                'imagen_principal' => 'productos/arreglo-caja-premium.jpg',
                'imagenes_adicionales' => ['productos/arreglo-caja-premium-2.jpg'],
                'variantes' => [
                    ['talla' => 'Mediano', 'sku' => 'ACP-M-004', 'precio_ajuste' => 0, 'stock' => 15],
                    ['talla' => 'Grande', 'sku' => 'ACP-G-004', 'precio_ajuste' => 50000, 'stock' => 8],
                ],
            ],
            [
                'categoria' => 'Arreglos Florales',
                'nombre' => 'Centro de Mesa Romantic',
                'descripcion' => 'Hermoso centro de mesa con rosas blancas, lisianthus y follaje verde. Perfecto para bodas y eventos.',
                'precio_base' => 55000,
                'imagen_principal' => 'productos/centro-mesa-romantic.jpg',
                'imagenes_adicionales' => [],
                'variantes' => null, // Sin variantes, precio y stock únicos
                'stock_unico' => 20,
            ],
            [
                'categoria' => 'Arreglos Florales',
                'nombre' => 'Arreglo Girasoles',
                'descripcion' => 'Radiante arreglo de girasoles con flores complementarias en tonos amarillos y naranjas. Transmite alegría y energía positiva.',
                'precio_base' => 48000,
                'imagen_principal' => 'productos/arreglo-girasoles.jpg',
                'imagenes_adicionales' => ['productos/arreglo-girasoles-2.jpg'],
                'variantes' => [
                    ['talla' => 'Pequeño', 'sku' => 'AGI-P-006', 'precio_ajuste' => -15000, 'stock' => 22],
                    ['talla' => 'Mediano', 'sku' => 'AGI-M-006', 'precio_ajuste' => 0, 'stock' => 18],
                ],
            ],

            // PLANTAS
            [
                'categoria' => 'Plantas',
                'nombre' => 'Orquídea Phalaenopsis',
                'descripcion' => 'Elegante orquídea de interior con flores duraderas. Incluye maceta decorativa y guía de cuidados.',
                'precio_base' => 65000,
                'imagen_principal' => 'productos/orquidea-phalaenopsis.jpg',
                'imagenes_adicionales' => [],
                'variantes' => null,
                'stock_unico' => 12,
            ],
            [
                'categoria' => 'Plantas',
                'nombre' => 'Suculentas Mini en Set',
                'descripcion' => 'Set de 3 suculentas variadas en macetas de cerámica. Ideales para decoración de escritorio, fáciles de cuidar.',
                'precio_base' => 28000,
                'imagen_principal' => 'productos/suculentas-set.jpg',
                'imagenes_adicionales' => ['productos/suculentas-set-2.jpg'],
                'variantes' => null,
                'stock_unico' => 35,
            ],
            [
                'categoria' => 'Plantas',
                'nombre' => 'Planta de Lavanda',
                'descripcion' => 'Aromática planta de lavanda en maceta. Perfecta para jardines, balcones o interiores luminosos.',
                'precio_base' => 32000,
                'imagen_principal' => 'productos/lavanda.jpg',
                'imagenes_adicionales' => [],
                'variantes' => null,
                'stock_unico' => 18,
            ],

            // OCASIONES ESPECIALES
            [
                'categoria' => 'Ocasiones Especiales',
                'nombre' => 'Bouquet de Novia Clásico',
                'descripcion' => 'Romántico bouquet de novia con rosas blancas, peonías y delicado baby breath. Personalizable según la temática de tu boda.',
                'precio_base' => 95000,
                'imagen_principal' => 'productos/bouquet-novia.jpg',
                'imagenes_adicionales' => ['productos/bouquet-novia-2.jpg', 'productos/bouquet-novia-3.jpg'],
                'variantes' => null,
                'stock_unico' => 8,
            ],
            [
                'categoria' => 'Ocasiones Especiales',
                'nombre' => 'Arreglo Condolencias',
                'descripcion' => 'Sobrio y elegante arreglo floral en tonos blancos con crisantemos, lirios y rosas. Expresa respeto y acompañamiento.',
                'precio_base' => 68000,
                'imagen_principal' => 'productos/arreglo-condolencias.jpg',
                'imagenes_adicionales' => [],
                'variantes' => [
                    ['talla' => 'Mediano', 'sku' => 'ACO-M-011', 'precio_ajuste' => 0, 'stock' => 10],
                    ['talla' => 'Grande', 'sku' => 'ACO-G-011', 'precio_ajuste' => 45000, 'stock' => 6],
                ],
            ],
            [
                'categoria' => 'Ocasiones Especiales',
                'nombre' => 'Ramo Día de la Madre',
                'descripcion' => 'Especial ramo con claveles, rosas rosadas y alstroemeria. Incluye tarjeta especial "Para Mamá" y envoltura premium.',
                'precio_base' => 52000,
                'imagen_principal' => 'productos/ramo-dia-madre.jpg',
                'imagenes_adicionales' => ['productos/ramo-dia-madre-2.jpg'],
                'variantes' => [
                    ['talla' => 'Pequeño', 'sku' => 'RDM-P-012', 'precio_ajuste' => -18000, 'stock' => 25],
                    ['talla' => 'Mediano', 'sku' => 'RDM-M-012', 'precio_ajuste' => 0, 'stock' => 30],
                    ['talla' => 'Grande', 'sku' => 'RDM-G-012', 'precio_ajuste' => 38000, 'stock' => 15],
                ],
            ],
            [
                'categoria' => 'Ocasiones Especiales',
                'nombre' => 'Arreglo Aniversario Love',
                'descripcion' => 'Romántico arreglo con rosas rojas y rosadas, chocolates Ferrero Rocher y globo metálico. El regalo perfecto para celebrar el amor.',
                'precio_base' => 82000,
                'imagen_principal' => 'productos/arreglo-aniversario.jpg',
                'imagenes_adicionales' => [],
                'variantes' => null,
                'stock_unico' => 12,
            ],
            [
                'categoria' => 'Ramos de Flores',
                'nombre' => 'Ramo de Claveles',
                'descripcion' => 'Ramo de claveles frescos en variedad de colores. Flores duraderas con fragancia delicada, perfectas para cualquier ocasión.',
                'precio_base' => 32000,
                'imagen_principal' => 'productos/ramo-claveles.jpg',
                'imagenes_adicionales' => [],
                'variantes' => [
                    ['talla' => 'Pequeño', 'sku' => 'RCL-P-014', 'precio_ajuste' => -10000, 'stock' => 30],
                    ['talla' => 'Mediano', 'sku' => 'RCL-M-014', 'precio_ajuste' => 0, 'stock' => 35],
                ],
            ],
            [
                'categoria' => 'Arreglos Florales',
                'nombre' => 'Arreglo Tropical Paradise',
                'descripcion' => 'Exótico arreglo con aves del paraíso, heliconias y follaje tropical. Ideal para ambientes modernos y festivos.',
                'precio_base' => 72000,
                'imagen_principal' => 'productos/arreglo-tropical.jpg',
                'imagenes_adicionales' => ['productos/arreglo-tropical-2.jpg'],
                'variantes' => null,
                'stock_unico' => 10,
            ],
        ];

        // Crear productos
        foreach ($productos as $index => $productoData) {
            $categoria = $categoriasCreadas[$productoData['categoria']];

            // Crear producto
            $sku = 'PROD-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $producto = Producto::create([
                'empresa_id' => $empresa->id,
                'categoria_id' => $categoria->id,
                'referencia' => $sku,
                'nombre' => $productoData['nombre'],
                'descripcion' => $productoData['descripcion'],
                'unidad_venta' => 'Unidad',
                'unidad_empaque' => 'Unidad',
                'activo' => true,
                'eliminado' => false,
                'tiene_variantes' => isset($productoData['variantes']) && $productoData['variantes'] !== null ? 1 : 0,
                'controlar_stock' => true,
                'permitir_venta_sin_stock' => false,
            ]);

            // Crear imagen principal
            ImagenProducto::create([
                'producto_id' => $producto->id,
                'ruta_imagen' => $productoData['imagen_principal'],
                'orden' => 0,
                'es_principal' => true,
            ]);

            // Crear imágenes adicionales
            if (!empty($productoData['imagenes_adicionales'])) {
                foreach ($productoData['imagenes_adicionales'] as $orden => $ruta) {
                    ImagenProducto::create([
                        'producto_id' => $producto->id,
                        'ruta_imagen' => $ruta,
                        'orden' => $orden + 1,
                        'es_principal' => false,
                    ]);
                }
            }

            // Si tiene variantes
            if (isset($productoData['variantes']) && $productoData['variantes'] !== null) {
                foreach ($productoData['variantes'] as $varianteData) {
                    $variante = VarianteProducto::create([
                        'producto_id' => $producto->id,
                        'sku' => $varianteData['sku'],
                        'talla' => $varianteData['talla'],
                        'activo' => true,
                    ]);

                    // Precio de la variante (ajuste sobre precio base del producto)
                    PrecioVariante::create([
                        'variante_producto_id' => $variante->id,
                        'lista_precio_id' => $listaPrecio->id,
                        'ajuste_precio' => $varianteData['precio_ajuste'],
                        'activo' => true,
                    ]);

                    // Stock de la variante
                    StockProducto::create([
                        'producto_id' => $producto->id,
                        'variante_producto_id' => $variante->id,
                        'cantidad_disponible' => $varianteData['stock'],
                        'cantidad_reservada' => 0,
                        'stock_minimo' => 5,
                        'alerta_stock_bajo' => true,
                    ]);
                }
            } else {
                // Producto sin variantes - precio y stock directo
                PrecioProducto::create([
                    'producto_id' => $producto->id,
                    'lista_precio_id' => $listaPrecio->id,
                    'precio' => $productoData['precio_base'],
                ]);

                StockProducto::create([
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'cantidad_disponible' => $productoData['stock_unico'] ?? 15,
                    'cantidad_reservada' => 0,
                    'stock_minimo' => 3,
                    'alerta_stock_bajo' => true,
                ]);
            }

            $this->command->info("✓ Producto creado: {$productoData['nombre']}");
        }

        $this->command->info("\n✓ Seeder completado:");
        $this->command->info("  - 4 categorías creadas");
        $this->command->info("  - 15 productos creados");
        $this->command->info("  - Variantes, precios y stock configurados");
    }
}
