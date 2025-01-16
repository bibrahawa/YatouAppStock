<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Réinitialiser les données existantes
        Role::truncate();
        Permission::truncate();

        // Créer les rôles
        $roles = [
            'Admin' => Role::create(['name' => 'Admin']),
            'Gerant' => Role::create(['name' => 'Gerant']),
            'Caissier' => Role::create(['name' => 'Caissier']),
        ];

        // Liste des permissions et leurs rôles associés
        $permissions = [
            "admin.access" => ['Admin', 'Gerant', 'Caissier'],
            "admins.manage" => ['Admin'],
            "admins.create" => ['Admin'],
            "category.create" => ['Admin', 'Gerant'],
            "category.manage" => ['Admin', 'Gerant'],
            "product.create" => ['Admin', 'Gerant'],
            "product.manage" => ['Admin', 'Gerant'],
            "product.view" => ['Admin', 'Gerant', 'Caissier'],
            "customer.create" => ['Admin', 'Gerant', 'Caissier'],
            "customer.manage" => ['Admin', 'Gerant'],
            "customer.view" => ['Admin', 'Gerant', 'Caissier'],
            "supplier.create" => ['Admin', 'Gerant', 'Caissier'],
            "supplier.manage" => ['Admin', 'Gerant'],
            "supplier.view" => ['Admin', 'Gerant', 'Caissier'],
            "user.create" => ['Admin', 'Gerant'],
            "user.manage" => ['Admin', 'Gerant'],
            "sell.create" => ['Admin', 'Gerant', 'Caissier'],
            "sell.manage" => ['Admin', 'Gerant'],
            "return.create" => ['Admin', 'Gerant', 'Caissier'],
            "purchase.create" => ['Admin', 'Gerant'],
            "purchase.manage" => ['Admin', 'Gerant'],
            "transaction.view" => ['Admin', 'Gerant'],
            "expense.create" => ['Admin', 'Gerant'],
            "expense.manage" => ['Admin', 'Gerant'],
            "settings.manage" => ['Admin', 'Gerant'],
            "acl.manage" => ['Admin', 'Gerant'],
            "acl.set" => ['Admin', 'Gerant'],
            "tax.actions" => ['Admin', 'Gerant'],
            "branch.create" => ['Admin', 'Gerant'],
            "report.view" => ['Admin', 'Gerant'],
            "profit.view" => ['Admin', 'Gerant'],
            "cash.view" => ['Admin', 'Gerant'],
            "profit.graph" => ['Admin', 'Gerant'],
        ];

        foreach ($permissions as $permission => $roleName) {
            $permissionObject = Permission::createPermission($permission);
            $rolesIds = Role::whereIn('name', $roleName)->pluck('id')->toArray();
            $permissionObject->roles()->sync($rolesIds);
        }

        // Create initial user
        User::truncate();

        $su = User::firstOrCreate(
            [ 'email' => 'bibrah@yatou.store' ],
            [
                'first_name' => 'Ibrahim',
                'last_name' => 'Barry',
                'password' => 'Admin@01'
            ]
        );

        $su->roles()->sync([1]);

    }
}
