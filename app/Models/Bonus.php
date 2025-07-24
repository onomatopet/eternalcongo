<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Représente un enregistrement de bonus pour un distributeur pour une période.
 *
 * @property int $id
 * @property string $period
 * @property string $num Numéro/Identifiant du bonus/formulaire
 * @property int $distributeur_id (Devrait maintenant être l'ID primaire)
 * @property float|null $bonus_direct (Casté decimal)
 * @property float|null $bonus_indirect (Casté decimal)
 * @property float|null $bonus_leadership (Casté decimal)
 * @property float $bonus (Total, casté decimal)
 * @property float $epargne (Casté decimal)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Distributeur $distributeur
 */
class Bonus extends Model
{
    use HasFactory;

    protected $table = 'bonuses';
    public $timestamps = true;

    protected $fillable = [
        'period',
        'num',
        'distributeur_id',
        'bonus_direct',
        'bonus_indirect',
        'bonus_leadership',
        'montant', // Changé de 'bonus' à 'montant'
        'epargne',
    ];

    protected $casts = [
        'distributeur_id' => 'integer',
        'bonus_direct' => 'decimal:2',
        'bonus_indirect' => 'decimal:2',
        'bonus_leadership' => 'decimal:2',
        'montant' => 'decimal:2', // Changé de 'bonus' à 'montant'
        'epargne' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Si vous avez des accesseurs/mutateurs, mettez-les à jour aussi
    public function getMontantFormattedAttribute()
    {
        return number_format($this->montant, 2, ',', ' ') . ' FCFA';
    }

    // Si vous aviez un accessor pour 'bonus', renommez-le
    public function getBonusTotalAttribute()
    {
        return $this->bonus_direct + $this->bonus_indirect + $this->bonus_leadership;
    }

    /**
     * Relation: Cet enregistrement de Bonus appartient à un Distributeur.
     */
    public function distributeur(): BelongsTo
    {
        return $this->belongsTo(Distributeur::class, 'distributeur_id', 'id');
    }
}
