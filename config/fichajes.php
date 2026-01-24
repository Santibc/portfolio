<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Fichajes
    |--------------------------------------------------------------------------
    |
    | Configuración general para el módulo de fichajes.
    |
    */

    // Límite de horas normales antes de considerar horas extra
    'horas_jornada_normal' => env('FICHAJES_HORAS_JORNADA', 8),

    // Tiempo de pausa obligatoria (en horas) para jornadas largas
    'pausa_obligatoria' => env('FICHAJES_PAUSA_OBLIGATORIA', 0.5),

    // Horas mínimas para aplicar pausa obligatoria
    'horas_minimas_pausa' => env('FICHAJES_HORAS_MINIMAS_PAUSA', 6),

    // Permitir fichajes fuera de ubicación de obra
    'permitir_fichaje_sin_ubicacion' => env('FICHAJES_SIN_UBICACION', true),

    // Radio máximo (en metros) para considerar fichaje válido en obra
    'radio_maximo_obra' => env('FICHAJES_RADIO_MAXIMO', 500),

    // Auto-validar fichajes después de X días
    'auto_validar_dias' => env('FICHAJES_AUTO_VALIDAR_DIAS', 0), // 0 = desactivado

];
