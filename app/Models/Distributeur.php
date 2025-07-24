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
    'distributeur_id',
    'nom_distributeur',
    'pnom_distributeur',
    'tel_distributeur',
    'adress_distributeur',
    'id_distrib_parent',
    'etoiles_id',
    'rang',
    'statut_validation_periode', // Ajoutez cette ligne
    'is_indivual_cumul_checked',
];

protected $casts = [
    'etoiles_id' => 'integer',
    'rang' => 'integer',
    'id_distrib_parent' => 'integer',
    'statut_validation_periode' => 'boolean', // Ajoutez cette ligne
    'is_indivual_cumul_checked' => 'boolean',
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

    // À ajouter dans app/Models/Distributeur.php dans la section des relations

    /**
     * Relation: Un Distributeur a plusieurs enregistrements d'historique d'avancement.
     */
    public function avancementHistory(): HasMany
    {
        return $this->hasMany(AvancementHistory::class, 'distributeur_id', 'id');
    }

    /**
     * Récupère les avancements pour une période spécifique
     */
    public function getAvancementsForPeriod(string $period)
    {
        return $this->avancementHistory()->forPeriod($period);
    }

    /**
     * Récupère le dernier avancement
     */
    public function getLastAdvancement()
    {
        return $this->avancementHistory()->latest('date_avancement')->first();
    }
}
