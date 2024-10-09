<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sections')->insert([
            ['title'=>'بخش اداری ','description'=>''],
            ['title'=>'بخش طراح ','description'=>''],
            ['title'=>'بخش فنی - گرافیک','description'=>''],
            ['title'=>'بخش فنی - برنامه نویسی','description'=>''],
            ['title'=>'بخش پشتیبانی ','description'=>''],
            ['title'=>'بخش فروش ','description'=>''],
        ]);
    }
}
