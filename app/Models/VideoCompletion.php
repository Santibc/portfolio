<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoCompletion extends Model
{
    use HasFactory;

    /**
     * No usar timestamps automáticos de Laravel
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'video_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Relación: Una completación pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una completación pertenece a un video
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope: Completaciones de un usuario específico
     */
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope: Completaciones de un video específico
     */
    public function scopeByVideo($query, Video $video)
    {
        return $query->where('video_id', $video->id);
    }

    /**
     * Scope: Completaciones recientes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('completed_at', 'desc');
    }
}
