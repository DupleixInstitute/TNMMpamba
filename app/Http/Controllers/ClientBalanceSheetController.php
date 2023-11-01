<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\BalanceSheet;
use App\Models\BalanceSheetData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientBalanceSheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:clients.balance_sheet.index'])->only(['index', 'show']);
        $this->middleware(['permission:clients.balance_sheet.create'])->only(['create', 'store']);
        $this->middleware(['permission:clients.balance_sheet.update'])->only(['edit', 'update']);
        $this->middleware(['permission:clients.balance_sheet.destroy'])->only(['destroy']);
    }

    public function index(Client $client)
    {
        $sheets = BalanceSheet::where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Clients/BalanceSheets/Index', [
            'client' => $client,
            'sheets' => $sheets,
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
        $fixedAssets = ChartOfAccount::whereIn('account_type', ['fixed_asset', 'non_current_asset'])->get();
        $currentLiabilities = ChartOfAccount::whereIn('account_type', ['current_liability', 'income_tax', 'credit_card'])->get();
        $longTermLiabilities = ChartOfAccount::whereIn('account_type', ['long_term_liability', 'other_liability'])->get();
        $equity = ChartOfAccount::whereIn('account_type', ['equity'])->get();
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
        $sheet->total_sales = $request->total_sales;
        $sheet->total_operating_expenses = $request->total_operating_expenses;
        $sheet->total_gross_margin = $request->total_gross_margin;
        $sheet->total_other_income = $request->total_other_income;
        $sheet->total_other_expenses = $request->total_other_expenses;
        $sheet->total_income_before_tax = $request->total_income_before_tax;
        $sheet->total_income_tax = $request->total_income_tax;
        $sheet->total_cost_of_goods_sold = $request->total_cost_of_goods_sold;
        $sheet->total_operating_profit = $request->total_operating_profit;
        $sheet->net_profit = $request->net_profit;
        $sheet->description = $request->description;
        $sheet->save();
        //save the charts
        foreach ($request->charts['sales'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['cost_of_goods_sold'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['operating_expenses'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_income'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_expenses'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['income_tax'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }

        activity()
            ->performedOn($client)
            ->log('Create Balance Sheet');
        return redirect()->route('clients.balance_sheets.index', [$client->id])->with('success', 'Balance Sheet created successfully.');

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

        $sales = ChartOfAccount::where('account_type', 'income')->get();
        $costsOfGoodsSold = ChartOfAccount::where('account_type', 'cost_of_goods_sold')->get();
        $expenses = ChartOfAccount::whereIn('account_type', ['expense'])->get();
        $otherExpenses = ChartOfAccount::whereIn('account_type', ['other_expense'])->get();
        $otherIncome = ChartOfAccount::whereIn('account_type', ['other_income'])->get();
        $incomeTax = ChartOfAccount::whereIn('account_type', ['income_tax'])->get();
        $chartData = [
            'sales' => [],
            'cost_of_goods_sold' => [],
            'operating_expenses' => [],
            'other_income' => [],
            'other_expenses' => [],
            'income_tax' => [],
        ];
        foreach ($sales as $key) {
            $chartData['sales'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($costsOfGoodsSold as $key) {
            $chartData['cost_of_goods_sold'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($expenses as $key) {
            $chartData['operating_expenses'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($otherExpenses as $key) {
            $chartData['other_expenses'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($otherIncome as $key) {
            $chartData['other_income'][] = [
                'name' => $key->name,
                'chart_of_account_id' => $key->id,
                'id' => $sheet->data->where('chart_of_account_id', $key->id)->first()->id ?? '',
                'amount' => $sheet->data->where('chart_of_account_id', $key->id)->first()->amount ?? '',
            ];
        }
        foreach ($incomeTax as $key) {
            $chartData['income_tax'][] = [
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
            'sales' => $sales,
            'costsOfGoodsSold' => $costsOfGoodsSold,
            'otherExpenses' => $otherExpenses,
            'expenses' => $expenses,
            'otherIncome' => $otherIncome,
            'incomeTax' => $incomeTax,
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
        $sheet->total_sales = $request->total_sales;
        $sheet->total_operating_expenses = $request->total_operating_expenses;
        $sheet->total_gross_margin = $request->total_gross_margin;
        $sheet->total_other_income = $request->total_other_income;
        $sheet->total_other_expenses = $request->total_other_expenses;
        $sheet->total_income_before_tax = $request->total_income_before_tax;
        $sheet->total_income_tax = $request->total_income_tax;
        $sheet->total_cost_of_goods_sold = $request->total_cost_of_goods_sold;
        $sheet->total_operating_profit = $request->total_operating_profit;
        $sheet->net_profit = $request->net_profit;
        $sheet->description = $request->description;
        $sheet->save();
        //delete current linked data
        $sheet->data->each->delete();
        //save the charts
        foreach ($request->charts['sales'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['cost_of_goods_sold'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['operating_expenses'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_income'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['other_expenses'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        foreach ($request->charts['income_tax'] as $item) {
            $sheetData = new BalanceSheetData();
            $sheetData->client_id = $client->id;
            $sheetData->balance_sheet_id = $sheet->id;
            $sheetData->chart_of_account_id = $item['chart_of_account_id'];
            $sheetData->name = $item['name'];
            $sheetData->amount = $item['amount'];
            $sheetData->save();
        }
        activity()
            ->performedOn($client)
            ->log('Update Balance Sheet');
        return redirect()->route('clients.balance_sheets.index', [$client->id])->with('success', 'Balance Sheet updated successfully.');

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
        return redirect()->route('clients.balance_sheets.index', [$sheet->client_id])->with('success', 'Client sheet deleted successfully.');

    }
}
