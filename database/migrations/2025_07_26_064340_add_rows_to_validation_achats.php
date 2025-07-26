<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            // Status de validation
            $table->enum('status', ['pending', 'validated', 'rejected', 'cancelled'])
                  ->default('pending')
                  ->after('online')
                  ->comment('Statut de validation de l\'achat');

            // Date de validation
            $table->timestamp('validated_at')->nullable()
                  ->after('status')
                  ->comment('Date de validation/rejet');

            // Erreurs de validation (JSON)
            $table->json('validation_errors')->nullable()
                  ->after('validated_at')
                  ->comment('Erreurs de validation si rejeté');

            // Index pour les requêtes
            $table->index(['period', 'status'], 'idx_achats_period_status');
            $table->index('validated_at');
        });

        // Mettre à jour les achats existants comme validés
        DB::statement("UPDATE achats SET status = 'validated', validated_at = created_at WHERE status = 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            // Supprimer les index
            $table->dropIndex('idx_achats_period_status');
            $table->dropIndex(['validated_at']);

            // Supprimer les colonnes
            $table->dropColumn(['status', 'validated_at', 'validation_errors']);
        });
    }
};
