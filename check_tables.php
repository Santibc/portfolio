<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== imagenes_proyecto ===\n";
$cols = DB::select('DESCRIBE imagenes_proyecto');
foreach($cols as $col) {
    echo $col->Field . ' | ' . $col->Type . "\n";
}

echo "\n=== documentos_proyecto ===\n";
$cols = DB::select('DESCRIBE documentos_proyecto');
foreach($cols as $col) {
    echo $col->Field . ' | ' . $col->Type . "\n";
}
