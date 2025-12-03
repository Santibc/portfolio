<?php

namespace App\Services\Project;

use App\Models\CategoriaProyecto;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;

class ProjectCodeGeneratorService
{
    /**
     * Generar un código único para un proyecto
     * Formato: {CODIGO_CATEGORIA}-{AÑO}-{SECUENCIAL}
     * Ejemplo: STK-2025-001, EAR-2025-002
     *
     * @param CategoriaProyecto $categoria
     * @return string
     */
    public function generateCode(CategoriaProyecto $categoria): string
    {
        $year = date('Y');
        $prefix = strtoupper($categoria->codigo) . '-' . $year . '-';

        // Buscar el último código de esta categoría en este año
        $lastCode = Proyecto::where('codigo', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(codigo, -3) AS UNSIGNED) DESC')
            ->value('codigo');

        if ($lastCode) {
            // Extraer el número secuencial del último código
            $lastNumber = (int) substr($lastCode, -3);
            $newNumber = $lastNumber + 1;
        } else {
            // Primer proyecto de esta categoría en este año
            $newNumber = 1;
        }

        // Formatear el número con 3 dígitos
        $sequential = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return $prefix . $sequential;
    }

    /**
     * Verificar si un código ya existe
     *
     * @param string $code
     * @return bool
     */
    public function codeExists(string $code): bool
    {
        return Proyecto::where('codigo', $code)->exists();
    }

    /**
     * Generar un código único con reintentos en caso de colisión
     *
     * @param CategoriaProyecto $categoria
     * @param int $maxAttempts
     * @return string
     * @throws \RuntimeException
     */
    public function generateUniqueCode(CategoriaProyecto $categoria, int $maxAttempts = 5): string
    {
        $attempts = 0;

        do {
            $code = $this->generateCode($categoria);
            $attempts++;

            if (!$this->codeExists($code)) {
                return $code;
            }

            // Pequeña pausa para evitar colisiones en creaciones simultáneas
            usleep(100000); // 100ms

        } while ($attempts < $maxAttempts);

        throw new \RuntimeException(
            "No se pudo generar un código único después de {$maxAttempts} intentos"
        );
    }
}
