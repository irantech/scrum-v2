<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseProgressSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table( 'base_progress' )->insert( [
			[ 'title'               => 'مرحله اصلی تست',
			  'description'         => '',
			  'private_description' => '',
			  'section_id'          => 1,
			  'software_id'         => 1,
			  'user_role'           => 'admin',
			  'percentage'          => '10'
			],
		] )
		;
	}
}
