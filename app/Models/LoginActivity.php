<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'city',
        'country',
        'user_agent',
        'status',
        'login_at',
    ];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get simple browser name from User Agent
     */
    public function getBrowserAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (str_contains($agent, 'Edg')) return 'Edge';
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Safari')) return 'Safari';
        if (str_contains($agent, 'Opera') || str_contains($agent, 'OPR')) return 'Opera';
        return 'Browser';
    }

    /**
     * Get platform/OS from User Agent
     */
    public function getPlatformAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (str_contains($agent, 'Windows')) return 'Windows';
        if (str_contains($agent, 'Macintosh') || str_contains($agent, 'Mac OS')) return 'macOS';
        if (str_contains($agent, 'Android')) return 'Android';
        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) return 'iOS';
        if (str_contains($agent, 'Linux')) return 'Linux';
        return 'Device';
    }
}