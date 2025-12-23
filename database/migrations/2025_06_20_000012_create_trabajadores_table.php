<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo_relacion', ['propio', 'subcontrata'])->default('propio');
            $table->string('nombre', 100);
            $table->string('apellidos', 150);
            $table->string('dni', 20)->unique();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_alta');
            $table->date('fecha_baja')->nullable();
            $table->string('categoria_convenio', 100)->nullable();
            $table->decimal('salario_bruto_mensual', 10, 2)->nullable();
            $table->decimal('coste_empresa_dia', 10, 2)->nullable()->comment('Calculado: salario + SS + indirectos');
            $table->decimal('coste_hora', 8, 2)->nullable();
            $table->integer('vacaciones_anuales')->default(22)->comment('Días de vacaciones al año');
            $table->decimal('vacaciones_acumuladas', 5, 2)->default(0)->comment('Días acumulados pendientes');
            $table->date('antiguedad')->nullable()->comment('Fecha inicio antigüedad');
            $table->foreignId('subcontrata_id')->nullable()->constrained('subcontratas')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
