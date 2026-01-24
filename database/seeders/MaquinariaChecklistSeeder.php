<?php

namespace Database\Seeders;

use App\Models\MaquinariaChecklistItem;
use App\Models\MaquinariaChecklistPlantilla;
use App\Models\MaquinariaTipo;
use Illuminate\Database\Seeder;

class MaquinariaChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Checklist para Motosierra
        $tipoMotosierra = MaquinariaTipo::where('nombre', 'Motosierra')->first();
        if ($tipoMotosierra) {
            $plantilla = MaquinariaChecklistPlantilla::firstOrCreate(
                ['maquinaria_tipo_id' => $tipoMotosierra->id, 'nombre' => 'Inspeccion Diaria Motosierra'],
                ['descripcion' => 'Checklist de seguridad antes del uso diario', 'activa' => true]
            );

            $items = [
                // Seguridad
                ['categoria' => 'Seguridad', 'descripcion' => 'Freno de cadena funciona correctamente', 'orden' => 1, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Protector de mano delantero en buen estado', 'orden' => 2, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Captor de cadena presente y en buen estado', 'orden' => 3, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Interruptor de parada funciona', 'orden' => 4, 'obligatorio' => true],

                // Motor
                ['categoria' => 'Motor', 'descripcion' => 'Nivel de combustible adecuado', 'orden' => 5, 'obligatorio' => false],
                ['categoria' => 'Motor', 'descripcion' => 'Nivel de aceite de cadena adecuado', 'orden' => 6, 'obligatorio' => true],
                ['categoria' => 'Motor', 'descripcion' => 'Filtro de aire limpio', 'orden' => 7, 'obligatorio' => false],
                ['categoria' => 'Motor', 'descripcion' => 'Arranque suave sin tirones', 'orden' => 8, 'obligatorio' => false],

                // Cadena y Espada
                ['categoria' => 'Cadena y Espada', 'descripcion' => 'Tension de cadena correcta', 'orden' => 9, 'obligatorio' => true],
                ['categoria' => 'Cadena y Espada', 'descripcion' => 'Cadena afilada', 'orden' => 10, 'obligatorio' => true],
                ['categoria' => 'Cadena y Espada', 'descripcion' => 'Espada sin deformaciones', 'orden' => 11, 'obligatorio' => true],

                // General
                ['categoria' => 'General', 'descripcion' => 'Sin fugas de combustible o aceite', 'orden' => 12, 'obligatorio' => true],
                ['categoria' => 'General', 'descripcion' => 'Mangos y empunaduras en buen estado', 'orden' => 13, 'obligatorio' => false],
                ['categoria' => 'General', 'descripcion' => 'Silenciador sin danos visibles', 'orden' => 14, 'obligatorio' => false],
            ];

            foreach ($items as $item) {
                MaquinariaChecklistItem::firstOrCreate(
                    ['plantilla_id' => $plantilla->id, 'descripcion' => $item['descripcion']],
                    ['categoria' => $item['categoria'], 'orden' => $item['orden'], 'obligatorio' => $item['obligatorio']]
                );
            }
        }

        // Checklist para Desbrozadora
        $tipoDesbrozadora = MaquinariaTipo::where('nombre', 'Desbrozadora')->first();
        if ($tipoDesbrozadora) {
            $plantilla = MaquinariaChecklistPlantilla::firstOrCreate(
                ['maquinaria_tipo_id' => $tipoDesbrozadora->id, 'nombre' => 'Inspeccion Diaria Desbrozadora'],
                ['descripcion' => 'Checklist de seguridad para desbrozadoras', 'activa' => true]
            );

            $items = [
                // Seguridad
                ['categoria' => 'Seguridad', 'descripcion' => 'Protector de disco/hilo en buen estado', 'orden' => 1, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Interruptor de parada funciona', 'orden' => 2, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Arnes de sujecion en buen estado', 'orden' => 3, 'obligatorio' => true],

                // Motor
                ['categoria' => 'Motor', 'descripcion' => 'Nivel de combustible adecuado', 'orden' => 4, 'obligatorio' => false],
                ['categoria' => 'Motor', 'descripcion' => 'Filtro de aire limpio', 'orden' => 5, 'obligatorio' => false],
                ['categoria' => 'Motor', 'descripcion' => 'Arranque correcto', 'orden' => 6, 'obligatorio' => false],

                // Cabezal
                ['categoria' => 'Cabezal', 'descripcion' => 'Disco/cuchilla sin grietas o danos', 'orden' => 7, 'obligatorio' => true],
                ['categoria' => 'Cabezal', 'descripcion' => 'Fijacion del cabezal segura', 'orden' => 8, 'obligatorio' => true],
                ['categoria' => 'Cabezal', 'descripcion' => 'Hilo de nylon con longitud adecuada', 'orden' => 9, 'obligatorio' => false],

                // General
                ['categoria' => 'General', 'descripcion' => 'Barra de transmision sin vibraciones anormales', 'orden' => 10, 'obligatorio' => false],
                ['categoria' => 'General', 'descripcion' => 'Mangos y empunaduras firmes', 'orden' => 11, 'obligatorio' => false],
            ];

            foreach ($items as $item) {
                MaquinariaChecklistItem::firstOrCreate(
                    ['plantilla_id' => $plantilla->id, 'descripcion' => $item['descripcion']],
                    ['categoria' => $item['categoria'], 'orden' => $item['orden'], 'obligatorio' => $item['obligatorio']]
                );
            }
        }

        // Checklist para Cortasetos
        $tipoCortasetos = MaquinariaTipo::where('nombre', 'Cortasetos')->first();
        if ($tipoCortasetos) {
            $plantilla = MaquinariaChecklistPlantilla::firstOrCreate(
                ['maquinaria_tipo_id' => $tipoCortasetos->id, 'nombre' => 'Inspeccion Diaria Cortasetos'],
                ['descripcion' => 'Checklist de seguridad para cortasetos', 'activa' => true]
            );

            $items = [
                ['categoria' => 'Seguridad', 'descripcion' => 'Protector de cuchillas en buen estado', 'orden' => 1, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Interruptor de parada funciona', 'orden' => 2, 'obligatorio' => true],
                ['categoria' => 'Motor', 'descripcion' => 'Nivel de combustible adecuado', 'orden' => 3, 'obligatorio' => false],
                ['categoria' => 'Motor', 'descripcion' => 'Filtro de aire limpio', 'orden' => 4, 'obligatorio' => false],
                ['categoria' => 'Cuchillas', 'descripcion' => 'Cuchillas afiladas', 'orden' => 5, 'obligatorio' => true],
                ['categoria' => 'Cuchillas', 'descripcion' => 'Cuchillas sin danos o mellas', 'orden' => 6, 'obligatorio' => true],
                ['categoria' => 'General', 'descripcion' => 'Mangos en buen estado', 'orden' => 7, 'obligatorio' => false],
            ];

            foreach ($items as $item) {
                MaquinariaChecklistItem::firstOrCreate(
                    ['plantilla_id' => $plantilla->id, 'descripcion' => $item['descripcion']],
                    ['categoria' => $item['categoria'], 'orden' => $item['orden'], 'obligatorio' => $item['obligatorio']]
                );
            }
        }

        // Checklist para Plataforma Elevadora
        $tipoPlataforma = MaquinariaTipo::where('nombre', 'Plataforma Elevadora')->first();
        if ($tipoPlataforma) {
            $plantilla = MaquinariaChecklistPlantilla::firstOrCreate(
                ['maquinaria_tipo_id' => $tipoPlataforma->id, 'nombre' => 'Inspeccion Pre-Uso Plataforma'],
                ['descripcion' => 'Checklist obligatorio antes de usar plataforma elevadora', 'activa' => true]
            );

            $items = [
                // Seguridad
                ['categoria' => 'Seguridad', 'descripcion' => 'Parada de emergencia funciona', 'orden' => 1, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Barandillas y portezuelas en buen estado', 'orden' => 2, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Anclajes para arnes disponibles', 'orden' => 3, 'obligatorio' => true],
                ['categoria' => 'Seguridad', 'descripcion' => 'Alarmas de inclinacion funcionan', 'orden' => 4, 'obligatorio' => true],

                // Sistemas
                ['categoria' => 'Sistemas', 'descripcion' => 'Sistema hidraulico sin fugas', 'orden' => 5, 'obligatorio' => true],
                ['categoria' => 'Sistemas', 'descripcion' => 'Nivel de aceite hidraulico correcto', 'orden' => 6, 'obligatorio' => true],
                ['categoria' => 'Sistemas', 'descripcion' => 'Controles de elevacion funcionan', 'orden' => 7, 'obligatorio' => true],
                ['categoria' => 'Sistemas', 'descripcion' => 'Controles de desplazamiento funcionan', 'orden' => 8, 'obligatorio' => true],

                // Estructura
                ['categoria' => 'Estructura', 'descripcion' => 'Estructura sin grietas o danos visibles', 'orden' => 9, 'obligatorio' => true],
                ['categoria' => 'Estructura', 'descripcion' => 'Ruedas/orugas en buen estado', 'orden' => 10, 'obligatorio' => true],
                ['categoria' => 'Estructura', 'descripcion' => 'Estabilizadores funcionan correctamente', 'orden' => 11, 'obligatorio' => true],

                // Electrico
                ['categoria' => 'Electrico', 'descripcion' => 'Bateria cargada', 'orden' => 12, 'obligatorio' => true],
                ['categoria' => 'Electrico', 'descripcion' => 'Luces de trabajo funcionan', 'orden' => 13, 'obligatorio' => false],
            ];

            foreach ($items as $item) {
                MaquinariaChecklistItem::firstOrCreate(
                    ['plantilla_id' => $plantilla->id, 'descripcion' => $item['descripcion']],
                    ['categoria' => $item['categoria'], 'orden' => $item['orden'], 'obligatorio' => $item['obligatorio']]
                );
            }
        }

        // Checklist generico para cualquier maquinaria
        $plantillaGenerica = MaquinariaChecklistPlantilla::firstOrCreate(
            ['maquinaria_tipo_id' => null, 'nombre' => 'Inspeccion Generica'],
            ['descripcion' => 'Checklist basico aplicable a cualquier maquinaria', 'activa' => true]
        );

        $itemsGenericos = [
            ['categoria' => 'Seguridad', 'descripcion' => 'Dispositivos de seguridad funcionan', 'orden' => 1, 'obligatorio' => true],
            ['categoria' => 'Seguridad', 'descripcion' => 'Parada de emergencia operativa', 'orden' => 2, 'obligatorio' => true],
            ['categoria' => 'Estado General', 'descripcion' => 'Sin danos visibles en la estructura', 'orden' => 3, 'obligatorio' => true],
            ['categoria' => 'Estado General', 'descripcion' => 'Sin fugas de fluidos', 'orden' => 4, 'obligatorio' => true],
            ['categoria' => 'Estado General', 'descripcion' => 'Controles funcionan correctamente', 'orden' => 5, 'obligatorio' => true],
            ['categoria' => 'Mantenimiento', 'descripcion' => 'Niveles de fluidos correctos', 'orden' => 6, 'obligatorio' => false],
            ['categoria' => 'Mantenimiento', 'descripcion' => 'Limpieza general adecuada', 'orden' => 7, 'obligatorio' => false],
        ];

        foreach ($itemsGenericos as $item) {
            MaquinariaChecklistItem::firstOrCreate(
                ['plantilla_id' => $plantillaGenerica->id, 'descripcion' => $item['descripcion']],
                ['categoria' => $item['categoria'], 'orden' => $item['orden'], 'obligatorio' => $item['obligatorio']]
            );
        }
    }
}
