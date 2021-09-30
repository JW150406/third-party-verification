<?php

use Illuminate\Database\Seeder;
use App\User;
use App\models\Role;
use App\models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            ZipcodeSeeder::class,
        ]);
    }
}
