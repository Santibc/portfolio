<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Estilos de ramo (Romántico, Elegante, Silvestre)
        Schema::create('estilos_ramo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Romántico, Elegante, Silvestre
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->decimal('precio_base', 10, 2)->default(0); // Precio base del estilo
            $table->integer('flores_minimo')->default(5); // Mínimo de flores requeridas
            $table->integer('flores_maximo')->default(30); // Máximo de flores permitidas
            $table->string('icono')->nullable(); // Clase de icono (bi-heart, bi-star, etc.)
            $table->string('color_principal')->nullable(); // Color para UI
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Envolturas de ramo (Papel Kraft, Celofán, Tela Premium)
        Schema::create('envolturas_ramo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Papel Kraft, Celofán, Tela Premium
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->decimal('precio_adicional', 10, 2)->default(0); // Costo adicional
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Modificar tabla ramos_personalizados para nuevo flujo
        // Usar raw SQL para modificar la constraint sin Doctrine DBAL
        DB::statement('ALTER TABLE ramos_personalizados MODIFY COLUMN tamano_ramo_id BIGINT UNSIGNED NULL');

        Schema::table('ramos_personalizados', function (Blueprint $table) {
            // Agregar nuevas columnas
            $table->unsignedBigInteger('estilo_ramo_id')->nullable()->after('session_id');
            $table->unsignedBigInteger('envoltura_ramo_id')->nullable()->after('estilo_ramo_id');
            $table->integer('paso_actual')->default(1)->after('envoltura_ramo_id');
            $table->decimal('precio_envoltura', 10, 2)->default(0)->after('precio_base');

            // Agregar foreign keys
            $table->foreign('estilo_ramo_id')->references('id')->on('estilos_ramo')->onDelete('set null');
            $table->foreign('envoltura_ramo_id')->references('id')->on('envolturas_ramo')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ramos_personalizados', function (Blueprint $table) {
            $table->dropForeign(['estilo_ramo_id']);
            $table->dropForeign(['envoltura_ramo_id']);
            $table->dropColumn(['estilo_ramo_id', 'envoltura_ramo_id', 'paso_actual', 'precio_envoltura']);
        });

        Schema::dropIfExists('envolturas_ramo');
        Schema::dropIfExists('estilos_ramo');
    }
};
