<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Asignar rol por defecto a un usuario recién registrado.
     */
    public function assignDefaultRole(User $user, string $roleType = 'Administrador'): void
    {
        try {
            $role = Role::where('name', $roleType)->first();

            if (! $role) {
                Log::warning("Rol '{$roleType}' no encontrado.");

                return;
            }

            $user->assignRole($role);
            Log::info("Rol '{$role->name}' asignado al usuario {$user->id}");
        } catch (\Exception $e) {
            Log::error("Error asignando rol al usuario {$user->id}: ".$e->getMessage());
        }
    }

    /**
     * Cambiar el rol de un usuario.
     */
    public function changeUserRole(User $user, string $newRole): bool
    {
        try {
            DB::beginTransaction();

            $role = Role::where('name', $newRole)->first();

            if (! $role) {
                Log::warning("Rol '{$newRole}' no encontrado.");

                return false;
            }

            $user->syncRoles([$role]);

            DB::commit();

            Log::info("Rol del usuario {$user->id} cambiado a '{$newRole}'");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cambiando rol del usuario {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Agregar un rol adicional al usuario.
     */
    public function addRoleToUser(User $user, string $roleName): bool
    {
        try {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                Log::warning("Rol '{$roleName}' no encontrado.");

                return false;
            }

            $user->assignRole($role);

            Log::info("Rol '{$roleName}' agregado al usuario {$user->id}");

            return true;
        } catch (\Exception $e) {
            Log::error("Error agregando rol al usuario {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Verificar si un usuario tiene permisos específicos.
     *
     * @param  string|array  $permissions
     */
    public function hasPermissions(User $user, $permissions): bool
    {
        if (is_array($permissions)) {
            return $user->hasAllPermissions($permissions);
        }

        return $user->hasPermissionTo($permissions);
    }

    /**
     * Obtener la ruta del dashboard.
     */
    public function getDashboardRoute(User $user): string
    {
        return 'dashboard';
    }

    /**
     * Verificar si un usuario puede acceder a una sección.
     */
    public function canAccessSection(User $user, array $allowedRoles): bool
    {
        return $user->hasAnyRole($allowedRoles);
    }

    /**
     * Obtener todos los roles disponibles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::all();
    }
}
