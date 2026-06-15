<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Motor de cálculo de nómina (Colombia). Clase estática y pura: NO toca la base
 * de datos, así es 100% testeable y es la única fuente de la matemática de
 * nómina y prestaciones. El frontend (Alpine) replica estas fórmulas para el
 * cálculo en vivo, pero el servidor siempre recalcula aquí al guardar.
 *
 * Todo el dinero es entero (COP). Se redondea al peso más cercano (round
 * half-up), validado contra el Excel del cliente.
 */
final class CalculadoraNomina
{
    /** Básico del período = salario mensual prorrateado por días (sobre 30). */
    public static function basico(int $salarioBase, int $dias): int
    {
        return (int) round($salarioBase * $dias / 30);
    }

    /** Auxilio de transporte prorrateado por días; 0 si el empleado no lo recibe. */
    public static function auxilioProrrateado(int $auxilioMensual, int $dias, bool $tieneAuxilio): int
    {
        return $tieneAuxilio ? (int) round($auxilioMensual * $dias / 30) : 0;
    }

    /** Deducción (salud o pensión) = porcentaje sobre el BÁSICO (no sobre devengado). */
    public static function deduccion(int $basico, int $porcentaje): int
    {
        return (int) round($basico * $porcentaje / 100);
    }

    public static function totalDevengado(int $basico, int $bono, int $auxilio): int
    {
        return $basico + $bono + $auxilio;
    }

    public static function totalDeducido(int $salud, int $pension): int
    {
        return $salud + $pension;
    }

    public static function neto(int $totalDevengado, int $totalDeducido): int
    {
        return $totalDevengado - $totalDeducido;
    }

    /**
     * Liquida una línea completa y devuelve el snapshot de todos los montos.
     *
     * @return array{basico:int,auxilio:int,bono:int,salud:int,pension:int,total_devengado:int,total_deducido:int,neto:int}
     */
    public static function liquidarLinea(
        int $salarioBase,
        int $dias,
        int $auxilioMensual,
        bool $tieneAuxilio,
        int $bono,
        int $porcentajeSalud,
        int $porcentajePension
    ): array {
        $basico = self::basico($salarioBase, $dias);
        $auxilio = self::auxilioProrrateado($auxilioMensual, $dias, $tieneAuxilio);
        $salud = self::deduccion($basico, $porcentajeSalud);
        $pension = self::deduccion($basico, $porcentajePension);
        $devengado = self::totalDevengado($basico, $bono, $auxilio);
        $deducido = self::totalDeducido($salud, $pension);

        return [
            'basico' => $basico,
            'auxilio' => $auxilio,
            'bono' => $bono,
            'salud' => $salud,
            'pension' => $pension,
            'total_devengado' => $devengado,
            'total_deducido' => $deducido,
            'neto' => self::neto($devengado, $deducido),
        ];
    }

    // ----- Prestaciones sociales -----

    /** Prima de servicios = (salario + auxilio) × días / 360. */
    public static function prima(int $salario, int $auxilio, int $dias): int
    {
        return (int) round(($salario + $auxilio) * $dias / 360);
    }

    /** Cesantías = (salario + auxilio) × días / 360. */
    public static function cesantias(int $salario, int $auxilio, int $dias): int
    {
        return (int) round(($salario + $auxilio) * $dias / 360);
    }

    /** Intereses sobre cesantías = cesantías × factor (12%) × días / 360. */
    public static function intereses(int $cesantias, int $dias, float $factor): int
    {
        return (int) round($cesantias * $factor * $dias / 360);
    }

    /** Vacaciones = salario × días / 720 (15 días por año, sin auxilio). */
    public static function vacaciones(int $salario, int $dias): int
    {
        return (int) round($salario * $dias / 720);
    }
}
