<?php
use App\models\Role;
use Illuminate\Database\Seeder;
use App\models\Permission;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
       // DB::table('role_user')->truncate();
        //DB::table('roles')->truncate();
        DB::table('permission_role')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $roles = [
            [
                'name' => 'admin', 
                'display_name' => 'Global Admin', 
                'description' => 'Full Permission',
                'accesslevel' => 'tpv',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'tpv_admin',
                'display_name' => 'TPV Admin',
                'description' => 'TPV Admin',
                'accesslevel' => 'tpv',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'name' => 'tpv_qa',
                'display_name' => 'TPV QA',
                'description' => 'TPV Qa',
                'accesslevel' => 'tpv',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'name' => 'client_admin',
                'display_name' => 'Client Admin',
                'description' => 'Client Admin',
                'accesslevel' => 'client',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'name' => 'sales_center_admin',
                'display_name' => 'Sales Center Admin',
                'description' => 'Sales Center Admin',
                'accesslevel' => 'salescenter',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'name' => 'sales_center_qa',
                'display_name' => 'Sales Center QA',
                'description' => 'Sales Center QA',
                'accesslevel' => 'salescenter',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ],
            [
                'name' => 'sales_center_location_admin',
                'display_name' => 'Sales Center Location Admin',
                'description' => 'Sales Center Location Admin',
                'accesslevel' => 'salescenter',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]
        ];
        
        foreach ($roles as $key => $value) {
            $role = Role::updateOrCreate(['name'=>$value['name']],$value);

            $allowedPermissions = config()->get('constants.roles.' . array_get($role, 'name'));

            $permissions = Permission::whereIn('name', $allowedPermissions)->get();

            foreach ($permissions as $key => $value) {
                $role->attachPermission($value);
            }

        }
    }
}
