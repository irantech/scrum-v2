<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractTypSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contract_types')->insert([
            ['title' => 'طراحی سایت آژانس', 'description' => ''],
            ['title' => 'اتوماسیون', 'description' => ''],
            ['title' => 'باشگاه مشتریان', 'description' => ''],
            ['title' => 'Safar 360', 'description' => ''],
            ['title' => 'سفربانک', 'description' => ''],
            ['title' => 'طراحی سایت هتل', 'description' => ''],
            ['title' => 'رزرواسیون هتل', 'description' => ''],
            ['title' => 'طراحی سایت عمومی', 'description' => ''],
            ['title' => 'فروش sms', 'description' => ''],
            ['title' => 'سئو', 'description' => ''],
            ['title' => 'آموزش', 'description' => ''],
            ['title' => 'اپلیکیشن', 'description' => ''],
            ['title' => 'رزرواسیون چارتری', 'description' => ''],
        ]);
    }
}
