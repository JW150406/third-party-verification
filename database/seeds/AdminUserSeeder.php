<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'admin@tpv360.com')->first();

        if (empty($user)) {
            $user = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@tpv360.com',
                'password' => bcrypt('tpv@123'),
                'access_level' => 'tpv'
            ]);
            $user->userid = "a" . $user->id;
            $user->save();
            $user->attachRole(1);
        }
    }
}
