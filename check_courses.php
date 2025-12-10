<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

echo "=== ROLES DISPONIBLES ===" . PHP_EOL;
$roles = Role::all();
foreach ($roles as $r) {
    echo "ID: {$r->id} - Nombre: \"{$r->name}\"" . PHP_EOL;
}

echo PHP_EOL . "=== USUARIOS ===" . PHP_EOL;
$users = User::with('roles')->get();
foreach ($users as $u) {
    $rol = $u->roles->first()->name ?? 'Sin rol';
    echo "- {$u->name} ({$u->email}) - Rol: {$rol}" . PHP_EOL;
}

echo PHP_EOL . "=== TABLA course_user ===" . PHP_EOL;
$pivot = DB::table('course_user')->get();
if ($pivot->isEmpty()) {
    echo "(vacía)" . PHP_EOL;
} else {
    print_r($pivot->toArray());
}
