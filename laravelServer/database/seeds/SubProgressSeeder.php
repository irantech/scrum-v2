<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_progress')->insert([
            ['title' => 'زیر مجموعه تست', 'description' => '', 'base_progress_id' => 1, 'section_id' => 1],

        ]);
    }
}
