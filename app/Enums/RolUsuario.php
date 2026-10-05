<?php

declare(strict_types=1);

namespace App\Enums;

/** Roles de Spatie que se asignan desde el módulo de usuarios (un rol por usuario). */
enum RolUsuario: string
{
    case Admin  = 'admin';
    case Ventas = 'ventas';

    public function label(): string
    {
        return match ($this) {
            self::Admin  => 'Administrador',
            self::Ventas => 'Ventas',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::Admin  => 'Acceso completo a todos los módulos.',
            self::Ventas => 'Solo la caja: abrir y cerrar turno y registrar ventas.',
        };
    }

    /** Variante de <x-badge>. */
    public function badge(): string
    {
        return match ($this) {
            self::Admin  => 'primary',
            self::Ventas => 'accent',
        };
    }

    /** @return array<string,string> valor => etiqueta, para <x-select> */
    public static function opciones(): array
    {
        $opciones = [];
        foreach (self::cases() as $rol) {
            $opciones[$rol->value] = $rol->label();
        }

        return $opciones;
    }
}
