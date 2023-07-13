<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $operatorRole = Role::firstOrCreate(['name' => 'Operator']);

        // Create permissions
        $createUsersPermission = Permission::firstOrCreate(['name' => 'create users']);
        $createAddedUsersPermission = Permission::firstOrCreate(['name' => 'create added users']);
        $createOperationsPermission = Permission::firstOrCreate(['name' => 'create operations']);
        
        $editUsersPermission = Permission::firstOrCreate(['name' => 'edit users']);
        $editAddedUsersPermission = Permission::firstOrCreate(['name' => 'edit added users']);

        $deleteUsersPermission = Permission::firstOrCreate(['name' => 'delete users']);
        $deleteAddedUsersPermission = Permission::firstOrCreate(['name' => 'delete added users']);
        $deleteOperationsPermission = Permission::firstOrCreate(['name' => 'delete operations']);

        $readAddedUsersPermission = Permission::firstOrCreate(['name' => 'read added users']);
        $readOperationsPermission = Permission::firstOrCreate(['name' => 'read operations']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($createUsersPermission,
                                    $createAddedUsersPermission,
                                    $createOperationsPermission,
                                    $editUsersPermission,
                                    $editAddedUsersPermission,
                                    $deleteUsersPermission,
                                    $deleteAddedUsersPermission,
                                    $deleteOperationsPermission,
                                    $readAddedUsersPermission,
                                    $readOperationsPermission);

        $managerRole->givePermissionTo($createAddedUsersPermission,
                                    $createOperationsPermission,
                                    $editAddedUsersPermission,
                                    $deleteAddedUsersPermission,
                                    $deleteOperationsPermission,
                                    $readAddedUsersPermission,
                                    $readOperationsPermission);

        $operatorRole->givePermissionTo($readAddedUsersPermission,
                                    $readOperationsPermission,
                                    $createAddedUsersPermission,
                                    $createOperationsPermission);


        // $role = Role::where('name', 'Admin')->first();
        // $user = User::where('email', 'admin@gmail.com')->first();

        // $user->assignRole($role);
    }
}
