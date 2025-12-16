<?php

namespace App\Services\Farmer;

use App\Models\User;
use App\Models\PerfilAgricultor;
use App\Models\FamiliaAgricultor;
use App\Notifications\FarmerWelcomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FarmerCreationService
{
    /**
     * Crear un agricultor con su perfil completo
     *
     * @param array $userData Datos del usuario
     * @param array $perfilData Datos del perfil de agricultor
     * @param array $familiaData Array de familiares
     * @param User $admin Usuario admin que crea
     * @return User
     * @throws \Exception
     */
    public function createFarmerWithProfile(
        array $userData,
        array $perfilData = [],
        array $familiaData = [],
        User $admin
    ): User {
        try {
            DB::beginTransaction();

            // Generar contraseña temporal (documento de identidad)
            $temporaryPassword = $this->generateTemporaryPassword($userData);

            // Crear usuario agricultor
            $farmer = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($temporaryPassword),
                'telefono' => $userData['telefono'] ?? null,
                'documento_identidad' => $userData['documento_identidad'] ?? null,
                'tipo_documento' => $userData['tipo_documento'] ?? 'CC',
                'fecha_nacimiento' => $userData['fecha_nacimiento'] ?? null,
                'pais' => $userData['pais'] ?? 'Colombia',
                'ciudad' => $userData['ciudad'] ?? null,
                'direccion' => $userData['direccion'] ?? null,
                'foto_perfil' => $userData['foto_perfil'] ?? null,
                'activo' => true,
                'kyc_status' => 'pendiente',
                'creado_por_admin' => true,
                'admin_creador_id' => $admin->id,
            ]);

            // Asignar rol de Agricultor
            $farmer->assignRole('Agricultor');

            // Crear perfil de agricultor si hay datos
            if (!empty($perfilData)) {
                $this->createPerfilAgricultor($farmer, $perfilData);
            }

            // Crear familiares si hay datos
            if (!empty($familiaData)) {
                $this->createFamiliaAgricultor($farmer, $familiaData);
            }

            // Enviar notificación de bienvenida con credenciales
            $this->sendWelcomeEmail($farmer, $temporaryPassword);

            DB::commit();

            Log::info("Agricultor creado por admin", [
                'farmer_id' => $farmer->id,
                'admin_id' => $admin->id,
                'email' => $farmer->email
            ]);

            return $farmer;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creando agricultor: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear solo el agricultor (Fase 1 del formulario)
     *
     * @param array $userData
     * @param User $admin
     * @return User
     * @throws \Exception
     */
    public function createFarmerBasic(array $userData, User $admin): User
    {
        try {
            DB::beginTransaction();

            // Generar contraseña temporal
            $temporaryPassword = $this->generateTemporaryPassword($userData);

            // Verificar si ya existe un usuario con ese email
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                throw new \Exception("Ya existe un usuario con el email: {$userData['email']}");
            }

            // Verificar si ya existe un usuario con ese documento
            if (!empty($userData['documento_identidad'])) {
                $existingDoc = User::where('documento_identidad', $userData['documento_identidad'])->first();
                if ($existingDoc) {
                    throw new \Exception("Ya existe un usuario con el documento: {$userData['documento_identidad']}");
                }
            }

            // Crear usuario agricultor
            $farmer = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($temporaryPassword),
                'telefono' => $userData['telefono'] ?? null,
                'documento_identidad' => $userData['documento_identidad'] ?? null,
                'tipo_documento' => $userData['tipo_documento'] ?? 'CC',
                'fecha_nacimiento' => $userData['fecha_nacimiento'] ?? null,
                'pais' => $userData['pais'] ?? 'Colombia',
                'ciudad' => $userData['ciudad'] ?? null,
                'direccion' => $userData['direccion'] ?? null,
                'foto_perfil' => $userData['foto_perfil'] ?? null,
                'activo' => true,
                'kyc_status' => 'pendiente',
                'creado_por_admin' => true,
                'admin_creador_id' => $admin->id,
            ]);

            // Asignar rol de Agricultor
            $farmer->assignRole('Agricultor');

            // Crear perfil vacío
            PerfilAgricultor::create([
                'user_id' => $farmer->id,
                'tipo_persona' => 'natural',
            ]);

            DB::commit();

            // Enviar email de bienvenida con credenciales
            $this->sendWelcomeEmail($farmer, $temporaryPassword);

            Log::info("Agricultor básico creado por admin", [
                'farmer_id' => $farmer->id,
                'admin_id' => $admin->id
            ]);

            return $farmer;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar perfil del agricultor (Fase 2)
     *
     * @param User $farmer
     * @param array $perfilData
     * @param array $familiaData
     * @return PerfilAgricultor
     */
    public function updateFarmerProfile(User $farmer, array $perfilData, array $familiaData = []): PerfilAgricultor
    {
        try {
            DB::beginTransaction();

            // Actualizar o crear perfil
            $perfil = PerfilAgricultor::updateOrCreate(
                ['user_id' => $farmer->id],
                $perfilData
            );

            // Actualizar familiares
            if (!empty($familiaData)) {
                // Eliminar familiares anteriores y crear nuevos
                FamiliaAgricultor::where('agricultor_id', $farmer->id)->delete();
                $this->createFamiliaAgricultor($farmer, $familiaData);
            }

            DB::commit();

            return $perfil;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generar contraseña temporal
     * Por defecto usa el documento de identidad
     *
     * @param array $userData
     * @return string
     */
    public function generateTemporaryPassword(array $userData): string
    {
        // Si tiene documento de identidad, usarlo como contraseña
        if (!empty($userData['documento_identidad'])) {
            return $userData['documento_identidad'];
        }

        // Si no, generar una contraseña aleatoria
        return Str::random(10);
    }

    /**
     * Enviar email de bienvenida con credenciales
     *
     * @param User $farmer
     * @param string $temporaryPassword
     * @return void
     */
    public function sendWelcomeEmail(User $farmer, string $temporaryPassword): void
    {
        try {
            $farmer->notify(new FarmerWelcomeNotification($farmer, $temporaryPassword));

            Log::info("Email de bienvenida enviado al agricultor", [
                'farmer_id' => $farmer->id,
                'email' => $farmer->email
            ]);
        } catch (\Exception $e) {
            // No fallar si el email no se puede enviar
            Log::error("Error enviando email de bienvenida: " . $e->getMessage());
        }
    }

    /**
     * Crear perfil de agricultor
     *
     * @param User $farmer
     * @param array $data
     * @return PerfilAgricultor
     */
    private function createPerfilAgricultor(User $farmer, array $data): PerfilAgricultor
    {
        return PerfilAgricultor::create([
            'user_id' => $farmer->id,
            'tipo_persona' => $data['tipo_persona'] ?? 'natural',
            'nombre_empresa' => $data['nombre_empresa'] ?? null,
            'nit' => $data['nit'] ?? null,
            'representante_legal' => $data['representante_legal'] ?? null,
            'direccion_finca' => $data['direccion_finca'] ?? null,
            'cultivo_asegurado' => $data['cultivo_asegurado'] ?? false,
            'anos_experiencia' => $data['anos_experiencia'] ?? null,
            'formacion_capacitaciones' => $data['formacion_capacitaciones'] ?? null,
            'cantidad_cosechas' => $data['cantidad_cosechas'] ?? null,
            'produccion_promedio' => $data['produccion_promedio'] ?? null,
            'num_personas_trabajando' => $data['num_personas_trabajando'] ?? null,
            'familia_trabaja_cultivo' => $data['familia_trabaja_cultivo'] ?? false,
            'roles_principales' => $data['roles_principales'] ?? null,
            'nivel_tecnificacion' => $data['nivel_tecnificacion'] ?? null,
            'tiene_riego' => $data['tiene_riego'] ?? false,
            'tiene_bodega' => $data['tiene_bodega'] ?? false,
            'tiene_transformacion' => $data['tiene_transformacion'] ?? false,
            'tiene_transporte' => $data['tiene_transporte'] ?? false,
            'accesibilidad' => $data['accesibilidad'] ?? null,
            'riesgos_naturales' => $data['riesgos_naturales'] ?? null,
        ]);
    }

    /**
     * Crear familiares del agricultor
     *
     * @param User $farmer
     * @param array $familiaData
     * @return void
     */
    private function createFamiliaAgricultor(User $farmer, array $familiaData): void
    {
        foreach ($familiaData as $familiar) {
            if (empty($familiar['nombre'])) continue;

            FamiliaAgricultor::create([
                'agricultor_id' => $farmer->id,
                'parentesco' => $familiar['parentesco'] ?? 'otro',
                'nombre' => $familiar['nombre'],
                'edad' => $familiar['edad'] ?? null,
                'nivel_educativo' => $familiar['nivel_educativo'] ?? null,
                'estudia_actualmente' => $familiar['estudia_actualmente'] ?? null,
                'trabaja_en_cultivo' => $familiar['trabaja_en_cultivo'] ?? false,
            ]);
        }
    }

    /**
     * Buscar agricultor existente por email o documento
     *
     * @param string|null $email
     * @param string|null $documento
     * @return User|null
     */
    public function findExistingFarmer(?string $email = null, ?string $documento = null): ?User
    {
        $query = User::role('Agricultor');

        if ($email) {
            $user = (clone $query)->where('email', $email)->first();
            if ($user) return $user;
        }

        if ($documento) {
            $user = (clone $query)->where('documento_identidad', $documento)->first();
            if ($user) return $user;
        }

        return null;
    }

    /**
     * Verificar si un agricultor puede ser usado para un nuevo proyecto
     *
     * @param User $farmer
     * @return bool
     */
    public function canCreateProject(User $farmer): bool
    {
        return $farmer->activo && $farmer->hasRole('Agricultor');
    }
}
