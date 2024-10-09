<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NewUserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		/*DB::table( 'users' )->insert( [
			[
				'name'     => 'Tabaei',
				'username' => 'retabaei',
				'email'    => 'retabaei@iran-tech.com',
				'password' => Hash::make( 'ITretabaei@123' ),
				'role'     => 'office'
			],
			[
				'name'     => 'Ganjali',
				'username' => 'acganjali',
				'email'    => 'acganjali@iran-tech.com',
				'password' => Hash::make( 'ITacganjali@123' ),
				'role'     => 'accountant'
			],
			[
				'name'     => 'Eibak',
				'username' => 'seeibak',
				'email'    => 'seeibak@iran-tech.com',
				'password' => Hash::make( 'ITseeibak@123' ),
				'role'     => 'support'
			],
			[
				'name'     => 'Sadeghian',
				'username' => 'sesadeghian',
				'email'    => 'sesadeghian@iran-tech.com',
				'password' => Hash::make( 'ITsesadeghian@123' ),
				'role'     => 'support'
			],
			[
				'name'     => 'Nazari',
				'username' => 'grnazari',
				'email'    => 'grnazari@iran-tech.com',
				'password' => Hash::make( 'ITgrnazari@123' ),
				'role'     => 'graphic'
			],
			[
				'name'     => 'Bahrami',
				'username' => 'sobahrami',
				'email'    => 'sobahrami@iran-tech.com',
				'password' => Hash::make( 'ITsobahrami@123' ),
				'role'     => 'programmer'
			],
			[
				'name'     => 'Kafi',
				'username' => 'sokafi',
				'email'    => 'sokafi@iran-tech.com',
				'password' => Hash::make( 'ITsokafi@123' ),
				'role'     => 'programmer'
			],
		] )*/
		;
		
		DB::table('users')->insert([
            [
                'name'     => 'Saniee',
                'username' => 'grsaniee',
                'email'    => 'grsaniee@iran-tech.com',
                'password' => Hash::make( 'ITgrsaniee@123' ),
                'role'     => 'graphic'
            ],
        ]);
	}
}
