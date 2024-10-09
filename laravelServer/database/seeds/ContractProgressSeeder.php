<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contract_progress')->insert([
            [
                'contract_id'      => 1,
                'base_progress_id' => 1,
                'status'           => 'running',
            ]
        ]);
    }
}
