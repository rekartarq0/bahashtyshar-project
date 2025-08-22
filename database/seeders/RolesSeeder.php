<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::firstOrCreate(['name' => 'admin']);
        $role_user = Role::firstOrCreate(['name' => 'user']);

        $permission_companies_read = Permission::firstOrCreate(['name' => 'read companies']);
        $permission_companies_write = Permission::firstOrCreate(['name' => 'write companies']);
        $permission_companies_edit = Permission::firstOrCreate(['name' => 'edit companies']);
        $permission_companies_delete = Permission::firstOrCreate(['name' => 'delete companies']);
        // users permision
        $permission_user_read = Permission::firstOrCreate(['name' => 'read users']);
        $permission_user_write = Permission::firstOrCreate(['name' => 'write users']);
        $permission_user_edit = Permission::firstOrCreate(['name' => 'edit users']);
        $permission_user_delete = Permission::firstOrCreate(['name' => 'delete users']);
        // roles permision
        $permission_roles_read = Permission::firstOrCreate(['name' => 'read roles']);
        $permission_roles_write = Permission::firstOrCreate(['name' => 'write roles']);
        $permission_roles_edit = Permission::firstOrCreate(['name' => 'edit roles']);
        $permission_roles_delete = Permission::firstOrCreate(['name' => 'delete roles']);
        //projects permision
        $permission_projects_read = Permission::firstOrCreate(['name' => 'read projects']);
        $permission_projects_write = Permission::firstOrCreate(['name' => 'write projects']);
        $permission_projects_edit = Permission::firstOrCreate(['name' => 'edit projects']); 
        $permission_projects_deleted=Permission::firstOrCreate(['name'=>'delete projects']);


        // permision of the users
        $permissions_admin =
            [
                $permission_companies_read,
                $permission_companies_write,
                $permission_companies_edit,
                $permission_companies_delete,
                $permission_user_read,
                $permission_user_write,
                $permission_user_edit,
                $permission_user_delete,
                $permission_roles_read,
                $permission_roles_write,
                $permission_roles_edit, 
                $permission_roles_delete,
                $permission_projects_read,
                $permission_projects_write,
                $permission_projects_edit,
                $permission_projects_deleted,

            ];
        // permision of the users
        $permission_users =
            [
                $permission_companies_read,
                $permission_companies_write,
                $permission_companies_edit,
                $permission_companies_delete,
            ];

        //assign permissions to the admin,user
        $role_admin->syncPermissions($permissions_admin);
        $role_user->syncPermissions($permission_users);
    }
}
