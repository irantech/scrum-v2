<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table( 'users' )->insert( [
			[
				'name'     => 'Superadmin',
				'username' => 'admin',
				'email'    => 'admin@iran-tech.com',
                'role_id'  => 1 ,
				'password' => Hash::make( 'Admin@123' ),
				'role'     => 'admin'
			]
		
		] )
		;
	}
}
