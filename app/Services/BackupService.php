<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BackupService
{
    private string $backupPath = 'backups/deletions';

    /**
     * Crée un backup avant suppression
     */
    public function createDeletionBackup(string $entityType, int $entityId, array $relatedData = []): array
    {
        try {
            $backupId = $this->generateBackupId($entityType, $entityId);
            $timestamp = now();

            // Préparer les données du backup
            $backupData = [
                'backup_id' => $backupId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'created_at' => $timestamp->toISOString(),
                'created_by' => Auth::id() ?? 0,
                'entity_data' => $this->getEntityData($entityType, $entityId),
                'related_data' => $relatedData,
                'metadata' => [
                    'app_version' => config('app.version', '1.0'),
                    'db_connection' => config('database.default'),
                    'user_ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            ];

            // Sauvegarder dans le système de fichiers
            $filename = "{$this->backupPath}/{$entityType}/{$backupId}.json";
            Storage::disk('local')->put($filename, json_encode($backupData, JSON_PRETTY_PRINT));

            // Sauvegarder également dans la base de données pour un accès rapide
            DB::table('deletion_backups')->insert([
                'backup_id' => $backupId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'backup_data' => json_encode($backupData),
                'file_path' => $filename,
                'created_by' => Auth::id() ?? 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            Log::info("Backup créé avec succès", [
                'backup_id' => $backupId,
                'entity_type' => $entityType,
                'entity_id' => $entityId
            ]);

            return [
                'success' => true,
                'backup_id' => $backupId,
                'file_path' => $filename,
                'created_at' => $timestamp
            ];

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du backup", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Restaure depuis un backup
     */
    public function restoreFromBackup(string $backupId): array
    {
        try {
            // Récupérer le backup depuis la DB
            $backup = DB::table('deletion_backups')
                ->where('backup_id', $backupId)
                ->first();

            if (!$backup) {
                throw new \Exception("Backup introuvable : {$backupId}");
            }

            $backupData = json_decode($backup->backup_data, true);

            // Vérifier que l'entité n'existe pas déjà
            if ($this->entityExists($backupData['entity_type'], $backupData['entity_id'])) {
                throw new \Exception("L'entité existe déjà et ne peut pas être restaurée");
            }

            DB::beginTransaction();

            // Restaurer l'entité principale
            $this->restoreEntity($backupData['entity_type'], $backupData['entity_data']);

            // Restaurer les données liées si présentes
            if (!empty($backupData['related_data'])) {
                $this->restoreRelatedData($backupData['entity_type'], $backupData['entity_id'], $backupData['related_data']);
            }

            // Marquer le backup comme restauré
            DB::table('deletion_backups')
                ->where('backup_id', $backupId)
                ->update([
                    'restored_at' => now(),
                    'restored_by' => Auth::id() ?? 0,
                    'updated_at' => now()
                ]);

            DB::commit();

            Log::info("Restauration réussie depuis le backup", [
                'backup_id' => $backupId,
                'entity_type' => $backupData['entity_type'],
                'entity_id' => $backupData['entity_id']
            ]);

            return [
                'success' => true,
                'entity_type' => $backupData['entity_type'],
                'entity_id' => $backupData['entity_id'],
                'message' => 'Restauration réussie'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur lors de la restauration", [
                'backup_id' => $backupId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Liste les backups disponibles
     */
    public function listBackups(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
{
    $query = DB::table('deletion_backups');

    // Appliquer les filtres
    if (!empty($filters['entity_type'])) {
        $query->where('entity_type', $filters['entity_type']);
    }

    if (!empty($filters['date_from'])) {
        $query->whereDate('created_at', '>=', $filters['date_from']);
    }

    if (!empty($filters['date_to'])) {
        $query->whereDate('created_at', '<=', $filters['date_to']);
    }

    if (!empty($filters['restored'])) {
        if ($filters['restored'] === 'yes') {
            $query->whereNotNull('restored_at');
        } else {
            $query->whereNull('restored_at');
        }
    }

    // Ordonner par date de création décroissante
    $query->orderBy('created_at', 'desc');

    // Paginer les résultats
    $backups = $query->paginate(20);

    // Transformer les données JSON en tableaux PHP
    $backups->transform(function ($backup) {
        $backup->backup_data = json_decode($backup->backup_data, true);

        // Ajouter la relation creator si nécessaire
        if ($backup->created_by) {
            $backup->creator = DB::table('users')
                ->where('id', $backup->created_by)
                ->first(['id', 'name', 'email']);
        }

        // Convertir les dates en objets Carbon
        $backup->created_at = Carbon::parse($backup->created_at);
        $backup->restored_at = $backup->restored_at ? Carbon::parse($backup->restored_at) : null;

        return $backup;
    });

    return $backups;
}

    /**
     * Nettoie les anciens backups
     */
    public function cleanupOldBackups(int $daysToKeep = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        // Récupérer les backups à supprimer
        $backupsToDelete = DB::table('deletion_backups')
            ->where('created_at', '<', $cutoffDate)
            ->whereNull('restored_at') // Ne pas supprimer les backups restaurés
            ->get();

        $deletedCount = 0;

        foreach ($backupsToDelete as $backup) {
            try {
                // Supprimer le fichier
                if (Storage::disk('local')->exists($backup->file_path)) {
                    Storage::disk('local')->delete($backup->file_path);
                }

                // Supprimer l'entrée DB
                DB::table('deletion_backups')
                    ->where('id', $backup->id)
                    ->delete();

                $deletedCount++;
            } catch (\Exception $e) {
                Log::warning("Impossible de supprimer le backup", [
                    'backup_id' => $backup->backup_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Nettoyage des backups terminé", [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateTimeString()
        ]);

        return $deletedCount;
    }

    /**
     * Génère un ID unique pour le backup
     */
    private function generateBackupId(string $entityType, int $entityId): string
    {
        return sprintf(
            '%s_%d_%s_%s',
            $entityType,
            $entityId,
            now()->format('Ymd_His'),
            Str::random(6)
        );
    }

    /**
     * Récupère les données de l'entité
     */
    private function getEntityData(string $entityType, int $entityId): array
    {
        switch ($entityType) {
            case 'distributeur':
                $entity = DB::table('distributeurs')->find($entityId);
                break;
            case 'achat':
                $entity = DB::table('achats')->find($entityId);
                break;
            case 'product':
                $entity = DB::table('products')->find($entityId);
                break;
            case 'bonus':
                $entity = DB::table('bonuses')->find($entityId);
                break;
            default:
                throw new \Exception("Type d'entité non supporté : {$entityType}");
        }

        if (!$entity) {
            throw new \Exception("Entité introuvable : {$entityType}#{$entityId}");
        }

        return (array) $entity;
    }

    /**
     * Vérifie si une entité existe
     */
    private function entityExists(string $entityType, int $entityId): bool
    {
        switch ($entityType) {
            case 'distributeur':
                return DB::table('distributeurs')->where('id', $entityId)->exists();
            case 'achat':
                return DB::table('achats')->where('id', $entityId)->exists();
            case 'product':
                return DB::table('products')->where('id', $entityId)->exists();
            case 'bonus':
                return DB::table('bonuses')->where('id', $entityId)->exists();
            default:
                return false;
        }
    }

    /**
     * Restaure une entité
     */
    private function restoreEntity(string $entityType, array $entityData): void
    {
        // Retirer les timestamps pour éviter les conflits
        unset($entityData['created_at'], $entityData['updated_at']);

        // Ajouter les nouveaux timestamps
        $entityData['created_at'] = now();
        $entityData['updated_at'] = now();

        switch ($entityType) {
            case 'distributeur':
                DB::table('distributeurs')->insert($entityData);
                break;
            case 'achat':
                DB::table('achats')->insert($entityData);
                break;
            case 'product':
                DB::table('products')->insert($entityData);
                break;
            case 'bonus':
                DB::table('bonuses')->insert($entityData);
                break;
            default:
                throw new \Exception("Type d'entité non supporté pour la restauration : {$entityType}");
        }
    }

    /**
     * Restaure les données liées
     */
    private function restoreRelatedData(string $entityType, int $entityId, array $relatedData): void
    {
        // Cette méthode peut être étendue selon les besoins spécifiques
        // Pour l'instant, on log simplement les données liées
        Log::info("Données liées disponibles pour restauration", [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'related_keys' => array_keys($relatedData)
        ]);

        // Exemple : restaurer les achats d'un distributeur
        if ($entityType === 'distributeur' && isset($relatedData['achats'])) {
            foreach ($relatedData['achats'] as $achat) {
                unset($achat['created_at'], $achat['updated_at']);
                $achat['created_at'] = now();
                $achat['updated_at'] = now();
                DB::table('achats')->insert($achat);
            }
        }
    }

    /**
     * Exporte un backup vers un fichier téléchargeable
     */
    public function exportBackup(string $backupId): array
    {
        try {
            $backup = DB::table('deletion_backups')
                ->where('backup_id', $backupId)
                ->first();

            if (!$backup) {
                throw new \Exception("Backup introuvable : {$backupId}");
            }

            $filename = "backup_{$backupId}.json";
            $content = json_encode(json_decode($backup->backup_data), JSON_PRETTY_PRINT);

            return [
                'success' => true,
                'filename' => $filename,
                'content' => $content,
                'mime_type' => 'application/json'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
