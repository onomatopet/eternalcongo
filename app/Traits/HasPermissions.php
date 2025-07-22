<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        // Pour l'instant, un système simple basé sur les rôles
        // Vous pouvez l'étendre avec un système de permissions plus complexe

        $adminPermissions = [
            'access_admin',
            'view_distributeurs',
            'create_distributeurs',
            'edit_distributeurs',
            'delete_distributeurs',
            'view_achats',
            'create_achats',
            'edit_achats',
            'delete_achats',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_bonuses',
            'create_bonuses',
            'edit_bonuses',
            'delete_bonuses',
            'execute_advancements',
            'execute_regularization',
            'view_deletion_requests',
            'export_data',
            'view_backups',
        ];

        $superAdminPermissions = array_merge($adminPermissions, [
            'approve_deletions',
            'execute_deletions',
            'force_delete',
            'restore_backups',
            'manage_all_deletion_requests',
        ]);

        // Vérification basée sur le rôle ou un champ dans la table users
        if ($this->hasRole('super_admin') || $this->is_super_admin) {
            return in_array($permission, $superAdminPermissions);
        }

        if ($this->hasRole('admin') || $this->is_admin) {
            return in_array($permission, $adminPermissions);
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        // Si vous avez un champ 'role' dans votre table users
        if (isset($this->role)) {
            return $this->role === $role;
        }

        // Ou si vous avez des champs booléens séparés
        switch ($role) {
            case 'super_admin':
                return $this->is_super_admin ?? false;
            case 'admin':
                return $this->is_admin ?? false;
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à l'admin
     */
    public function canAccessAdmin(): bool
    {
        return $this->hasPermission('access_admin');
    }
}
