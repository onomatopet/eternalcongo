<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CorrectForeignIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:correct-foreign-ids {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrects columns storing distributor matricules to store primary IDs instead, and copies achat data.';

    /**
     * Lookup map [matricule => primary_key_id].
     * @var Collection|null
     */
    protected ?Collection $matriculeToIdMap = null;

    /**
     * Stores details of orphan records found during processing.
     * Structure: ['table_name' => [['row_id' => id, 'column' => name, 'invalid_value' => value], ...]]
     * @var array
     */
    protected array $orphanDetails = [];

    /**
     * Configuration des tables et colonnes à corriger.
     * Format: 'table_name' => ['colonne_a_corriger_1', 'colonne_a_corriger_2', ...]
     * @var array
     */
    protected array $tablesToCorrect = [
        'distributeurs'             => ['id_distrib_parent'],
        'level_currents'            => ['distributeur_id', 'id_distrib_parent'],
        'level_current_histories'   => ['distributeur_id', 'id_distrib_parent'],
        'achats'                    => ['distributeur_id'],
        'bonuses'                   => ['distributeur_id'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn("!!! IMPORTANT !!!");
        $this->warn("This command will permanently modify foreign key data across multiple tables.");
        $this->warn("Ensure you have a reliable database backup BEFORE proceeding.");

        if (!$this->option('force') && !$this->confirm('Do you want to continue? [y/N]', false)) {
            $this->comment('Operation cancelled.');
            return self::FAILURE;
        }

        // --- 1. Build the Distributor Lookup Map ---
        $this->line("\n<fg=cyan>Step 1: Building distributor matricule-to-id map...</>");
        if (!$this->buildDistributorMap()) {
            return self::FAILURE; // Arrêter si la map ne peut pas être construite
        }
        $this->info('Distributor map built successfully (' . $this->matriculeToIdMap->count() . ' entries).');

        // --- 2. Process Each Table for ID Correction ---
        $this->line("\n<fg=cyan>Step 2: Correcting Matricule references to Primary IDs...</>");
        foreach ($this->tablesToCorrect as $tableName => $columns) {
            $this->processIdCorrection($tableName, $columns);
        }
        $this->info('Finished ID correction.');

        // --- 3. Copy Data in 'achats' table ---
        $this->line("\n<fg=cyan>Step 3: Copying data in 'achats' table...</>");
        $this->copyAchatData();
        $this->info('Finished copying achat data.');


        // --- 4. Final Report ---
        $this->line("\n------------------------------------------");
        $this->info('<fg=green>Foreign ID Correction Process Finished.</>');

        if (!empty($this->orphanDetails)) {
            $totalOrphans = 0;
            $this->warn("\n<fg=yellow>Orphan Records Found (Matricule not in 'distributeurs' map):</>");
            foreach($this->orphanDetails as $table => $orphans) {
                 $this->warn("  Table '{$table}':");
                 foreach($orphans as $orphan) {
                      $this->warn("    - Row ID: {$orphan['row_id']}, Column: {$orphan['column']}, Invalid Matricule: {$orphan['invalid_value']}");
                      $totalOrphans++;
                 }
            }
             $this->error("\n{$totalOrphans} ORPHAN RECORDS DETECTED!");
             $this->error("These rows were NOT updated and WILL CAUSE FOREIGN KEY CONSTRAINT ERRORS.");
             $this->error("You MUST correct or delete these records manually before adding foreign keys.");
             $this->line("------------------------------------------");
             return self::FAILURE; // Important: Indiquer l'échec
        } else {
            $this->info("\nNo orphan records found.");
            $this->info("<fg=green>Data correction seems successful. You should now be able to add foreign key constraints.</>");
             $this->line("------------------------------------------");
            return self::SUCCESS;
        }
    }

    /**
     * Build the Matricule -> ID map.
     */
    protected function buildDistributorMap(): bool
    {
        // Identique aux commandes précédentes
        try {
            $this->matriculeToIdMap = DB::table('distributeurs')
                ->pluck('id', 'distributeur_id'); // Clé = Matricule, Valeur = ID Primaire

            if ($this->matriculeToIdMap === null) {
                 $this->error('Failed to build map (pluck returned null).'); return false;
            }
             if ($this->matriculeToIdMap->has(null) || $this->matriculeToIdMap->has('')) {
                 $this->error('Found NULL or empty matricules (distributeur_id) in the distributeurs table. Cannot proceed.');
                 return false;
             }
              // Vérification Doublon Matricule (Simple)
             $duplicateCheck = DB::table('distributeurs')
                ->select('distributeur_id')
                ->groupBy('distributeur_id')
                ->havingRaw('COUNT(*) > 1')
                ->first();
             if ($duplicateCheck) {
                 $this->error("DUPLICATE matricule found: {$duplicateCheck->distributeur_id}. Cannot proceed with matricule-based correction.");
                 return false;
             }

            return true;
        } catch (\Exception $e) {
            $this->error('Failed to build distributor map: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process ID correction for a specific table and columns.
     */
    protected function processIdCorrection(string $tableName, array $columnsToCorrect): void
    {
        $this->info("  Processing table: <fg=yellow>{$tableName}</>");
        $totalRows = DB::table($tableName)->count();
        if ($totalRows == 0) {
            $this->line("    Table is empty. Skipping.");
            return;
        }

        $progressBar = $this->output->createProgressBar($totalRows);
        $progressBar->start();

        // Sélectionner l'ID de la ligne + toutes les colonnes à corriger
        $selectColumns = array_merge(['id'], $columnsToCorrect);

        DB::table($tableName)
            ->select($selectColumns)
            ->orderBy('id')
            ->chunkById(200, function (Collection $rows) use ($tableName, $columnsToCorrect, $progressBar) {
                $updates = []; // [rowId => ['col1' => newId1, 'col2' => newId2]]

                foreach ($rows as $row) {
                    $rowUpdates = []; // Updates for this specific row

                    foreach ($columnsToCorrect as $columnName) {
                        $currentValue = $row->$columnName; // Matricule ou 0 ou NULL

                        if ($currentValue === null) continue; // Déjà NULL, OK

                        if ($currentValue === 0 || $currentValue === '0') {
                             // Convertir 0 en NULL (la colonne doit être nullable)
                             $rowUpdates[$columnName] = null;
                             continue; // Passer à la colonne suivante pour cette ligne
                        }

                        // Rechercher le matricule dans la map
                        $correctPrimaryKeyId = $this->matriculeToIdMap->get($currentValue);

                        if ($correctPrimaryKeyId !== null) {
                            // Matricule trouvé, mettre à jour si différent
                            if ($currentValue != $correctPrimaryKeyId) {
                                $rowUpdates[$columnName] = $correctPrimaryKeyId;
                            }
                        } else {
                            // Matricule NON trouvé = ORPHELIN
                            // Vérifier si ce n'est pas déjà un ID primaire valide (peu probable ici)
                            if (!$this->matriculeToIdMap->contains($currentValue)){
                                 $this->orphanDetails[$tableName][] = [
                                     'row_id' => $row->id,
                                     'column' => $columnName,
                                     'invalid_value' => $currentValue
                                 ];
                                 Log::warning("Orphan detected: Table={$tableName}, RowID={$row->id}, Column={$columnName}, InvalidMatricule={$currentValue}");
                            }
                            // Ne pas ajouter aux $rowUpdates pour les orphelins
                        }
                    } // Fin foreach column

                    // Planifier la mise à jour pour cette ligne si des changements sont nécessaires
                    if (!empty($rowUpdates)) {
                        $updates[$row->id] = $rowUpdates;
                    }
                } // Fin foreach row in chunk

                // Appliquer les mises à jour pour ce chunk
                if (!empty($updates)) {
                    foreach ($updates as $rowId => $updateData) {
                         try {
                            DB::table($tableName)->where('id', $rowId)->update($updateData);
                        } catch (\Exception $e) {
                             Log::error("Failed to update RowID {$rowId} in {$tableName}: ".$e->getMessage()." | Data: ".json_encode($updateData));
                             // Marquer comme orphelin si l'update échoue ?
                             $this->orphanDetails[$tableName][] = ['row_id' => $rowId, 'column' => 'UPDATE_FAILED', 'invalid_value' => json_encode($updateData)];
                        }
                    }
                }
                $progressBar->advance($rows->count());
                unset($rows); // Libérer mémoire
                unset($updates); // Libérer mémoire

            }); // Fin chunkById

        $progressBar->finish();
        $this->info("\n    Finished processing {$tableName}.");
    }


    /**
     * Copy data between old and new columns in 'achats' table.
     */
    protected function copyAchatData(): void
    {
         $this->info("  Copying data within 'achats' table...");
         try {
             // Copier pointvaleur -> points_unitaire_achat
             if (Schema::hasColumn('achats', 'pointvaleur') && Schema::hasColumn('achats', 'points_unitaire_achat')) {
                  Log::info('    Copying pointvaleur -> points_unitaire_achat...');
                 DB::table('achats')->whereNotNull('pointvaleur')->orderBy('id')->chunk(500, function ($chunk) {
                     foreach ($chunk as $row) { DB::table('achats')->where('id', $row->id)->update(['points_unitaire_achat' => (float)$row->pointvaleur]); }
                 });
             } else { Log::warning('    Skipped copying pointvaleur data (column missing).');}

             // Copier montant -> montant_total_ligne
             if (Schema::hasColumn('achats', 'montant') && Schema::hasColumn('achats', 'montant_total_ligne')) {
                  Log::info('    Copying montant -> montant_total_ligne...');
                  DB::table('achats')->whereNotNull('montant')->orderBy('id')->chunk(500, function ($chunk) {
                      foreach ($chunk as $row) { DB::table('achats')->where('id', $row->id)->update(['montant_total_ligne' => (float)$row->montant]); }
                  });
             } else { Log::warning('    Skipped copying montant data (column missing).'); }

             // Remplir prix_unitaire_achat (si possible et souhaité)
             if (Schema::hasColumn('achats', 'prix_unitaire_achat')) {
                 Log::info('    Attempting to fill prix_unitaire_achat (best effort)...');
                 // Jointure pour obtenir le prix actuel du produit et le mettre dans l'achat
                 // ATTENTION: Ne reflète pas le prix historique si les prix changent !
                 DB::table('achats as a')
                    ->join('products as p', 'a.products_id', '=', 'p.id')
                    ->where('a.prix_unitaire_achat', '=', 0.00) // Remplir seulement si vide (ou condition appropriée)
                    ->orderBy('a.id')
                    ->chunk(500, function($achatsToUpdate) {
                        foreach ($achatsToUpdate as $achat) {
                            DB::table('achats')
                              ->where('id', $achat->id)
                              ->update(['prix_unitaire_achat' => $achat->prix_product]); // Utilise prix actuel du produit
                        }
                         Log::info('    Processed chunk for filling prix_unitaire_achat.');
                    });
                  Log::info('    Finished attempting to fill prix_unitaire_achat.');
             }

         } catch (\Exception $e) {
              $this->error("  ERROR during achat data copy: " . $e->getMessage());
              // Ne pas arrêter forcément toute la commande, mais signaler l'échec de cette étape
         }
    }
}
