<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RolUsuario;
use App\Models\User;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/** Alta, edición y baja de usuarios del sistema con su rol (uno por usuario). */
class UsuarioService
{
    public function crear(array $datos): User
    {
        return DB::transaction(function () use ($datos) {
            $usuario = User::create([
                'name'     => $datos['name'],
                'email'    => $datos['email'],
                'password' => Hash::make($datos['password']),
            ]);
            $usuario->forceFill(['email_verified_at' => now()])->save();

            $this->asignarRol($usuario, RolUsuario::from($datos['rol']));

            return $usuario;
        });
    }

    public function actualizar(User $usuario, array $datos, User $actor): User
    {
        $rol = RolUsuario::from($datos['rol']);

        if ($rol !== RolUsuario::Admin && $usuario->isAdmin()) {
            if ($usuario->is($actor)) {
                throw new DomainException('No puedes quitarte a ti mismo el rol de administrador.');
            }
            $this->asegurarOtroAdmin($usuario);
        }

        return DB::transaction(function () use ($usuario, $datos, $rol) {
            $usuario->fill(['name' => $datos['name'], 'email' => $datos['email']]);
            if (! empty($datos['password'])) {
                $usuario->password = Hash::make($datos['password']);
            }
            $usuario->save();

            $this->asignarRol($usuario, $rol);

            return $usuario->fresh();
        });
    }

    public function eliminar(User $usuario, User $actor): void
    {
        if ($usuario->is($actor)) {
            throw new DomainException('No puedes eliminar tu propio usuario.');
        }
        if ($usuario->isAdmin()) {
            $this->asegurarOtroAdmin($usuario);
        }

        $foto = $usuario->profile_photo;

        try {
            DB::transaction(fn () => $usuario->delete());
        } catch (QueryException) {
            // Ventas, turnos, gastos… referencian al usuario (FK restrict): conservarlo para la historia.
            throw new DomainException(
                "No se puede eliminar a {$usuario->name}: tiene movimientos registrados (ventas, turnos, gastos…). "
                .'Si ya no debe entrar al sistema, cámbiale la contraseña.'
            );
        }

        if ($foto && File::exists(public_path('uploads/profile-photos/'.$foto))) {
            File::delete(public_path('uploads/profile-photos/'.$foto));
        }
    }

    private function asignarRol(User $usuario, RolUsuario $rol): void
    {
        // findOrCreate: el rol "ventas" existe aunque la BD no haya corrido el seeder actualizado.
        $usuario->syncRoles([Role::findOrCreate($rol->value, 'web')]);
    }

    private function asegurarOtroAdmin(User $usuario): void
    {
        $otrosAdmins = User::role(RolUsuario::Admin->value)->whereKeyNot($usuario->id)->exists();
        if (! $otrosAdmins) {
            throw new DomainException('Debe quedar al menos un administrador en el sistema.');
        }
    }
}
