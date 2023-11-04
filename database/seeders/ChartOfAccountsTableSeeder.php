<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chart_of_accounts')->insert([
            [
                'name' => '',
                'gl_code' => '',
                'account_type' => '',
                'description' => '',
            ],
            [
                'name' => '',
                'gl_code' => '',
                'account_type' => '',
                'description' => '',
            ],
            [
                'name' => '',
                'gl_code' => '',
                'account_type' => '',
                'description' => '',
            ],
            [
                'name' => '',
                'gl_code' => '',
                'account_type' => '',
                'description' => '',
            ],
            [
                'name' => '',
                'gl_code' => '',
                'account_type' => '',
                'description' => '',
            ],
        ]);
    }
}
