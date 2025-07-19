<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Champs distributeur ajoutés
        'pnom_distributeur',
        'nom_distributeur',
        'tel_distributeur',
        'adress_distributeur',
        'distributeur_id',
        'etoiles_id',
        'rang',
        'id_distrib_parent',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
            'distributeur_id' => 'integer',
            'etoiles_id' => 'integer',
            'rang' => 'integer',
            'id_distrib_parent' => 'integer',
            'role_id' => 'integer',
        ];
    }

    // --- Relations ---

    /**
     * Relation: Un User peut avoir un parent distributeur.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_distrib_parent', 'id');
    }

    /**
     * Relation: Un User peut avoir plusieurs enfants distributeurs.
     */
    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'id_distrib_parent', 'id');
    }

    // --- Méthodes utilitaires ---

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Vérifie si l'utilisateur est un distributeur.
     */
    public function isDistributeur(): bool
    {
        return !is_null($this->distributeur_id);
    }

    /**
     * Obtient le nom complet du distributeur.
     */
    public function getFullDistributeurNameAttribute(): string
    {
        if ($this->pnom_distributeur && $this->nom_distributeur) {
            return trim("{$this->pnom_distributeur} {$this->nom_distributeur}");
        }
        return $this->name ?? '';
    }

    /**
     * Obtient le matricule formaté.
     */
    public function getFormattedMatriculeAttribute(): string
    {
        return $this->distributeur_id ? "#{$this->distributeur_id}" : '';
    }
}
