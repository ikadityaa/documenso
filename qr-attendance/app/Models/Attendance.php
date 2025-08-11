<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'checked_in_at',
        'checked_out_at',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TutoringSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}