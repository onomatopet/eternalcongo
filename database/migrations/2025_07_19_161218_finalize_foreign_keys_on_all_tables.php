<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Important

return new class extends Migration
{
    /**
     * Helper pour supprimer une FK en utilisant DB::statement pour une robustesse maximale.
     * Il interroge d'abord information_schema pour trouver le nom exact de la contrainte.
     */
    private function dropForeignKeySQL(string $tableName, string $columnName): void
    {
        $keyIdentifier = "{$tableName}.{$columnName}";
        Log::debug("Attempting to find and drop FK for {$keyIdentifier}...");

        try {
            // 1. Trouver le nom réel de la contrainte
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = ?
                  AND REFERENCED_TABLE_NAME IS NOT NULL;
            ", [$tableName, $columnName]);

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                Log::info("Found existing FK constraint '{$constraintName}' for {$keyIdentifier}. Dropping it.");
                // 2. Supprimer la contrainte par son nom si trouvée
                DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraintName}`");
                Log::info("Successfully dropped FK '{$constraintName}'.");
            } else {
                 Log::info("No existing FK constraint found for {$keyIdentifier}. Skipping drop.");
            }
        } catch (\Exception $e) {
             Log::error("An error occurred while trying to drop FK for {$keyIdentifier}: " . $e->getMessage());
             // Ne pas relancer pour permettre à la migration de continuer son nettoyage
        }
    }

    public function up(): void
    {
        Log::info('Finalizing database structure: Dropping old columns and adding FKs (v7 - DB::statement)...');

        // --- 1. Nettoyer la table achats ---
        Log::info('Step 1: Dropping old columns from achats...');
        Schema::table('achats', function (Blueprint $table) {
            if (Schema::hasColumn('achats', 'pointvaleur')) { $table->dropColumn('pointvaleur'); }
            if (Schema::hasColumn('achats', 'montant')) { $table->dropColumn('montant'); }
        });
        Log::info('Step 1: Finished dropping old columns.');

        // --- 2. TENTER DE SUPPRIMER TOUTES LES FK POTENTIELLES (via DB::statement) ---
        Log::info('Step 2: Preemptively dropping potentially existing FKs using raw SQL...');

        $this->dropForeignKeySQL('bonuses', 'distributeur_id');
        $this->dropForeignKeySQL('level_currents', 'id_distrib_parent');
        $this->dropForeignKeySQL('level_currents', 'distributeur_id');
        $this->dropForeignKeySQL('achats', 'products_id');
        $this->dropForeignKeySQL('achats', 'distributeur_id');
        $this->dropForeignKeySQL('products', 'pointvaleur_id');
        $this->dropForeignKeySQL('products', 'category_id');
        $this->dropForeignKeySQL('distributeurs', 'id_distrib_parent');

        Log::info('Step 2: Finished attempting to drop existing FKs.');


        // --- 3. Ajouter les clés étrangères (via Schema Builder) ---
        Log::info('Step 3: Adding Foreign Key constraints...');

        // a) Table distributeurs
        Schema::table('distributeurs', function (Blueprint $table) {
             try {
                  $table->foreign('id_distrib_parent')->references('id')->on('distributeurs')->onDelete('set null')->onUpdate('cascade');
                  Log::info('Added FK: distributeurs.id_distrib_parent -> distributeurs.id');
             } catch (\Exception $e) { Log::error("Failed to add FK on distributeurs: ".$e->getMessage()); throw $e; }
        });

        // b) Table products
        Schema::table('products', function (Blueprint $table) {
             try {
                 $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null')->onUpdate('cascade');
                 $table->foreign('pointvaleur_id')->references('id')->on('pointvaleurs')->onDelete('restrict')->onUpdate('cascade');
                 Log::info('Added FKs on products table.');
            } catch (\Exception $e) { Log::error("Failed to add FKs on products: ".$e->getMessage()); throw $e; }
        });

        // c) Table achats
        Schema::table('achats', function (Blueprint $table) {
             try {
                 $table->foreign('distributeur_id')->references('id')->on('distributeurs')->onDelete('restrict')->onUpdate('cascade');
                 $table->foreign('products_id')->references('id')->on('products')->onDelete('restrict')->onUpdate('cascade');
                 Log::info('Added FKs on achats table.');
             } catch (\Exception $e) { Log::error("Failed to add FKs on achats: ".$e->getMessage()); throw $e; }
        });

        // d) Table level_currents
        Schema::table('level_currents', function (Blueprint $table) {
             try {
                 $table->foreign('distributeur_id')->references('id')->on('distributeurs')->onDelete('cascade')->onUpdate('cascade');
                 $table->foreign('id_distrib_parent')->references('id')->on('distributeurs')->onDelete('set null')->onUpdate('cascade');
                 Log::info('Added FKs on level_currents table.');
             } catch (\Exception $e) { Log::error("Failed to add FKs on level_currents: ".$e->getMessage()); throw $e; }
        });

        // e) Table bonuses
        Schema::table('bonuses', function (Blueprint $table) {
             try {
                 $table->foreign('distributeur_id')->references('id')->on('distributeurs')->onDelete('cascade')->onUpdate('cascade');
                 Log::info('Added FK on bonuses table.');
             } catch (\Exception $e) { Log::error("Failed to add FK on bonuses: ".$e->getMessage()); throw $e; }
        });

        Log::info('Database structure finalized with Foreign Keys.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::warning('Reverting final structure changes (Dropping FKs, Re-adding columns)...');

        // Utiliser la même méthode de suppression pour down
        $this->dropForeignKeySQL('bonuses', 'distributeur_id');
        $this->dropForeignKeySQL('level_currents', 'id_distrib_parent');
        $this->dropForeignKeySQL('level_currents', 'distributeur_id');
        $this->dropForeignKeySQL('achats', 'products_id');
        $this->dropForeignKeySQL('achats', 'distributeur_id');
        $this->dropForeignKeySQL('products', 'pointvaleur_id');
        $this->dropForeignKeySQL('products', 'category_id');
        $this->dropForeignKeySQL('distributeurs', 'id_distrib_parent');

        // ... Recréer colonnes achats ...
        Schema::table('achats', function (Blueprint $table) {
             if (!Schema::hasColumn('achats', 'montant')) { $table->double('montant', 12, 2)->nullable(); }
             if (!Schema::hasColumn('achats', 'pointvaleur')) { $table->bigInteger('pointvaleur')->nullable(); }
         });
        Log::info('Reverted FKs and re-added old achats columns (data not restored).');
    }
};
