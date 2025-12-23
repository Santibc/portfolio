<?php

namespace App\Services\Kyc;

use App\Models\User;
use App\Models\DocumentoKyc;
use Illuminate\Support\Facades\Storage;

class KycService
{
    /**
     * Determina si el usuario puede invertir según su estado KYC
     */
    public function canInvest(User $user): bool
    {
        return in_array($user->kyc_status, ['en_revision', 'aprobado']);
    }

    /**
     * Determina si el usuario debe subir documentos KYC
     */
    public function needsToUploadDocuments(User $user): bool
    {
        return in_array($user->kyc_status, ['pendiente', 'rechazado']);
    }

    /**
     * Guardar documentos KYC y cambiar estado a 'en_revision'
     */
    public function submitKyc(User $user, array $documentos): void
    {
        // Mapeo de nombres de formulario a enum de BD
        $mapeoTipos = [
            'documento_frente' => 'cedula_frontal',
            'documento_reverso' => 'cedula_trasera',
            'selfie' => 'selfie',
            'comprobante_domicilio' => 'prueba_domicilio',
        ];

        // Crear directorio si no existe
        $uploadDir = public_path("uploads/kyc/{$user->id}");
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Guardar cada documento
        foreach ($documentos as $tipo => $archivo) {
            // Obtener datos antes de mover el archivo
            $originalName = $archivo->getClientOriginalName();
            $mimeType = $archivo->getClientMimeType();
            $sizeKb = (int) ceil($archivo->getSize() / 1024);
            $extension = $archivo->getClientOriginalExtension();

            $filename = time() . '_' . $tipo . '.' . $extension;
            $archivo->move($uploadDir, $filename);
            $path = "uploads/kyc/{$user->id}/{$filename}";

            DocumentoKyc::create([
                'usuario_id' => $user->id,
                'tipo_documento' => $mapeoTipos[$tipo] ?? $tipo,
                'nombre_archivo' => $originalName,
                'ruta_archivo' => $path,
                'mime_type' => $mimeType,
                'tamanio_kb' => $sizeKb,
                'fecha_subida' => now(),
                'estado' => 'pendiente_revision',
            ]);
        }

        // Cambiar estado del usuario a 'en_revision' (YA puede invertir)
        $user->update([
            'kyc_status' => 'en_revision'
        ]);
    }

    /**
     * Aprobar KYC de un usuario
     */
    public function approveKyc(User $user, User $admin): void
    {
        $user->update([
            'kyc_status' => 'aprobado',
            'kyc_aprobado_por' => $admin->id,
            'kyc_aprobado_at' => now(),
        ]);

        // Actualizar documentos a aprobados
        $user->documentosKyc()->update([
            'estado' => 'aprobado',
            'revisado_por' => $admin->id,
            'revisado_at' => now(),
        ]);

        // TODO: Notificar al usuario
    }

    /**
     * Rechazar KYC de un usuario (BLOQUEA inversiones)
     */
    public function rejectKyc(User $user, User $admin, string $motivo): void
    {
        $user->update([
            'kyc_status' => 'rechazado',
            'kyc_notas' => $motivo,
        ]);

        // Actualizar documentos a rechazados
        $user->documentosKyc()->update([
            'estado' => 'rechazado',
            'revisado_por' => $admin->id,
            'revisado_at' => now(),
            'observaciones' => $motivo,
        ]);

        // TODO: Notificar al usuario que debe subir nuevos documentos
    }

    /**
     * Obtener usuarios con KYC en revisión (para admin)
     */
    public function getUsersPendingReview()
    {
        return User::where('kyc_status', 'en_revision')
            ->with('documentosKyc')
            ->latest('updated_at')
            ->paginate(20);
    }
}
