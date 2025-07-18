<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Le modèle User de Laravel/Jetstream/Breeze
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportDistributorsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-distributors {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from "distributeurs_old" table to the "users" table and fixes parent links.';

    /**
     * Map [ancien_matricule => nouvel_user_id]
     * @var array
     */
    private array $matriculeToNewIdMap = [];


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn("--- Starting Distributor Data Import to Users Table ---");
        $this->line("Reading from 'distributeurs_old' table on connection 'db_first'.");
        $this->line("Writing to 'users' table on default connection.");

        // Vérifier si la table source existe sur la connexion 'db_first'
        if (!Schema::connection('db_first')->hasTable('distributeurs_old')) {
            $this->error("Source table 'distributeurs_old' not found on 'db_first' connection. Please rename your old distributor table first.");
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("This will CREATE/UPDATE users based on the 'db_first' connection. Ensure the 'users' table is empty for a clean import. Proceed?")) {
            $this->comment("Operation cancelled.");
            return self::FAILURE;
        }

        // Démarrer une transaction globale sur la base de destination
        DB::connection('mysql')->beginTransaction();

        try {
            // --- ÉTAPE 1: Créer TOUS les utilisateurs et remplir la map ---
            $this->line("\nStep 1: Creating/updating users and building the ID map...");

            // Lire TOUS les distributeurs de l'ancienne base
            $oldDistributors = DB::connection('db_first')->table('distributeurs_old')->orderBy('id')->get();
            $progressBar = $this->output->createProgressBar($oldDistributors->count());

            foreach ($oldDistributors as $oldDistributor) {
                // Créer/mettre à jour l'utilisateur sur la connexion par défaut (mysql)
                $user = User::on('mysql')->updateOrCreate(
                    ['distributeur_id' => $oldDistributor->distributeur_id], // Clé unique pour trouver/créer
                    [
                        'name' => trim($oldDistributor->pnom_distributeur . ' ' . $oldDistributor->nom_distributeur),
                        'pnom_distributeur' => $oldDistributor->pnom_distributeur,
                        'nom_distributeur' => $oldDistributor->nom_distributeur,
                        'distributeur_id' => $oldDistributor->distributeur_id, // Répéter la clé pour l'insertion/mise à jour
                        'email' => "user_{$oldDistributor->distributeur_id}@example.com", // Email générique unique
                        'password' => Hash::make(Str::random(10)), // Mot de passe aléatoire
                        'email_verified_at' => now(), // On les considère vérifiés
                        'etoiles_id' => $oldDistributor->etoiles_id,
                        'rang' => $oldDistributor->rang,
                        'tel_distributeur' => $oldDistributor->tel_distributeur,
                        'adress_distributeur' => $oldDistributor->adress_distributeur,
                    ]
                );

                // La logique de création d'équipe a été supprimée ici car la table 'teams' n'existe pas.

                // Remplir la map pour la deuxième passe
                $this->matriculeToNewIdMap[$oldDistributor->distributeur_id] = $user->id;
                $progressBar->advance();
            }
            $progressBar->finish();
            $this->info("\nStep 1: User creation/update complete.");


            // --- ÉTAPE 2: Mettre à jour les liens de parenté ---
            $this->line("\nStep 2: Updating parent relationships (id_distrib_parent)...");
            $progressBar2 = $this->output->createProgressBar($oldDistributors->count());

            foreach ($oldDistributors as $oldDistributor) {
                // Si l'ancien distributeur avait un parent (et que ce n'était pas 0 ou NULL)
                if (!empty($oldDistributor->id_distrib_parent) && $oldDistributor->id_distrib_parent != 0) {
                    $parentMatricule = $oldDistributor->id_distrib_parent;
                    $childMatricule = $oldDistributor->distributeur_id;

                    // La map est maintenant complète, les lookups devraient fonctionner
                    $newParentId = $this->matriculeToNewIdMap[$parentMatricule] ?? null;
                    $newChildId = $this->matriculeToNewIdMap[$childMatricule] ?? null;

                    if ($newParentId && $newChildId) {
                        // Mettre à jour l'enfant avec l'ID de son nouveau parent
                        User::on('mysql')->where('id', $newChildId)->update(['id_distrib_parent' => $newParentId]);
                    } else {
                        // Logguer un avertissement si le parent n'est pas trouvé (orphelin)
                        $this->warn("\nCould not create parent link for child matricule {$childMatricule} (parent matricule '{$parentMatricule}' not found in maps).");
                        Log::warning("Orphan during import: Child matricule {$childMatricule}, Parent matricule '{$parentMatricule}' not found.");
                    }
                }
                $progressBar2->advance();
            }
            $progressBar2->finish();
            $this->info("\nStep 2: Parent relationship update complete.");

            // Valider la transaction si toutes les étapes ont réussi
            DB::connection('mysql')->commit();
            $this->info("\n<fg=green>Import process finished successfully! All data committed.</>");

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::connection('mysql')->rollBack();
            $this->error("\nAn error occurred during import. All changes have been rolled back.");
            $this->error($e->getMessage());
            Log::critical("Distributor import failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
