<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'title'          => 'admin',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'programmer',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'graphic',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'office',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'support',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'sale',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'customer',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'technical Manager',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'sales manager',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'administrative manager',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'title'          => 'support Manager',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
        ]);
    }
}
