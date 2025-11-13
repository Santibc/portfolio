<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear el rol de técnico si no existe
        if (!Role::where('name', 'tecnico')->exists()) {
            Role::create(['name' => 'tecnico', 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar el rol de técnico
        $role = Role::where('name', 'tecnico')->first();
        if ($role) {
            $role->delete();
        }
    }
};
