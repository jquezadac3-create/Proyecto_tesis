<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'assign new admin']);
        Permission::create(['name' => 'manage qr codes']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo('view users');
        $adminRole->givePermissionTo('create user');
        $adminRole->givePermissionTo('edit user');
        $adminRole->givePermissionTo('delete user');
        $adminRole->givePermissionTo('assign new admin');
        $adminRole->givePermissionTo('manage qr codes');

        $sellerRole = Role::create(['name' => 'seller']);
        $sellerRole->givePermissionTo('edit user');
        // $sellerRole->givePermissionTo('view users'); --- The seller role should not have this permission
        $sellerRole->givePermissionTo('manage qr codes');
    }
}
