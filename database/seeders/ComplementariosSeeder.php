<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductoAdicional;
use Illuminate\Support\Facades\DB;

class ComplementariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder crea productos complementarios de ejemplo:
     * - Cross-selling: Productos generales que se muestran a todos
     * - Específicos: Productos que se asignan a productos particulares
     *
     * @return void
     */
    public function run()
    {
        // Limpiar tabla antes de insertar (usando delete en lugar de truncate por foreign keys)
        DB::table('productos_adicionales')->delete();

        $complementarios = [
            // === CROSS-SELLING (Generales) ===
            [
                'nombre' => 'Caja de Bombones Ferrero Rocher (16 unidades)',
                'categoria' => 'bombones',
                'tipo_complementario' => 'cross_selling',
                'precio' => 45000,
                'descripcion' => 'Exquisita caja de bombones Ferrero Rocher con 16 unidades.',
                'disponible' => true,
                'stock' => 50,
                'mostrar_en_checkout' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Bombones Artesanales Mix (12 unidades)',
                'categoria' => 'bombones',
                'tipo_complementario' => 'cross_selling',
                'precio' => 38000,
                'descripcion' => 'Selección de bombones artesanales con diferentes sabores.',
                'disponible' => true,
                'stock' => 30,
                'mostrar_en_checkout' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Peluche Osito Clásico (30cm)',
                'categoria' => 'peluche',
                'tipo_complementario' => 'cross_selling',
                'precio' => 35000,
                'descripcion' => 'Tierno peluche de osito de 30cm de altura.',
                'disponible' => true,
                'stock' => 40,
                'mostrar_en_checkout' => true,
                'orden' => 3,
            ],
            [
                'nombre' => 'Peluche Corazón "Te Amo" (25cm)',
                'categoria' => 'peluche',
                'tipo_complementario' => 'cross_selling',
                'precio' => 28000,
                'descripcion' => 'Corazón de peluche con mensaje "Te Amo".',
                'disponible' => true,
                'stock' => 35,
                'mostrar_en_checkout' => true,
                'orden' => 4,
            ],
            [
                'nombre' => 'Globo Metálico Corazón Rojo',
                'categoria' => 'globo',
                'tipo_complementario' => 'cross_selling',
                'precio' => 15000,
                'descripcion' => 'Globo metálico en forma de corazón color rojo.',
                'disponible' => true,
                'stock' => 100,
                'mostrar_en_checkout' => true,
                'orden' => 5,
            ],
            [
                'nombre' => 'Globo Metálico "Feliz Cumpleaños"',
                'categoria' => 'globo',
                'tipo_complementario' => 'cross_selling',
                'precio' => 12000,
                'descripcion' => 'Globo metálico con mensaje de feliz cumpleaños.',
                'disponible' => true,
                'stock' => 80,
                'mostrar_en_checkout' => true,
                'orden' => 6,
            ],
            [
                'nombre' => 'Vino Tinto Gran Reserva',
                'categoria' => 'vino',
                'tipo_complementario' => 'cross_selling',
                'precio' => 85000,
                'descripcion' => 'Vino tinto gran reserva de excelente calidad.',
                'disponible' => true,
                'stock' => 20,
                'mostrar_en_checkout' => true,
                'orden' => 7,
            ],
            [
                'nombre' => 'Espumante Brut Nature',
                'categoria' => 'vino',
                'tipo_complementario' => 'cross_selling',
                'precio' => 65000,
                'descripcion' => 'Espumante brut nature ideal para celebraciones.',
                'disponible' => true,
                'stock' => 25,
                'mostrar_en_checkout' => true,
                'orden' => 8,
            ],
            [
                'nombre' => 'Whisky Johnnie Walker Red Label',
                'categoria' => 'licor',
                'tipo_complementario' => 'cross_selling',
                'precio' => 95000,
                'descripcion' => 'Whisky escocés mezclado Red Label.',
                'disponible' => true,
                'stock' => 15,
                'mostrar_en_checkout' => true,
                'orden' => 9,
            ],
            [
                'nombre' => 'Ron Añejo Premium',
                'categoria' => 'licor',
                'tipo_complementario' => 'cross_selling',
                'precio' => 120000,
                'descripcion' => 'Ron añejo premium de alta calidad.',
                'disponible' => true,
                'stock' => 12,
                'mostrar_en_checkout' => true,
                'orden' => 10,
            ],

            // === COMPLEMENTARIOS ESPECÍFICOS ===
            [
                'nombre' => 'Florero Cristal Clásico',
                'categoria' => 'florero',
                'tipo_complementario' => 'especifico',
                'precio' => 45000,
                'descripcion' => 'Hermoso florero de cristal para complementar tu ramo de flores.',
                'disponible' => true,
                'stock' => 25,
                'mostrar_en_checkout' => true,
                'orden' => 11,
            ],
            [
                'nombre' => 'Florero Cerámico Moderno',
                'categoria' => 'florero',
                'tipo_complementario' => 'especifico',
                'precio' => 38000,
                'descripcion' => 'Florero cerámico de diseño moderno.',
                'disponible' => true,
                'stock' => 20,
                'mostrar_en_checkout' => true,
                'orden' => 12,
            ],
            [
                'nombre' => 'Preservante para Flores (Sobre)',
                'categoria' => 'preservante',
                'tipo_complementario' => 'especifico',
                'precio' => 5000,
                'descripcion' => 'Preservante especial que prolonga la vida de tus flores hasta por 10 días.',
                'disponible' => true,
                'stock' => 100,
                'mostrar_en_checkout' => true,
                'orden' => 13,
            ],
            [
                'nombre' => 'Fertilizante para Flores Cortadas',
                'categoria' => 'preservante',
                'tipo_complementario' => 'especifico',
                'precio' => 8000,
                'descripcion' => 'Fertilizante especial para mantener flores cortadas frescas y vibrantes.',
                'disponible' => true,
                'stock' => 80,
                'mostrar_en_checkout' => true,
                'orden' => 14,
            ],
        ];

        foreach ($complementarios as $complementario) {
            ProductoAdicional::create($complementario);
        }

        $this->command->info('✅ Complementarios creados exitosamente!');
        $this->command->info('📦 Total: ' . count($complementarios) . ' productos');
        $this->command->info('🛒 Cross-selling: 10 productos (Bombones, Peluches, Globos, Vinos, Licores)');
        $this->command->info('🏷️  Específicos: 4 productos (Floreros y Preservantes para ramos)');
    }
}
