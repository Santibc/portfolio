
// ============================================
// MÓDULO DE LOGÍSTICA
// ============================================
Route::middleware(['auth'])->prefix('logistica')->group(function () {
    // Zonas de Cobertura
    Route::get('/zonas', [App\Http\Controllers\LogisticaController::class, 'indexZonas'])
        ->name('logistica.zonas.index');
    Route::get('/zonas/create', [App\Http\Controllers\LogisticaController::class, 'createZona'])
        ->name('logistica.zonas.create');
    Route::post('/zonas', [App\Http\Controllers\LogisticaController::class, 'storeZona'])
        ->name('logistica.zonas.store');
    Route::get('/zonas/{id}/edit', [App\Http\Controllers\LogisticaController::class, 'editZona'])
        ->name('logistica.zonas.edit');
    Route::put('/zonas/{id}', [App\Http\Controllers\LogisticaController::class, 'updateZona'])
        ->name('logistica.zonas.update');
    Route::delete('/zonas/{id}', [App\Http\Controllers\LogisticaController::class, 'destroyZona'])
        ->name('logistica.zonas.destroy');

    // Tarifas por Zona
    Route::get('/tarifas', [App\Http\Controllers\LogisticaController::class, 'indexTarifas'])
        ->name('logistica.tarifas.index');
    Route::get('/tarifas/create/{zonaId?}', [App\Http\Controllers\LogisticaController::class, 'createTarifa'])
        ->name('logistica.tarifas.create');
    Route::post('/tarifas', [App\Http\Controllers\LogisticaController::class, 'storeTarifa'])
        ->name('logistica.tarifas.store');
    Route::get('/tarifas/{id}/edit', [App\Http\Controllers\LogisticaController::class, 'editTarifa'])
        ->name('logistica.tarifas.edit');
    Route::put('/tarifas/{id}', [App\Http\Controllers\LogisticaController::class, 'updateTarifa'])
        ->name('logistica.tarifas.update');
    Route::delete('/tarifas/{id}', [App\Http\Controllers\LogisticaController::class, 'destroyTarifa'])
        ->name('logistica.tarifas.destroy');

    // Horarios de Entrega
    Route::get('/horarios', [App\Http\Controllers\LogisticaController::class, 'indexHorarios'])
        ->name('logistica.horarios.index');
    Route::get('/horarios/create/{zonaId?}', [App\Http\Controllers\LogisticaController::class, 'createHorario'])
        ->name('logistica.horarios.create');
    Route::post('/horarios', [App\Http\Controllers\LogisticaController::class, 'storeHorario'])
        ->name('logistica.horarios.store');
    Route::get('/horarios/{id}/edit', [App\Http\Controllers\LogisticaController::class, 'editHorario'])
        ->name('logistica.horarios.edit');
    Route::put('/horarios/{id}', [App\Http\Controllers\LogisticaController::class, 'updateHorario'])
        ->name('logistica.horarios.update');
    Route::delete('/horarios/{id}', [App\Http\Controllers\LogisticaController::class, 'destroyHorario'])
        ->name('logistica.horarios.destroy');

    // Reglas de Capacidad
    Route::get('/capacidad', [App\Http\Controllers\LogisticaController::class, 'indexCapacidad'])
        ->name('logistica.capacidad.index');
    Route::get('/capacidad/create', [App\Http\Controllers\LogisticaController::class, 'createCapacidad'])
        ->name('logistica.capacidad.create');
    Route::post('/capacidad', [App\Http\Controllers\LogisticaController::class, 'storeCapacidad'])
        ->name('logistica.capacidad.store');
    Route::get('/capacidad/{id}/edit', [App\Http\Controllers\LogisticaController::class, 'editCapacidad'])
        ->name('logistica.capacidad.edit');
    Route::put('/capacidad/{id}', [App\Http\Controllers\LogisticaController::class, 'updateCapacidad'])
        ->name('logistica.capacidad.update');
    Route::delete('/capacidad/{id}', [App\Http\Controllers\LogisticaController::class, 'destroyCapacidad'])
        ->name('logistica.capacidad.destroy');

    // API para Checkout
    Route::post('/api/verificar-disponibilidad', [App\Http\Controllers\LogisticaController::class, 'verificarDisponibilidad'])
        ->name('logistica.api.verificar-disponibilidad');
    Route::post('/api/horarios-disponibles', [App\Http\Controllers\LogisticaController::class, 'obtenerHorariosDisponibles'])
        ->name('logistica.api.horarios-disponibles');
});
