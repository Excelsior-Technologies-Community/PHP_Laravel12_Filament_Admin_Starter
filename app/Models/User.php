<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Automatically assign the "user" role
     * when a new user is registered.
     */
    protected static function booted(): void
    {
        static::created(function (User $user): void {
            $userRole = Role::where([
                'name' => 'user',
                'guard_name' => 'web',
            ])->first();

            if (
                $userRole &&
                ! $user->hasAnyRole([
                    'super_admin',
                    'admin',
                    'user',
                ])
            ) {
                $user->assignRole($userRole);
            }
        });
    }

    /**
     * Filament Admin Panel Access Control.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([
            'super_admin',
            'admin',
        ]) && $this->is_active;
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class);
    }
}
