<?php

namespace App\Services\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    /**
     * Asignar rol por defecto a un usuario recién registrado.
     *
     * @param User $user
     * @param string $roleType
     * @return void
     */
    public function assignDefaultRole(User $user, string $roleType = 'Inversionista'): void
    {
        try {
            // Verificar si el rol existe
            $role = Role::where('name', $roleType)->first();

            if (!$role) {
                Log::warning("Rol '{$roleType}' no encontrado. Asignando rol Inversionista por defecto.");
                $role = Role::where('name', 'Inversionista')->first();
            }

            if ($role) {
                $user->assignRole($role);
                Log::info("Rol '{$role->name}' asignado al usuario {$user->id}");
            } else {
                Log::error("No se pudo asignar ningún rol al usuario {$user->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error asignando rol al usuario {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Cambiar el rol de un usuario.
     *
     * @param User $user
     * @param string $newRole
     * @return bool
     */
    public function changeUserRole(User $user, string $newRole): bool
    {
        try {
            DB::beginTransaction();

            // Verificar que el rol existe
            $role = Role::where('name', $newRole)->first();

            if (!$role) {
                Log::warning("Rol '{$newRole}' no encontrado.");
                return false;
            }

            // Remover todos los roles actuales y asignar el nuevo
            $user->syncRoles([$role]);

            DB::commit();

            Log::info("Rol del usuario {$user->id} cambiado a '{$newRole}'");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cambiando rol del usuario {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Agregar un rol adicional al usuario (múltiples roles).
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    public function addRoleToUser(User $user, string $roleName): bool
    {
        try {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                Log::warning("Rol '{$roleName}' no encontrado.");
                return false;
            }

            $user->assignRole($role);

            Log::info("Rol '{$roleName}' agregado al usuario {$user->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error agregando rol al usuario {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un usuario tiene permisos específicos.
     *
     * @param User $user
     * @param string|array $permissions
     * @return bool
     */
    public function hasPermissions(User $user, $permissions): bool
    {
        if (is_array($permissions)) {
            return $user->hasAllPermissions($permissions);
        }

        return $user->hasPermissionTo($permissions);
    }

    /**
     * Obtener la ruta del dashboard según el rol del usuario.
     *
     * @param User $user
     * @return string
     */
    public function getDashboardRoute(User $user): string
    {
        if ($user->hasRole('Administrador')) {
            return 'admin.dashboard';
        }

        if ($user->hasRole('Supervisor')) {
            return 'supervisor.dashboard';
        }

        if ($user->hasRole('Agricultor')) {
            return 'farmer.dashboard';
        }

        if ($user->hasRole('Vendedor')) {
            return 'vendedor.dashboard';
        }

        // Por defecto, inversionista
        return 'inversionista.dashboard';
    }

    /**
     * Verificar si un usuario puede acceder a una sección.
     *
     * @param User $user
     * @param array $allowedRoles
     * @return bool
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

    /**
     * Verificar si el usuario necesita completar KYC.
     *
     * @param User $user
     * @return bool
     */
    public function requiresKyc(User $user): bool
    {
        // Solo los inversionistas requieren KYC aprobado
        return $user->hasRole('Inversionista') && $user->kyc_status !== 'aprobado';
    }
}
