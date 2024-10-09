<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ContractTypSeeder::class,
            CustomerSeeder::class,
            SectionSeeder::class,
            RoleSeeder::class ,
            UserSeeder::class,
            BaseProgressSeeder::class,
            ContractProgressSeeder::class,
            ContractSubProgressSeeder::class,
            ContractSeeder::class,
            SubProgressSeeder::class,
            PermissionSeeder::class
//             ContractTypeSeeder::class,
//             SectionSeeder::class,
        ]);
        DB::table('softwares')->insert([
            ['title' => 'نرم افزار حسابداری'],
            ['title' => 'نرم افزار اتوماسیون'],

        ]);
    }
}
