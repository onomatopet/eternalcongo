<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Si les distributeurs peuvent se connecter :
// use Illuminate\Foundation\Auth\User as Authenticatable;
// class Distributeur extends Authenticatable

class Distributeur extends Model // Ou extends Authenticatable
{
    use HasFactory; // Ajoutez Notifiable si besoin: use HasFactory, Notifiable;

    protected $table = 'distributeurs';
    public $timestamps = true;

    protected $fillable = [
        'etoiles_id',
        'rang',
        'distributeur_id', // Matricule
        'nom_distributeur',
        'pnom_distributeur',
        'tel_distributeur',
        'adress_distributeur',
        'id_distrib_parent',
        'statut_validation_periode', // Nom de la colonne après modification
    ];

    protected $casts = [
        'id' => 'integer',
        'etoiles_id' => 'integer', // Ou smallInteger si changé
        'rang' => 'integer',
        'distributeur_id' => 'string', // Matricule peut contenir lettres/chiffres? Mettre string est plus sûr. Si c'est TOUJOURS un nombre, garder 'integer'.
        'id_distrib_parent' => 'integer',
        'statut_validation_periode' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Si les distributeurs se connectent, ajouter :
    // protected $hidden = ['password', 'remember_token'];

    // --- Relations ---

    /**
     * Relation: Un Distributeur a un parent Distributeur (peut être null).
     */
    public function parent(): BelongsTo
    {
        // Référence la clé 'id_distrib_parent' de cette table vers la clé 'id' de cette même table
        return $this->belongsTo(Distributeur::class, 'id_distrib_parent', 'id');
    }

    /**
     * Relation: Un Distributeur a plusieurs enfants Distributeurs directs.
     */
    public function children(): HasMany
    {
        // Référence la clé 'id' de cette table vers la clé 'id_distrib_parent' de cette même table
        return $this->hasMany(Distributeur::class, 'id_distrib_parent', 'id');
    }

    /**
     * Relation: Un Distributeur effectue plusieurs Achats.
     */
    public function achats(): HasMany
    {
        return $this->hasMany(Achat::class, 'distributeur_id', 'id');
    }

    /**
     * Relation: Un Distributeur a plusieurs entrées de niveau actuelles (normalement une seule par période).
     */
    public function levelCurrents(): HasMany
    {
        // Assurez-vous que le modèle LevelCurrent existe
        return $this->hasMany(LevelCurrent::class, 'distributeur_id', 'id');
    }

    /**
     * Relation: Un Distributeur a plusieurs entrées d'historique de niveau.
     */
    public function levelHistories(): HasMany
    {
         // Assurez-vous que le modèle LevelCurrentHistory existe
        return $this->hasMany(LevelCurrentHistory::class, 'distributeur_id', 'id');
    }

    /**
     * Relation: Un Distributeur peut avoir plusieurs enregistrements de Bonus.
     */
    public function bonuses(): HasMany
    {
         // Assurez-vous que le modèle Bonus existe
        return $this->hasMany(Bonus::class, 'distributeur_id', 'id');
    }

    // --- Accesseurs (Optionnel) ---
    /**
     * Obtient le nom complet du distributeur.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->pnom_distributeur} {$this->nom_distributeur}");
    }
}
