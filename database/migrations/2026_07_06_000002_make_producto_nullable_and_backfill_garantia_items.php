<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * (1) El producto deja de ser obligatorio en la garantía (ahora los productos
     *     reclamados viven en garantia_items y son opcionales).
     * (2) Se copian las garantías existentes (producto único) a garantia_items
     *     para no perder historial, con cantidad 1.
     */
    public function up(): void
    {
        // MODIFY vía SQL crudo para no depender de doctrine/dbal.
        DB::statement('ALTER TABLE garantias MODIFY producto_id BIGINT UNSIGNED NULL');

        // Backfill: solo garantías con producto que aún no tengan items.
        $garantias = DB::table('garantias')
            ->whereNotNull('producto_id')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('garantia_items')
                    ->whereColumn('garantia_items.garantia_id', 'garantias.id');
            })
            ->get(['id', 'producto_id', 'variante_producto_id', 'created_at', 'updated_at']);

        foreach ($garantias as $g) {
            DB::table('garantia_items')->insert([
                'garantia_id' => $g->id,
                'producto_id' => $g->producto_id,
                'variante_producto_id' => $g->variante_producto_id,
                'cantidad' => 1,
                'created_at' => $g->created_at,
                'updated_at' => $g->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // Se deja producto_id nullable (revertirlo a NOT NULL fallaría si hay garantías
        // registradas sin producto). Solo se limpian los items copiados desde el producto único.
        DB::table('garantia_items')->delete();
    }
};
