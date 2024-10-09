<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            [
                'email'           => 'customer@iran-tech.com',
                'name'            => 'Test Customer',
                'old_user'        => 'test',
                'old_pass'        => Hash::make('password'),
                'old_id_customer' => 1,
                'old_number'      => 1,
                'old_whmcs_id'    => 1,
                'old_whmcs_hash'  => md5(1)
            ]
        ]);
    }
}
