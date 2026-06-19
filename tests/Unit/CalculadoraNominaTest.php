<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CalculadoraNomina;
use PHPUnit\Framework\TestCase;

class CalculadoraNominaTest extends TestCase
{
    /** El básico es el salario prorrateado por días sobre 30 (round half-up). */
    public function test_basico_quincenal(): void
    {
        $this->assertSame(2_500_000, CalculadoraNomina::basico(5_000_000, 15));
        // 1.575.452,5 -> 1.575.453 (half-up, como el Excel)
        $this->assertSame(1_575_453, CalculadoraNomina::basico(3_150_905, 15));
        // 875.452,5 -> 875.453
        $this->assertSame(875_453, CalculadoraNomina::basico(1_750_905, 15));
    }

    public function test_auxilio_prorrateado(): void
    {
        // 249.095 * 15 / 30 = 124.547,5 -> 124.548 (coincide con el Excel)
        $this->assertSame(124_548, CalculadoraNomina::auxilioProrrateado(249_095, 15, true));
        $this->assertSame(0, CalculadoraNomina::auxilioProrrateado(249_095, 15, false));
    }

    public function test_deduccion_sobre_basico(): void
    {
        $this->assertSame(100_000, CalculadoraNomina::deduccion(2_500_000, 4));
        // 63.018,12 -> 63.018
        $this->assertSame(63_018, CalculadoraNomina::deduccion(1_575_453, 4));
        // 35.018,12 -> 35.018
        $this->assertSame(35_018, CalculadoraNomina::deduccion(875_453, 4));
    }

    /** Línea completa de Luz Yamile (sin auxilio, con bono). */
    public function test_liquidar_linea_administradora(): void
    {
        $linea = CalculadoraNomina::liquidarLinea(5_000_000, 15, 249_095, false, 500_000, 4, 4);

        $this->assertSame(2_500_000, $linea['basico']);
        $this->assertSame(0, $linea['auxilio']);
        $this->assertSame(100_000, $linea['salud']);
        $this->assertSame(100_000, $linea['pension']);
        $this->assertSame(3_000_000, $linea['total_devengado']);
        $this->assertSame(200_000, $linea['total_deducido']);
        $this->assertSame(2_800_000, $linea['neto']);
    }

    /** Línea de Luz Mery (con auxilio, sin bono). */
    public function test_liquidar_linea_con_auxilio(): void
    {
        $linea = CalculadoraNomina::liquidarLinea(3_150_905, 15, 249_095, true, 0, 4, 4);

        $this->assertSame(1_575_453, $linea['basico']);
        $this->assertSame(124_548, $linea['auxilio']);
        $this->assertSame(1_700_001, $linea['total_devengado']);
        $this->assertSame(126_036, $linea['total_deducido']);
        $this->assertSame(1_573_965, $linea['neto']);
    }

    /** Empleado a SMMLV 2026 con bono (Diana). */
    public function test_liquidar_linea_smmlv_con_bono(): void
    {
        $linea = CalculadoraNomina::liquidarLinea(1_750_905, 15, 249_095, true, 150_000, 4, 4);

        $this->assertSame(875_453, $linea['basico']);
        $this->assertSame(124_548, $linea['auxilio']);
        $this->assertSame(70_036, $linea['total_deducido']);
        $this->assertSame(1_150_001, $linea['total_devengado']);
        $this->assertSame(1_079_965, $linea['neto']);
    }

    public function test_prestaciones(): void
    {
        // Prima/cesantías de un semestre (180 días) para SMMLV + auxilio = 2.000.000.
        $this->assertSame(1_000_000, CalculadoraNomina::prima(1_750_905, 249_095, 180));
        $this->assertSame(1_000_000, CalculadoraNomina::cesantias(1_750_905, 249_095, 180));
        // Intereses 12% anual sobre 1.000.000 por medio año (180/360) = 60.000.
        $this->assertSame(60_000, CalculadoraNomina::intereses(1_000_000, 180, 0.12));
        // Vacaciones: 15 días por año => salario/2 para un año completo.
        $this->assertSame(875_453, CalculadoraNomina::vacaciones(1_750_905, 360));
    }
}
