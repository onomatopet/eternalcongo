<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    /**
     * Crée un backup complet avant une suppression critique
     */
    public function createDeletionBackup(string $entityType, int $entityId, array $relatedData = []): array
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupId = uniqid('backup_');
            $backupDir = "backups/deletions/{$entityType}/{$timestamp}_{$backupId}";

            // Créer le dossier de backup
            Storage::disk('local')->makeDirectory($backupDir);

            $backupFiles = [];

            // 1. Backup de l'entité principale
            $mainEntityFile = $this->backupMainEntity($entityType, $entityId, $backupDir);
            $backupFiles['main_entity'] = $mainEntityFile;

            // 2. Backup des entités liées
            if (!empty($relatedData)) {
                $relatedFiles = $this->backupRelatedEntities($relatedData, $backupDir);
                $backupFiles['related_entities'] = $relatedFiles;
            }

            // 3. Créer un manifeste du backup
            $manifest = $this->createBackupManifest($entityType, $entityId, $relatedData, $backupFiles);
            $manifestFile = "{$backupDir}/manifest.json";
            Storage::disk('local')->put($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));
            $backupFiles['manifest'] = $manifestFile;

            Log::info("Backup de suppression créé", [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'backup_id' => $backupId,
                'backup_dir' => $backupDir
            ]);

            return [
                'success' => true,
                'backup_id' => $backupId,
                'backup_dir' => $backupDir,
                'files' => $backupFiles,
                'manifest' => $manifest
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
     * Backup de l'entité principale
     */
    private function backupMainEntity(string $entityType, int $entityId, string $backupDir): string
    {
        $tableName = $this->getTableName($entityType);

        // Récupérer les données de l'entité
        $entityData = DB::table($tableName)->where('id', $entityId)->first();

        if (!$entityData) {
            throw new \Exception("Entité {$entityType} avec ID {$entityId} non trouvée");
        }

        // Sauvegarder en JSON
        $filename = "{$backupDir}/{$entityType}_{$entityId}.json";
        $data = [
            'table' => $tableName,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data' => (array) $entityData,
            'backup_date' => now()->toISOString()
        ];

        Storage::disk('local')->put($filename, json_encode($data, JSON_PRETTY_PRINT));

        return $filename;
    }

    /**
     * Backup des entités liées
     */
    private function backupRelatedEntities(array $relatedData, string $backupDir): array
    {
        $relatedFiles = [];

        foreach ($relatedData as $relation => $data) {
            if (empty($data)) continue;

            $filename = "{$backupDir}/related_{$relation}.json";
            $backupData = [
                'relation' => $relation,
                'count' => count($data),
                'data' => $data,
                'backup_date' => now()->toISOString()
            ];

            Storage::disk('local')->put($filename, json_encode($backupData, JSON_PRETTY_PRINT));
            $relatedFiles[$relation] = $filename;
        }

        return $relatedFiles;
    }

    /**
     * Crée le manifeste du backup
     */
    private function createBackupManifest(string $entityType, int $entityId, array $relatedData, array $backupFiles): array
    {
        return [
            'backup_info' => [
                'id' => uniqid('manifest_'),
                'created_at' => now()->toISOString(),
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System'
            ],
            'entity_details' => [
                'type' => $entityType,
                'id' => $entityId,
                'related_entities_count' => count($relatedData)
            ],
            'backup_files' => $backupFiles,
            'related_data_summary' => array_map(function($data) {
                return ['count' => is_array($data) ? count($data) : 1];
            }, $relatedData),
            'restoration_notes' => [
                'warning' => 'Ce backup a été créé avant une suppression. Utilisez restore() pour récupérer les données.',
                'tables_affected' => array_keys($relatedData)
            ]
        ];
    }

    /**
     * Restaure des données depuis un backup
     */
    public function restoreFromBackup(string $backupId): array
    {
        try {
            // Trouver le dossier de backup
            $backupDir = $this->findBackupDirectory($backupId);
            if (!$backupDir) {
                throw new \Exception("Backup {$backupId} non trouvé");
            }

            // Charger le manifeste
            $manifestPath = "{$backupDir}/manifest.json";
            if (!Storage::disk('local')->exists($manifestPath)) {
                throw new \Exception("Manifeste du backup non trouvé");
            }

            $manifest = json_decode(Storage::disk('local')->get($manifestPath), true);

            // Commencer la restauration
            DB::beginTransaction();

            $restored = [];

            // 1. Restaurer l'entité principale
            $mainEntityFile = $manifest['backup_files']['main_entity'];
            $mainEntityData = json_decode(Storage::disk('local')->get($mainEntityFile), true);

            DB::table($mainEntityData['table'])->insert($mainEntityData['data']);
            $restored['main_entity'] = $mainEntityData['entity_type'];

            // 2. Restaurer les entités liées
            if (isset($manifest['backup_files']['related_entities'])) {
                foreach ($manifest['backup_files']['related_entities'] as $relation => $file) {
                    $relatedData = json_decode(Storage::disk('local')->get($file), true);
                    // Note: La restauration des entités liées nécessite une logique spécifique selon le type
                    $restored['related'][$relation] = $relatedData['count'];
                }
            }

            DB::commit();

            Log::info("Restauration depuis backup réussie", [
                'backup_id' => $backupId,
                'restored' => $restored
            ]);

            return [
                'success' => true,
                'restored' => $restored,
                'backup_id' => $backupId
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
     * Liste tous les backups disponibles
     */
    public function listBackups(string $entityType = null): array
    {
        $backupsDir = 'backups/deletions';
        if ($entityType) {
            $backupsDir .= "/{$entityType}";
        }

        $directories = Storage::disk('local')->directories($backupsDir);
        $backups = [];

        foreach ($directories as $dir) {
            $manifestPath = "{$dir}/manifest.json";
            if (Storage::disk('local')->exists($manifestPath)) {
                $manifest = json_decode(Storage::disk('local')->get($manifestPath), true);
                $backups[] = [
                    'backup_id' => basename($dir),
                    'entity_type' => $manifest['backup_info']['entity_type'],
                    'entity_id' => $manifest['backup_info']['entity_id'],
                    'created_at' => $manifest['backup_info']['created_at'],
                    'user_name' => $manifest['backup_info']['user_name'],
                    'size' => $this->getBackupSize($dir)
                ];
            }
        }

        return $backups;
    }

    /**
     * Utilitaires privés
     */
    private function getTableName(string $entityType): string
    {
        $tableMap = [
            'distributeur' => 'distributeurs',
            'achat' => 'achats',
            'product' => 'products',
            'bonus' => 'bonuses'
        ];

        return $tableMap[$entityType] ?? $entityType . 's';
    }

    private function findBackupDirectory(string $backupId): ?string
    {
        $directories = Storage::disk('local')->allDirectories('backups/deletions');

        foreach ($directories as $dir) {
            if (str_contains($dir, $backupId)) {
                return $dir;
            }
        }

        return null;
    }

    private function getBackupSize(string $dir): string
    {
        $files = Storage::disk('local')->allFiles($dir);
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += Storage::disk('local')->size($file);
        }

        return $this->formatBytes($totalSize);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
