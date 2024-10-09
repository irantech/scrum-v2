<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contracts')->insert([
            [
                'user_id'         => 1,
                'type_id'         => 1,
                'old_id_customer' => 1,
                'contract_code'   => 0000,
                'title'           => 'قرارداد تست برای مشتری تستی',
                'description'     => 'متن قرارداد به شرح زیر میباشد.',
                'sign_date'       => Carbon::now(),
                'start_date'      => Carbon::now(),
                'end_date'        => Carbon::now()->addWeek()
            ]
        ]);
    }
}
