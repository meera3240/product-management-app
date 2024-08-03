<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'sub-admin']);

        // Create permissions
        Permission::create(['name' => 'manage products']);

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $subAdminRole = Role::findByName('sub-admin');

        $adminRole->givePermissionTo('manage products');
        $subAdminRole->givePermissionTo('manage products');
    }

}
