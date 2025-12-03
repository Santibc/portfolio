<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case BORRADOR = 'borrador';
    case EN_REVISION = 'en_revision';
    case RECHAZADO = 'rechazado';
    case APROBADO = 'aprobado';
    case EN_RECAUDACION = 'en_recaudacion';
    case FONDEADO = 'fondeado';
    case EN_EJECUCION = 'en_ejecucion';
    case EN_COSECHA = 'en_cosecha';
    case FINALIZADO = 'finalizado';
    case CANCELADO = 'cancelado';

    /**
     * Obtener la etiqueta legible del estado
     */
    public function label(): string
    {
        return match($this) {
            self::BORRADOR => 'Borrador',
            self::EN_REVISION => 'En Revisión',
            self::RECHAZADO => 'Rechazado',
            self::APROBADO => 'Aprobado',
            self::EN_RECAUDACION => 'En Recaudación',
            self::FONDEADO => 'Fondeado',
            self::EN_EJECUCION => 'En Ejecución',
            self::EN_COSECHA => 'En Cosecha',
            self::FINALIZADO => 'Finalizado',
            self::CANCELADO => 'Cancelado',
        };
    }

    /**
     * Obtener el color del badge para el estado
     */
    public function color(): string
    {
        return match($this) {
            self::BORRADOR => 'secondary',
            self::EN_REVISION => 'warning',
            self::RECHAZADO => 'danger',
            self::APROBADO => 'success',
            self::EN_RECAUDACION => 'primary',
            self::FONDEADO => 'success',
            self::EN_EJECUCION => 'primary',
            self::EN_COSECHA => 'warning',
            self::FINALIZADO => 'success',
            self::CANCELADO => 'danger',
        };
    }

    /**
     * Determinar si el proyecto puede ser editado en este estado
     */
    public function canEdit(): bool
    {
        return $this === self::BORRADOR || $this === self::RECHAZADO;
    }

    /**
     * Determinar si el proyecto puede ser enviado a revisión
     */
    public function canSubmitForReview(): bool
    {
        return $this === self::BORRADOR;
    }

    /**
     * Determinar si el proyecto puede ser aprobado
     */
    public function canBeApproved(): bool
    {
        return $this === self::EN_REVISION;
    }

    /**
     * Determinar si el proyecto puede ser rechazado
     */
    public function canBeRejected(): bool
    {
        return $this === self::EN_REVISION;
    }

    /**
     * Determinar si el proyecto está activo para inversiones
     */
    public function isActiveForInvestment(): bool
    {
        return $this === self::EN_RECAUDACION;
    }

    /**
     * Obtener todos los estados como array de opciones
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Obtener estados que representan proyectos activos
     */
    public static function activeStates(): array
    {
        return [
            self::EN_RECAUDACION->value,
            self::FONDEADO->value,
            self::EN_EJECUCION->value,
            self::EN_COSECHA->value,
        ];
    }
}
