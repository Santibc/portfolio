<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PROYECTOS EN BASE DE DATOS ===\n\n";

$proyectos = App\Models\Proyecto::select('id', 'codigo', 'nombre', 'estado')
    ->with('agricultor:id,name')
    ->get();

if ($proyectos->isEmpty()) {
    echo "No hay proyectos en la base de datos.\n";
} else {
    foreach ($proyectos as $proyecto) {
        echo sprintf(
            "ID: %d | Código: %s | Estado: %s\nNombre: %s\nAgricultor: %s\n\n",
            $proyecto->id,
            $proyecto->codigo,
            $proyecto->estado,
            $proyecto->nombre,
            $proyecto->agricultor->name ?? 'N/A'
        );
    }
    echo 'Total proyectos: '.$proyectos->count()."\n";
}
