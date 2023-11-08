<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\BalanceSheet;
use App\Models\BalanceSheetData;
use App\Models\IncomeStatement;
use App\Models\RatioAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientRatioAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:clients.ratio_analysis.index'])->only(['index', 'show']);
        $this->middleware(['permission:clients.ratio_analysis.create'])->only(['create', 'store']);
        $this->middleware(['permission:clients.ratio_analysis.update'])->only(['edit', 'update']);
        $this->middleware(['permission:clients.ratio_analysis.destroy'])->only(['destroy']);
    }

    public function index(Client $client)
    {

        $ratio = RatioAnalysis::where('client_id', $client->id)->first();
        $sheets = BalanceSheet::where('client_id', $client->id)
            ->orderBy('year')
            ->groupBy('year')
            ->get();
        $data = [
        ];
        foreach ($sheets as $sheet) {
            $incomeStatement = IncomeStatement::where('year', $sheet->year)->first();
            $turnoverGrowth = 0;
            if ($statement = IncomeStatement::where('year', $sheet->year - 1)->first()) {
                $turnoverGrowth = $statement->total_sales ? round(($incomeStatement->total_sales / $statement->total_sales - 1) * 100) : 0;
            }
            $data[$sheet->year] = [
                'working_capital_total_assets' => round(($sheet->total_current_assets + $sheet->total_other_current_assets - $sheet->total_current_liabilities) / ($sheet->total_assets), 2),
                'retained_earnings_total_assets' => round(($sheet->total_retained_earnings) / ($sheet->total_assets), 2),
                'ebit_total_assets' => round($incomeStatement->total_operating_profit / $sheet->total_assets, 2),
                'equity_total_liabilities' => round($sheet->total_equity / $sheet->total_liabilities, 2),
                'sales_total_assets' => round($incomeStatement->total_sales / $sheet->total_assets, 2),
                'z_score' => round(($data[$sheet->year]['working_capital_total_assets'] * 1.2) + ($data[$sheet->year]['retained_earnings_total_assets'] * 1.4) + ($data[$sheet->year]['ebit_total_assets'] * 3.3) + ($data[$sheet->year]['equity_total_liabilities'] * 0.6) + ($data[$sheet->year]['sales_total_assets'] * 1), 2),
                'z_second_score' => round(($data[$sheet->year]['working_capital_total_assets'] * 6.56) + ($data[$sheet->year]['retained_earnings_total_assets'] * 3.26) + ($data[$sheet->year]['ebit_total_assets'] * 6.72) + ($data[$sheet->year]['equity_total_liabilities'] * 1.05) + ($data[$sheet->year]['sales_total_assets'] * 0), 2),
                'z_score_thresholds_healthy' => 0,
                'current_ratio' => $sheet->total_current_liabilities ? round($sheet->total_current_assets / $sheet->total_current_liabilities, 2) : 2,
                'quick_ratio' => $sheet->total_current_liabilities ? round(($sheet->total_current_assets - $incomeStatement->total_stock) / $sheet->total_current_liabilities, 2) : 2,
                'debtor_days' => round($sheet->total_accounts_receivable / $incomeStatement->total_sales, 2),
                'creditor_days' => round($sheet->total_accounts_payable / $incomeStatement->total_sales, 2),
                'working_capital' => $sheet->total_working_capital ? round($incomeStatement->total_sales / ($sheet->total_working_capital), 2) : 'n/a',
                'turnover_growth' => $turnoverGrowth,
                'gross_profit' => round(($incomeStatement->total_gross_margin / $incomeStatement->total_sales) * 100, 2),
                'operating_profit_margin' => round(($incomeStatement->total_operating_profit / $incomeStatement->total_sales) * 100, 2),
                'net_profit_margin' => round(($incomeStatement->net_profit / $incomeStatement->total_sales) * 100, 2),
                'return_on_equity' => round(($incomeStatement->net_profit / $sheet->total_equity) * 100, 2),
                'return_on_assets' => round(($incomeStatement->net_profit / $sheet->total_assets), 2),
                'return_on_investment' => round(($incomeStatement->total_operating_profit / ($sheet->total_assets + $sheet->total_long_term_liabilities)), 2),
                'gearing_ratio' => round($sheet->total_liabilities / $sheet->total_equity, 2),
                'long_term_debt' => round($sheet->total_long_term_liabilities / $sheet->total_equity, 2),
                'tangible_net_worth' => round($sheet->total_liabilities / $sheet->total_tangible_net_worth, 2),
                'total_assets' => round($sheet->total_equity / $sheet->total_assets, 2),
                'solvency' => $sheet->total_liabilities ? round($sheet->total_assets / $sheet->total_liabilities, 2) : 'n/a',
                'interest_cover' => $incomeStatement->total_net_finance_costs ? round($incomeStatement->total_operating_profit / $incomeStatement->total_net_finance_costs, 2) : 'n/a',
                'gross_interest_debts' => $sheet->total_long_term_liabilities ? round(($incomeStatement->total_cost_of_goods_sold_depreciation + $incomeStatement->total_amortisation) / $sheet->total_long_term_liabilities, 2) : 'n/a',
                'total_assets_turnover' => round($incomeStatement->total_sales / $sheet->total_assets, 2),
                'fixed_assets_turn_over' => round($incomeStatement->total_sales / $sheet->total_fixed_assets, 2),
            ];
        }
        return Inertia::render('Clients/BalanceSheets/Index', [
            'client' => $client,
            'sheets' => $sheets,
            'ratio' => $ratio,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Client $client)
    {
        $currentAssets = ChartOfAccount::whereIn('account_type', ['current_asset', 'cash', 'bank', 'stock'])->get();
        $otherAssets = ChartOfAccount::whereIn('account_type', ['other_asset'])->get();
        $otherCurrentAssets = ChartOfAccount::whereIn('account_type', ['other_current_asset'])->get();
        $fixedAssets = ChartOfAccount::whereIn('account_type', ['fixed_asset', 'non_current_asset', 'property_plant_equipment', 'investment_property', 'right_of_use_asset', 'intangible_asset'])->get();
        $currentLiabilities = ChartOfAccount::whereIn('account_type', ['current_liability', 'income_tax', 'credit_card'])->get();
        $longTermLiabilities = ChartOfAccount::whereIn('account_type', ['long_term_liability', 'other_liability', 'finance_lease_liability', 'other_long_term_liability', 'deferred_income_tax'])->get();
        $equity = ChartOfAccount::whereIn('account_type', ['retained_earning', 'capital_contribution', 'equity', 'reserve'])->get();
        return Inertia::render('Clients/BalanceSheets/Create', [
            'client' => $client,
            'currentAssets' => $currentAssets,
            'otherAssets' => $otherAssets,
            'otherCurrentAssets' => $otherCurrentAssets,
            'fixedAssets' => $fixedAssets,
            'currentLiabilities' => $currentLiabilities,
            'longTermLiabilities' => $longTermLiabilities,
            'equity' => $equity,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'year' => ['required'],
        ]);
        $sheet = new BalanceSheet();
        $sheet->created_by_id = Auth::id();
        $sheet->client_id = $client->id;
        $sheet->year = $request->year;
        $sheet->as_at_date = $request->as_at_date;
        $sheet->total_assets = $request->total_assets;
        $sheet->total_equity = $request->total_equity;
        $sheet->total_liabilities = $request->total_liabilities;
        $sheet->total_equity = $request->total_equity;
        $sheet->total_working_capital = $request->total_working_capital;
        $sheet->total_current_assets = $request->total_current_assets;
        $sheet->total_current_liabilities = $request->total_current_liabilities;
        $sheet->total_long_term_liabilities = $request->total_long_term_liabilities;
        $sheet->total_other_current_assets = $request->total_other_current_assets;
        $sheet->total_other_assets = $request->total_other_assets;
        $sheet->total_fixed_assets = $request->total_fixed_assets;
        $sheet->description = $request->description;
        $sheet->save();
        //save the charts
        foreach ($request->charts['current_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_current_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['fixed_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['current_liabilities'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['long_term_liabilities'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['equity'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }

        activity()
            ->performedOn($client)
            ->log('Create Balance Sheet');
        return redirect()->route('clients.ratio_analysiss.index', [$client->id])->with('success', 'Balance Sheet created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param BalanceSheet $sheet
     * @return \Illuminate\Http\Response
     */
    public function show(BalanceSheet $sheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BalanceSheet $sheet
     * @return \Inertia\Response
     */
    public function edit(BalanceSheet $sheet)
    {
        $client = $sheet->client;
        $currentAssets = ChartOfAccount::whereIn('account_type', ['current_asset', 'cash', 'bank', 'stock'])->get();
        $otherAssets = ChartOfAccount::whereIn('account_type', ['other_asset'])->get();
        $otherCurrentAssets = ChartOfAccount::whereIn('account_type', ['other_current_asset'])->get();
        $fixedAssets = ChartOfAccount::whereIn('account_type', ['fixed_asset', 'non_current_asset', 'property_plant_equipment', 'investment_property', 'right_of_use_asset', 'intangible_asset'])->get();
        $currentLiabilities = ChartOfAccount::whereIn('account_type', ['current_liability', 'income_tax', 'credit_card'])->get();
        $longTermLiabilities = ChartOfAccount::whereIn('account_type', ['long_term_liability', 'other_liability', 'finance_lease_liability', 'other_long_term_liability', 'deferred_income_tax'])->get();
        $equity = ChartOfAccount::whereIn('account_type', ['retained_earning', 'capital_contribution', 'equity', 'reserve'])->get();
        $chartData = [
            'current_assets' => [],
            'other_assets' => [],
            'other_current_assets' => [],
            'fixed_assets' => [],
            'current_liabilities' => [],
            'long_term_liabilities' => [],
            'equity' => [],
        ];
        foreach ($currentAssets as $key) {
            $chartData['current_assets'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($otherCurrentAssets as $key) {
            $chartData['other_current_assets'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($otherAssets as $key) {
            $chartData['other_assets'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($fixedAssets as $key) {
            $chartData['fixed_assets'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($currentLiabilities as $key) {
            $chartData['current_liabilities'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($longTermLiabilities as $key) {
            $chartData['long_term_liabilities'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($equity as $key) {
            $chartData['equity'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];

        }
        $sheet->charts = $chartData;
        return Inertia::render('Clients/BalanceSheets/Edit', [
            'client' => $client,
            'sheet' => $sheet,
            'currentAssets' => $currentAssets,
            'otherAssets' => $otherAssets,
            'otherCurrentAssets' => $otherCurrentAssets,
            'fixedAssets' => $fixedAssets,
            'currentLiabilities' => $currentLiabilities,
            'longTermLiabilities' => $longTermLiabilities,
            'equity' => $equity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param BalanceSheet $sheet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BalanceSheet $sheet)
    {

        $client = $sheet->client;
        $request->validate([
            'year' => ['required'],
        ]);
        $sheet->year = $request->year;
        $sheet->as_at_date = $request->as_at_date;
        $sheet->total_assets = $request->total_assets;
        $sheet->total_equity = $request->total_equity;
        $sheet->total_liabilities = $request->total_liabilities;
        $sheet->total_equity = $request->total_equity;
        $sheet->total_working_capital = $request->total_working_capital;
        $sheet->total_current_assets = $request->total_current_assets;
        $sheet->total_current_liabilities = $request->total_current_liabilities;
        $sheet->total_long_term_liabilities = $request->total_long_term_liabilities;
        $sheet->total_other_current_assets = $request->total_other_current_assets;
        $sheet->total_other_assets = $request->total_other_assets;
        $sheet->total_fixed_assets = $request->total_fixed_assets;
        $sheet->description = $request->description;
        $sheet->save();
        //delete current sheet data
        $sheet->data->each->delete();
        //save the charts
        foreach ($request->charts['current_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_current_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['fixed_assets'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['current_liabilities'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['long_term_liabilities'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['equity'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->ratio_analysis_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        activity()
            ->performedOn($client)
            ->log('Update Balance Sheet');
        return redirect()->route('clients.ratio_analysiss.index', [$client->id])->with('success', 'Balance Sheet updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BalanceSheet $sheet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BalanceSheet $sheet)
    {
        $sheet->data->each->delete();
        $sheet->delete();
        activity()
            ->performedOn($sheet)
            ->log('Delete Balance Sheet');
        return redirect()->route('clients.ratio_analysiss.index', [$sheet->client_id])->with('success', 'Client sheet deleted successfully.');

    }

    public function summary(Client $client)
    {
        $sheets = BalanceSheet::where('client_id', $client->id)
            ->orderBy('year')
            ->groupBy('year')
            ->get();
        return Inertia::render('Clients/BalanceSheets/Summary', [
            'client' => $client,
            'sheets' => $sheets,
        ]);
    }
}
