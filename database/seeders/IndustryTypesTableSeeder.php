<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustryTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('industry_types')->insert([
            [
                'name' => 'Mining',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Trade',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Finance and Business Services',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Real Estate',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Manufacturing',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '60',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Construction',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '60',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '40',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Agriculture',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '90',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '40',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Parastatals',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
            [
                'name' => 'Transport and Communications',
                'current_ratio' => '2',
                'quick_ratio' => '1',
                'debtor_days' => '30',
                'creditor_days' => '30',
                'working_capital' => '',
                'turnover_growth' => '4',
                'gross_profit' => '30',
                'operating_profit_margin' => '15',
                'net_profit_margin' => '10',
                'return_on_equity' => '15',
                'return_on_assets' => '11',
                'return_on_investment' => '8',
                'gearing_ratio' => '150',
                'long_term_debt' => '100',
                'tangible_net_worth' => '80',
                'total_assets' => '',
                'solvency' => '1.5',
                'interest_cover' => '7',
                'gross_interest_debts' => '',
                'total_assets_turnover' => '10',
                'fixed_assets_turn_over' => '8',
            ],
        ]);
    }
}