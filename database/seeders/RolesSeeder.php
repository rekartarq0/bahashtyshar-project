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
        $role_photographer = Role::firstOrCreate(['name' => 'photographer']);
        $role_Request = Role::firstOrCreate(['name' => 'Request']);
        $role_prepare = Role::firstOrCreate(['name' => 'prepare']);
        $role_handling = Role::firstOrCreate(['name' => 'handling']);
        //show
        $role_show = Role::firstOrCreate(['name' => 'show']);
        $role_contract = Role::firstOrCreate(['name' => 'contract']);

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
        //projects permision
        $permission_photographer_read = Permission::firstOrCreate(['name' => 'read photographer']);
        $permission_photographer_write = Permission::firstOrCreate(['name' => 'write photographer']);
        $permission_photographer_edit = Permission::firstOrCreate(['name' => 'edit photographer']);
        $permission_photographer_deleted = Permission::firstOrCreate(['name' => 'delete photographer']);
        //permission Dashboard
        $permission_dashboard_read = Permission::firstOrCreate(['name' => 'read dashboard']);
        //permission ScreenOne
        $permission_screenone_read = Permission::firstOrCreate(['name' => 'read screenOne']);
        //permission ScreenTwo
        $permission_screentwo_read = Permission::firstOrCreate(['name' => 'read screenTwo']);
        // permission Darxsta
        $permission_darxsta_read = Permission::firstOrCreate(['name' => 'read darxsta']);
        //permission Repport
        $permission_repport_read = Permission::firstOrCreate(['name' => 'read repport']);
$permission_stage_read_show = Permission::firstOrCreate(['name' => 'read show']);
$permission_stage_read_handling = Permission::firstOrCreate(['name' => 'read handling']);

        
        $permission_stage_read_sale = Permission::firstOrCreate(['name' => 'read Sale']);
        $permission_stage_read_Measurement = Permission::firstOrCreate(['name' => 'read Measurment']);
        $permission_stage_read_Request = Permission::firstOrCreate(['name' => 'read request']);
        $permission_stage_read_Pricing = Permission::firstOrCreate(['name' => 'read Pricing']);
        $permission_stage_read_prepare = Permission::firstOrCreate(['name' => 'read prepare']);
        $permission_stage_read_Planning = Permission::firstOrCreate(['name' => 'read Planning']);
        $permission_stage_read_Reklam = Permission::firstOrCreate(['name' => 'read Reklam']);
        $permission_stage_read_contract = Permission::firstOrCreate(['name' => 'read contract']);

        $permission_stage_change_stage = Permission::firstOrCreate(['name' => 'change stage']);

        //desghin permission
        $role_Request->syncPermissions([
            $permission_dashboard_read,
            $permission_stage_read_Request,
            $permission_projects_edit,
            $permission_stage_change_stage
        ]);
       
        // prepare permission
        $role_prepare->syncPermissions([
            $permission_dashboard_read,
            $permission_stage_read_prepare,
            $permission_stage_change_stage,
            $permission_projects_edit,

        ]);
        //show
        $role_show->syncPermissions([
            $permission_dashboard_read,
            $permission_stage_read_Planning,
            $permission_stage_read_show,
            $permission_stage_change_stage,
            $permission_stage_read_Measurement,
            $permission_projects_edit,

        ]);
        //handling
        $role_handling->syncPermissions([
            $permission_dashboard_read,
            $permission_stage_read_sale,
            $permission_stage_read_handling,
            $permission_projects_write,
            $permission_projects_edit,
            $permission_stage_change_stage
        ]);
        //contract
        $role_contract->syncPermissions([
            $permission_dashboard_read,
            $permission_stage_read_contract,
            $permission_projects_write,
            $permission_projects_edit,
            $permission_stage_change_stage
        ]);
        // permision of the users
        $permissions_admin =
            [
                $permission_darxsta_read,
                $permission_stage_read_show ,
                $permission_stage_read_handling,
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
            $permission_photographer_read,
            $permission_photographer_write,
            $permission_photographer_edit,
            $permission_photographer_deleted,
            $permission_dashboard_read,
            $permission_screenone_read,
            $permission_screentwo_read,
            $permission_repport_read,
            $permission_stage_read_sale,
            $permission_stage_read_Measurement,
            $permission_stage_read_Request,
            $permission_stage_read_Pricing,
            $permission_stage_read_prepare,
            $permission_stage_read_Planning,
            $permission_stage_read_Reklam,
            $permission_stage_change_stage,
            $permission_stage_read_contract,

            ];
        // permision of the users
        $permission_users =
            [
                $permission_companies_read,
                $permission_companies_write,
                $permission_companies_edit,
                $permission_companies_delete,
                $permission_dashboard_read,
                $permission_screenone_read,
                $permission_screentwo_read
            ];

        // permision of the photographer
        $permission_photographers =
            [
                $permission_photographer_read,
                $permission_photographer_write,
                $permission_photographer_edit,
                $permission_photographer_deleted,
                $permission_dashboard_read,
                $permission_stage_read_Reklam,
                $permission_stage_change_stage,
                $permission_projects_edit,
            ];
          



        //assign permissions to the admin,user
        $role_admin->syncPermissions($permissions_admin);
        $role_user->syncPermissions($permission_users);
        $role_photographer->syncPermissions($permission_photographers);
    }
}
