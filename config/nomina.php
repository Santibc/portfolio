<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Parámetros de nómina (Colombia)
    |--------------------------------------------------------------------------
    | Valores legales por defecto. Se usan como sugerencia al crear empleados
    | y como base de los cálculos. Cada empleado puede sobrescribir su salario,
    | auxilio y porcentajes; estos son solo los defaults del año.
    */

    // Salario mínimo mensual legal vigente (oficial 2026).
    'smmlv' => (int) env('NOMINA_SMMLV', 1_750_905),

    // Auxilio de transporte mensual (oficial 2026). Aplica a quien devenga
    // hasta 2 SMMLV. Se prorratea por días en cada quincena.
    'auxilio_transporte' => (int) env('NOMINA_AUXILIO', 249_095),

    // Porcentajes de deducción al trabajador (sobre el salario básico).
    'porcentaje_salud'   => (int) env('NOMINA_PCT_SALUD', 4),
    'porcentaje_pension' => (int) env('NOMINA_PCT_PENSION', 4),

    // Interés anual sobre cesantías (12%).
    'factor_intereses_cesantias' => (float) env('NOMINA_FACTOR_INTERESES', 0.12),

    // Divisores de prorrateo.
    'dias_quincena'         => 15,
    'dias_mes'              => 30,
    'divisor_prestaciones'  => 360,  // prima / cesantías / intereses
    'divisor_vacaciones'    => 720,  // 15 días de vacaciones por año
];
