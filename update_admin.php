<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::find(1);
$user->password = bcrypt('admin123');
$user->save();
echo "Password actualizado para: " . $user->email;
