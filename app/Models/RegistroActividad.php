<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroActividad extends Model
{
    protected $table = 'registro_actividades';

    const UPDATED_AT = null;

    protected $fillable = ['usuario_id', 'orden_id', 'accion', 'descripcion', 'datos_extra'];

    protected $casts = [
        'datos_extra' => 'array',
    ];

    const TIPOS_ACCION = [
        // Ordenes
        'orden.creada' => 'Orden Creada',
        'orden.actualizada' => 'Orden Actualizada',
        'orden.copiada' => 'Orden Copiada',
        'orden.anulada' => 'Orden Anulada',
        'orden.comentario_agregado' => 'Comentario Agregado',
        // Pagos
        'pago.registrado' => 'Pago Registrado',
        'pago.aprobado' => 'Pago Aprobado',
        'pago.rechazado' => 'Pago Rechazado',
        // Piezas / Operario
        'pieza.avance_actualizado' => 'Avance Actualizado',
        'pieza.avance_disminuido' => 'Avance Disminuido',
        'pieza.transferida' => 'Pieza Transferida',
        'pieza.liberada_a_pool' => 'Pieza Liberada a Pool',
        'pieza.tomada_de_pool' => 'Pieza Tomada de Pool',
        'pieza.entregada' => 'Pieza Entregada',
        'pieza.foto_subida' => 'Foto de Pieza Subida',
        // Entregas
        'entrega.foto_subida' => 'Foto de Entrega Subida',
        // Garantias
        'garantia.registrada' => 'Garantia Registrada',
        'garantia.en_proceso' => 'Garantia en Proceso',
        'garantia.completada' => 'Garantia Completada',
        'garantia.reentregada' => 'Garantia Reentregada',
        // Clientes
        'cliente.creado' => 'Cliente Creado',
        'cliente.actualizado' => 'Cliente Actualizado',
        // Catalogo Items
        'catalogo_item.creado' => 'Item Creado',
        'catalogo_item.actualizado' => 'Item Actualizado',
        // Bosquejos Matriz
        'bosquejo_grupo.creado' => 'Grupo Bosquejo Creado',
        'bosquejo_grupo.actualizado' => 'Grupo Bosquejo Actualizado',
        'bosquejo_grupo.eliminado' => 'Grupo Bosquejo Eliminado',
        'bosquejo.creado' => 'Bosquejo Creado',
        'bosquejo.actualizado' => 'Bosquejo Actualizado',
        'bosquejo.eliminado' => 'Bosquejo Eliminado',
        // Tabla de Precios
        'tabla_precios.precios_actualizados' => 'Precios Actualizados',
        'tabla_precios.servicio_creado' => 'Servicio Creado',
        'tabla_precios.servicio_actualizado' => 'Servicio Actualizado',
        'tabla_precios.servicio_eliminado' => 'Servicio Eliminado',
        'tabla_precios.importacion' => 'Importacion de Precios',
        // Configuracion
        'configuracion.actualizada' => 'Configuracion Actualizada',
        'configuracion.logo_actualizado' => 'Logo Actualizado',
        'configuracion.logo_eliminado' => 'Logo Eliminado',
        // Usuarios / Auth
        'usuario.inicio_sesion' => 'Inicio de Sesion',
        'usuario.cierre_sesion' => 'Cierre de Sesion',
        'usuario.creado' => 'Usuario Creado',
        'usuario.actualizado' => 'Usuario Actualizado',
        'usuario.eliminado' => 'Usuario Eliminado',
        // Sistema
        'sistema.borradores_eliminados' => 'Borradores Eliminados',
    ];

    const COLORES_CATEGORIA = [
        'orden' => 'primary',
        'pago' => 'success',
        'pieza' => 'info',
        'entrega' => 'info',
        'garantia' => 'warning',
        'cliente' => 'secondary',
        'catalogo_item' => 'secondary',
        'bosquejo_grupo' => 'dark',
        'bosquejo' => 'dark',
        'tabla_precios' => 'danger',
        'configuracion' => 'danger',
        'usuario' => 'dark',
        'sistema' => 'danger',
    ];

    public static function badgeAccion(string $accion): string
    {
        $etiqueta = self::TIPOS_ACCION[$accion] ?? $accion;
        $categoria = explode('.', $accion)[0];
        // bosquejo_grupo usa prefijo bosquejo_grupo
        $color = self::COLORES_CATEGORIA[$categoria] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . e($etiqueta) . '</span>';
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new \RuntimeException('Los registros de actividad no pueden ser modificados.');
    }

    public function delete()
    {
        throw new \RuntimeException('Los registros de actividad no pueden ser eliminados.');
    }
}
