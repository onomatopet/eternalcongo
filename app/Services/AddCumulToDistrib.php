<?php

// Supposons que ce code est dans une méthode d'un Service, Contrôleur, ou Commande.
// Assurez-vous d'avoir les 'use' statements nécessaires en haut du fichier:
namespace App\Services;
use App\Models\Achat;
use App\Models\Level_current_test;
use App\Models\Distributeur; // Si utilisé pour récupérer rang/etoiles par défaut
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AddCumulToDistrib
{
    public function processAchatsAndLevels(string $period) // La période est passée en argument
    {
        Log::info("Début du traitement des achats et mise à jour des niveaux pour la période: {$period}");

        // --- 1. Récupérer tous les achats agrégés pour la période ---
        // selectRaw est bon. Le groupBy('distributeur_id') est incorrect si on veut aussi id_distrib_parent.
        // Si id_distrib_parent est le même pour tous les achats d'un distributeur pour cette période, c'est ok.
        // Sinon, il faut le retirer du selectRaw/groupBy ou utiliser MAX()/MIN() si on en a besoin pour la création.
        // Ici, je vais supposer qu'on peut récupérer id_distrib_parent via le modèle Distributeur si besoin pour la création.
        $achatsAgreges = Achat::selectRaw('distributeur_id, SUM(pointvaleur) as total_new_achats')
            ->where('period', $period)
            ->groupBy('distributeur_id')
            ->havingRaw('SUM(pointvaleur) > 0') // Optionnel: Ne traiter que s'il y a des achats
            ->get()
            ->keyBy('distributeur_id'); // Clé par distributeur_id pour accès facile

        if ($achatsAgreges->isEmpty()) {
            Log::info("Aucun achat trouvé pour la période {$period}. Traitement terminé.");
            return "Aucun achat à traiter pour la période {$period}.";
        }
        Log::info("Achats agrégés récupérés pour {$achatsAgreges->count()} distributeurs.");

        // --- 2. Récupérer tous les enregistrements Level_current_test existants pour la période ---
        $existingLevels = Level_current_test::where('period', $period)
            ->whereIn('distributeur_id', $achatsAgreges->keys()) // Ne charger que les distributeurs ayant des achats
            ->get()
            ->keyBy('distributeur_id'); // Clé par distributeur_id

        Log::info("Niveaux existants récupérés pour {$existingLevels->count()} distributeurs pour la période {$period}.");

        $updates = []; // Pour les mises à jour groupées
        $inserts = []; // Pour les insertions groupées
        $distributeursACreerIds = []; // IDs des distributeurs pour lesquels il faut créer un Level_current_test

        // --- 3. Préparer les mises à jour et les insertions ---
        foreach ($achatsAgreges as $distribId => $achat) {
            $nouveauxAchats = (float) $achat->total_new_achats;

            if ($existingLevel = $existingLevels->get($distribId)) {
                // CAS 1: Le Level_current_test existe -> MISE À JOUR
                // Utiliser DB::raw pour les incrémentations atomiques
                // Préparer un tableau pour un 'upsert' ou des 'update' individuels optimisés
                $updates[] = [
                    'distributeur_id' => $distribId, // Utilisé pour le where de l'update
                    'period' => $period,            // Utilisé pour le where de l'update
                    'cumul_individuel_increment' => $nouveauxAchats,
                    'new_cumul_assign' => $nouveauxAchats,
                    'cumul_total_increment' => $nouveauxAchats,
                    'cumul_collectif_increment' => $nouveauxAchats, // Vérifiez cette logique
                ];
            } else {
                // CAS 2: Le Level_current_test N'EXISTE PAS -> INSERTION
                // Marquer pour récupération des infos du distributeur principal
                $distributeursACreerIds[] = $distribId;
                // Stocker les achats pour l'insertion plus tard
                // $inserts attendra les infos de la table Distributeur
            }
        }

        // --- 4. Récupérer les informations des distributeurs pour les nouvelles créations ---
        $distributeursPourCreation = collect();
        if (!empty($distributeursACreerIds)) {
            $distributeursPourCreation = Distributeur::whereIn('distributeur_id', $distributeursACreerIds)
                                                     ->select('distributeur_id', 'id_distrib_parent', 'rang') // Ajouter 'etoiles' si nécessaire
                                                     ->get()
                                                     ->keyBy('distributeur_id');
            Log::info("Informations récupérées pour {$distributeursPourCreation->count()} nouveaux distributeurs à insérer dans Level_current_test.");
        }

        // --- 5. Finaliser les données pour l'insertion ---
        foreach ($distributeursACreerIds as $distribId) {
            $achatInfo = $achatsAgreges->get($distribId);
            $distribInfo = $distributeursPourCreation->get($distribId);

            if ($achatInfo && $distribInfo) {
                $nouveauxAchats = (float) $achatInfo->total_new_achats;
                $inserts[] = [
                    'distributeur_id' => $distribId,
                    'period' => $period,
                    'rang' => $distribInfo->rang ?? null, // Rang actuel du distributeur
                    'etoiles' => 1, // Grade initial par défaut pour une nouvelle période
                    'cumul_individuel' => $nouveauxAchats,
                    'new_cumul' => $nouveauxAchats,
                    'cumul_total' => $nouveauxAchats,
                    'cumul_collectif' => $nouveauxAchats, // Initialisé avec les achats du distributeur lui-même
                    'id_distrib_parent' => $distribInfo->id_distrib_parent ?? null,
                    'created_at' => Carbon::now(), // Utiliser Carbon::now() ou une date de période
                    'updated_at' => Carbon::now(), // Si vos colonnes existent
                ];
            } else {
                Log::warning("Impossible de créer l'enregistrement Level_current_test pour distributeur_id {$distribId}: informations d'achat ou de distributeur manquantes.");
            }
        }

        // --- 6. Exécuter les opérations sur la base de données ---
        $updatedCount = 0;
        $insertedCount = 0;

        try {
            DB::beginTransaction();

            // Exécuter les mises à jour (plusieurs requêtes individuelles mais atomiques)
            if (!empty($updates)) {
                foreach ($updates as $updateData) {
                    // Utiliser updateOrInsert est une option, mais pour des incréments,
                    // il est souvent plus clair de faire un select puis update/insert séparément
                    // ou de gérer les updates comme ci-dessous.
                    $affectedRows = Level_current_test::where('distributeur_id', $updateData['distributeur_id'])
                        ->where('period', $updateData['period'])
                        ->update([
                            'cumul_individuel' => DB::raw("cumul_individuel + " . $updateData['cumul_individuel_increment']),
                            'new_cumul' => $updateData['new_cumul_assign'], // Assignation directe
                            'cumul_total' => DB::raw("cumul_total + " . $updateData['cumul_total_increment']),
                            'cumul_collectif' => DB::raw("cumul_collectif + " . $updateData['cumul_collectif_increment']),
                            'updated_at' => Carbon::now(),
                        ]);
                    if ($affectedRows > 0) {
                        $updatedCount++;
                    }
                }
                Log::info("Mises à jour effectuées pour {$updatedCount} enregistrements Level_current_test.");
            }

            // Exécuter les insertions groupées
            if (!empty($inserts)) {
                // Level_current_test::insert($inserts); // Ne remplit pas created_at/updated_at automatiquement
                // Préférer une boucle create si on veut les timestamps Eloquent et les événements,
                // mais pour du bulk, 'insert' est plus rapide. Si created_at/updated_at sont définis manuellement, 'insert' est ok.
                foreach (array_chunk($inserts, 500) as $chunk) { // Insérer par lots
                    Level_current_test::insert($chunk);
                    $insertedCount += count($chunk);
                }
                Log::info("Insertions effectuées pour {$insertedCount} nouveaux enregistrements Level_current_test.");
            }

            DB::commit();
            $message = "Traitement terminé pour la période {$period}. Mises à jour: {$updatedCount}, Insertions: {$insertedCount}.";
            Log::info($message);
            return $message;

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = "Erreur lors du traitement pour la période {$period}: " . $e->getMessage();
            Log::error($errorMessage, ['exception' => $e]);
            // Ne pas utiliser dd() dans un script de production ou une commande
            // throw $e; // Relancer l'exception si on veut que la commande échoue
            return "ERREUR: " . $errorMessage;
        }
    }
}
