<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\ContratoLiberacion;
use Illuminate\Console\Command;

class MigrarLiberacionesGarantia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contratos:migrar-liberaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar liberaciones antiguas al nuevo sistema de liberaciones parciales';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando migración de liberaciones de garantía...');
        $this->newLine();

        // Buscar contratos con liberación antigua (fecha_liberacion_real no null)
        $contratosLiberados = Contrato::where('tiene_retencion', true)
            ->whereNotNull('fecha_liberacion_real')
            ->get();

        if ($contratosLiberados->isEmpty()) {
            $this->info('No se encontraron contratos con liberaciones antiguas para migrar.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$contratosLiberados->count()} contratos con liberación antigua.");
        $this->newLine();

        $migrados = 0;
        $saltados = 0;

        foreach ($contratosLiberados as $contrato) {
            // Verificar si ya tiene registro en la nueva tabla
            if ($contrato->liberaciones()->count() > 0) {
                $this->warn("⏭️  Contrato {$contrato->codigo} ya migrado, saltando...");
                $saltados++;
                continue;
            }

            try {
                // Crear registro de liberación completa (100%)
                ContratoLiberacion::create([
                    'contrato_id' => $contrato->id,
                    'porcentaje_liberado' => 100,
                    'importe_liberado' => $contrato->importe_retenido,
                    'fecha_liberacion' => $contrato->fecha_liberacion_real,
                    'notas' => 'Migración automática: liberación completa del sistema antiguo',
                    'user_id' => null, // No sabemos quién lo liberó en el sistema antiguo
                ]);

                // Actualizar campos nuevos en el contrato
                $contrato->update([
                    'porcentaje_total_liberado' => 100,
                    'importe_total_liberado' => $contrato->importe_retenido,
                    'estado_garantia' => Contrato::ESTADO_GARANTIA_LIBERADA,
                ]);

                $this->info("✅ Migrado: {$contrato->codigo} - {$contrato->importe_retenido} € (100%)");
                $migrados++;

            } catch (\Exception $e) {
                $this->error("❌ Error migrando {$contrato->codigo}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Migración completada:");
        $this->info("  • Migrados: {$migrados}");
        $this->info("  • Saltados (ya migrados): {$saltados}");

        return Command::SUCCESS;
    }
}
