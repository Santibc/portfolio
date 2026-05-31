<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoMercado;
use App\Enums\EstadoMercadoItem;
use App\Models\ListaMercado;
use App\Models\Mercado;
use App\Models\MercadoItem;
use App\Models\RegistroMercado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MercadosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@admin.com')->first();
        $lista = ListaMercado::with('items.producto.tipo')->where('activa', true)->first();

        if (! $user || ! $lista) {
            $this->command->warn('Falta admin o lista activa; saltando MercadosDemoSeeder.');
            return;
        }

        $listaItems = $lista->items;
        if ($listaItems->isEmpty()) {
            $this->command->warn('La lista activa no tiene items; saltando MercadosDemoSeeder.');
            return;
        }

        // Crea 3 mercados completados (en semanas pasadas) y 1 en progreso (hoy)
        $semanasAtras = [6, 4, 2, 0];

        foreach ($semanasAtras as $idx => $semanas) {
            $enProgreso = ($semanas === 0);

            $iniciado = Carbon::now()->subWeeks($semanas)->setTime(rand(6, 9), rand(0, 59));
            $finalizado = $enProgreso ? null : $iniciado->copy()->addHours(rand(2, 4))->addMinutes(rand(0, 59));

            $mercado = Mercado::create([
                'lista_id'      => $lista->id,
                'user_id'       => $user->id,
                'estado'        => $enProgreso ? EstadoMercado::EnProgreso : EstadoMercado::Completado,
                'iniciado_en'   => $iniciado,
                'finalizado_en' => $finalizado,
                'created_at'    => $iniciado,
                'updated_at'    => $finalizado ?? $iniciado,
            ]);

            foreach ($listaItems as $li) {
                $producto = $li->producto;
                if (! $producto) {
                    continue;
                }

                $estado = $enProgreso
                    ? $this->estadoAleatorioEnProgreso()
                    : (rand(1, 100) <= 90 ? EstadoMercadoItem::Registrado : EstadoMercadoItem::Saltado);

                $registroId = null;

                if ($estado === EstadoMercadoItem::Registrado) {
                    $valor = $this->valorBase($producto->tipo->nombre ?? '');
                    $valor = (int) round($valor * rand(85, 115) / 100);
                    $cantidad = (int) max(1, round($li->cantidad_sugerida * rand(80, 120) / 100));

                    $fechaReg = $finalizado
                        ? $iniciado->copy()->addMinutes(rand(0, (int) max(1, $iniciado->diffInMinutes($finalizado))))
                        : Carbon::now()->subMinutes(rand(5, 120));

                    $registro = RegistroMercado::create([
                        'producto_mercado_id' => $producto->id,
                        'mercado_id'          => $mercado->id,
                        'cantidad'            => $cantidad,
                        'valor'               => $valor,
                        'created_at'          => $fechaReg,
                        'updated_at'          => $fechaReg,
                    ]);
                    $registroId = $registro->id;
                }

                MercadoItem::create([
                    'mercado_id'               => $mercado->id,
                    'lista_mercado_item_id'    => $li->id,
                    'producto_mercado_id'      => $producto->id,
                    'tipo_producto_mercado_id' => $producto->tipo_id,
                    'cantidad_sugerida'        => $li->cantidad_sugerida,
                    'estado'                   => $estado,
                    'registro_mercado_id'      => $registroId,
                    'created_at'               => $iniciado,
                    'updated_at'               => $finalizado ?? $iniciado,
                ]);
            }
        }

        // Genera además registros sueltos (sin mercado) para mostrar histórico
        $productos = \App\Models\ProductoMercado::with('tipo')->activos()->get();
        $registrosSueltos = 80;

        for ($i = 0; $i < $registrosSueltos; $i++) {
            $producto = $productos->random();
            $fecha = Carbon::now()->subDays(rand(0, 90))->setTime(rand(6, 19), rand(0, 59));

            $valor = $this->valorBase($producto->tipo->nombre ?? '');
            $valor = (int) round($valor * rand(80, 120) / 100);

            RegistroMercado::create([
                'producto_mercado_id' => $producto->id,
                'mercado_id'          => null,
                'cantidad'            => rand(1, 15),
                'valor'               => $valor,
                'created_at'          => $fecha,
                'updated_at'          => $fecha,
            ]);
        }

        $this->command->info('Mercados demo sembrados (4 mercados + registros sueltos).');
    }

    private function estadoAleatorioEnProgreso(): EstadoMercadoItem
    {
        $r = rand(1, 100);
        return match (true) {
            $r <= 50 => EstadoMercadoItem::Pendiente,
            $r <= 90 => EstadoMercadoItem::Registrado,
            default  => EstadoMercadoItem::Saltado,
        };
    }

    private function valorBase(string $tipoNombre): int
    {
        return match ($tipoNombre) {
            'Pollo', 'Pescado', 'Cerdo' => rand(15000, 80000),
            'Plaza'                     => rand(2000, 25000),
            'Makro'                     => rand(8000, 60000),
            'Vísceras'                  => rand(10000, 30000),
            'Gaseosas'                  => rand(3000, 15000),
            'Salsamentaria'             => rand(8000, 35000),
            'Aseo', 'Desechables'       => rand(5000, 25000),
            default                     => rand(5000, 30000),
        };
    }
}
