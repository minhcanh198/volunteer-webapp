<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::firstOrCreate(['name' => 'superadmin'], ['name' => 'superadmin']);
        $provider = Role::firstOrCreate(['name' => 'provider'], ['name' => 'provider']);
        $manager = Role::firstOrCreate(['name' => 'manager'], ['name' => 'manager']);

        $viewProgram = Permission::firstOrCreate(['name' => 'program_view'], ['name' => 'program_view']);
        $editProgram = Permission::firstOrCreate(['name' => 'program_edit'], ['name' => 'program_edit']);
        $deleteProgram = Permission::firstOrCreate(['name' => 'program_delete'], ['name' => 'program_delete']);
        $provider->syncPermissions([$viewProgram, $editProgram, $deleteProgram]);
    }
}
