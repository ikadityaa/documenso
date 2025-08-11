<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutoringSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'tutor_id',
        'location',
        'scheduled_at',
        'duration_minutes',
        'active_qr_token_hash',
        'active_qr_expires_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'active_qr_expires_at' => 'datetime',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function getEndsAtAttribute(): Carbon
    {
        return $this->scheduled_at->clone()->addMinutes((int) $this->duration_minutes);
    }

    public function isQrActive(): bool
    {
        return $this->active_qr_token_hash !== null && $this->active_qr_expires_at !== null && now()->lt($this->active_qr_expires_at);
    }
}