<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'destinatario_email',
        'destinatarios',
        'destinatario_id',
        'asunto',
        'emailable_type',
        'emailable_id',
        'estado',
        'error_message',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
        'destinatarios' => 'array',
    ];

    /**
     * Tipos de email disponibles
     */
    const TIPO_FACTURA = 'factura';
    const TIPO_ALERTA = 'alerta';
    const TIPO_DOCUMENTO = 'documento';
    const TIPO_FICHAJE = 'fichaje';
    const TIPO_BIENVENIDA = 'bienvenida';

    /**
     * Estados del email
     */
    const ESTADO_ENVIADO = 'enviado';
    const ESTADO_FALLIDO = 'fallido';
    const ESTADO_PENDIENTE = 'pendiente';

    /**
     * Usuario destinatario
     */
    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    /**
     * Modelo relacionado (factura, alerta, documento, etc.)
     */
    public function emailable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para emails enviados
     */
    public function scopeEnviados($query)
    {
        return $query->where('estado', self::ESTADO_ENVIADO);
    }

    /**
     * Scope para emails fallidos
     */
    public function scopeFallidos($query)
    {
        return $query->where('estado', self::ESTADO_FALLIDO);
    }

    /**
     * Crear log de email enviado exitosamente
     * @param array|string $emails Array of emails or single email string
     */
    public static function logEnviado(
        string $tipo,
        $emails,
        string $asunto,
        ?Model $emailable = null,
        ?int $userId = null
    ): self {
        // Normalize to array
        $emailArray = is_array($emails) ? $emails : [$emails];

        return self::create([
            'tipo' => $tipo,
            'destinatario_email' => $emailArray[0], // First email for backward compatibility
            'destinatarios' => $emailArray, // All emails in JSON
            'destinatario_id' => $userId,
            'asunto' => $asunto,
            'emailable_type' => $emailable ? get_class($emailable) : null,
            'emailable_id' => $emailable?->id,
            'estado' => self::ESTADO_ENVIADO,
            'enviado_at' => now(),
        ]);
    }

    /**
     * Crear log de email fallido
     * @param array|string $emails Array of emails or single email string
     */
    public static function logFallido(
        string $tipo,
        $emails,
        string $asunto,
        string $errorMessage,
        ?Model $emailable = null,
        ?int $userId = null
    ): self {
        // Normalize to array
        $emailArray = is_array($emails) ? $emails : [$emails];

        return self::create([
            'tipo' => $tipo,
            'destinatario_email' => $emailArray[0], // First email for backward compatibility
            'destinatarios' => $emailArray, // All emails in JSON
            'destinatario_id' => $userId,
            'asunto' => $asunto,
            'emailable_type' => $emailable ? get_class($emailable) : null,
            'emailable_id' => $emailable?->id,
            'estado' => self::ESTADO_FALLIDO,
            'error_message' => $errorMessage,
        ]);
    }
}
