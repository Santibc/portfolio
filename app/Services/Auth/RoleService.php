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
    public function assignDefaultRole(User $user, string $roleType = 'Administrador'): void
    {
        try {
            $role = Role::where('name', $roleType)->first();

            if (!$role) {
                Log::warning("Rol '{$roleType}' no encontrado.");
                return;
            }

            $user->assignRole($role);
            Log::info("Rol '{$role->name}' asignado al usuario {$user->id}");
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

            $role = Role::where('name', $newRole)->first();

            if (!$role) {
                Log::warning("Rol '{$newRole}' no encontrado.");
                return false;
            }

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
     * Agregar un rol adicional al usuario.
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
     * Jerarquia de roles (mayor numero = mayor jerarquia).
     */
    public const ROLE_HIERARCHY = [
        'Administrador' => 4,
        'Recepcion' => 3,
        'Contabilidad' => 2,
        'Operario' => 1,
    ];

    /**
     * Rutas de dashboard por rol.
     */
    protected const ROLE_DASHBOARD_ROUTES = [
        'Administrador' => 'admin.configuracion',
        'Recepcion' => 'recepcion.panel',
        'Contabilidad' => 'contabilidad.panel',
        'Operario' => 'operario.panel',
    ];

    /**
     * Obtener la ruta del dashboard segun el rol de mayor jerarquia del usuario.
     */
    public function getDashboardRoute(User $user): string
    {
        $topRole = $this->getTopRole($user);

        return self::ROLE_DASHBOARD_ROUTES[$topRole] ?? 'dashboard';
    }

    /**
     * Obtener el rol de mayor jerarquia del usuario.
     */
    public function getTopRole(User $user): ?string
    {
        $roles = $user->getRoleNames();

        if ($roles->isEmpty()) {
            return null;
        }

        return $roles->sortByDesc(function ($role) {
            return self::ROLE_HIERARCHY[$role] ?? 0;
        })->first();
    }

    /**
     * Obtener el nivel de jerarquia del usuario.
     */
    public function getJerarquia(User $user): int
    {
        $topRole = $this->getTopRole($user);

        return self::ROLE_HIERARCHY[$topRole] ?? 0;
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
}
