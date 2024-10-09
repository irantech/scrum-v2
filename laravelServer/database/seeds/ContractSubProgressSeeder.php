<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContractSubProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contract_sub_progress')->insert([
            [
                'contract_id'     => 1,
                'sub_progress_id' => 1,
                'status'          => 'running',
                'estimated_time'  => '1 week',
                'refer_to'        => '',
                'start_date'=>  Carbon::now()

            ]
        ]);
    }
}
