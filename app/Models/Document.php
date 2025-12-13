<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->order)) {
                $document->order = static::where('course_id', $document->course_id)->max('order') + 1;
            }
        });

        // Eliminar archivo cuando se elimina el registro
        static::deleting(function ($document) {
            if ($document->file_path && file_exists(public_path($document->file_path))) {
                unlink(public_path($document->file_path));
            }
        });
    }

    /**
     * Relación: Un documento pertenece a un curso
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Obtener la URL del documento
     */
    public function getFileUrlAttribute(): string
    {
        return asset($this->file_path);
    }

    /**
     * Obtener tamaño formateado (KB, MB, etc)
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Obtener icono según tipo de archivo
     */
    public function getFileIconAttribute(): string
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'bi-file-earmark-pdf',
            'doc', 'docx' => 'bi-file-earmark-word',
            'xls', 'xlsx' => 'bi-file-earmark-excel',
            'ppt', 'pptx' => 'bi-file-earmark-ppt',
            default => 'bi-file-earmark',
        };
    }

    /**
     * Obtener color del icono según tipo de archivo
     */
    public function getFileIconColorAttribute(): string
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'text-danger',
            'doc', 'docx' => 'text-primary',
            'xls', 'xlsx' => 'text-success',
            'ppt', 'pptx' => 'text-warning',
            default => 'text-secondary',
        };
    }

    /**
     * Obtener nombre del tipo de archivo
     */
    public function getFileTypeNameAttribute(): string
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'PDF',
            'doc', 'docx' => 'Word',
            'xls', 'xlsx' => 'Excel',
            'ppt', 'pptx' => 'PowerPoint',
            default => strtoupper($this->file_type),
        };
    }

    /**
     * Scope: Ordenar por campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
