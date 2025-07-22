<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPermissions;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasRole(string $role)
 * @method bool canAccessAdmin()
 * @method void updateLastLogin()
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Rôles disponibles
     */
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * Obtenir tous les rôles disponibles
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_USER => 'Utilisateur',
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_SUPER_ADMIN => 'Super Administrateur',
        ];
    }

    /**
     * Obtenir le nom du rôle formaté
     */
    public function getRoleNameAttribute(): string
    {
        $roles = self::getAvailableRoles();
        return $roles[$this->role] ?? 'Inconnu';
    }

    /**
     * Scopes pour filtrer par rôle
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Méthodes de vérification de rôle
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminOrAbove(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Relations avec les demandes de suppression
     */
    public function deletionRequestsCreated()
    {
        return $this->hasMany(DeletionRequest::class, 'requested_by_id');
    }

    public function deletionRequestsApproved()
    {
        return $this->hasMany(DeletionRequest::class, 'approved_by_id');
    }
}
