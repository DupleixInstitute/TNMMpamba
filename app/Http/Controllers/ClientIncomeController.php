<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\IncomeStatement;
use App\Models\IncomeStatementData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientIncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:clients.income_statement.index'])->only(['index', 'show']);
        $this->middleware(['permission:clients.income_statement.create'])->only(['create', 'store']);
        $this->middleware(['permission:clients.income_statement.update'])->only(['edit', 'update']);
        $this->middleware(['permission:clients.income_statement.destroy'])->only(['destroy']);
    }

    public function index(Client $client)
    {
        $statements = IncomeStatement::where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Clients/IncomeStatements/Index', [
            'client' => $client,
            'statements' => $statements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Client $client)
    {
        $sales = ChartOfAccount::where('account_type', 'income')->get();
        $costsOfGoodsSold = ChartOfAccount::where('account_type', 'cost_of_goods_sold')->get();
        $otherExpenses = ChartOfAccount::whereIn('account_type', ['other_expense', 'expense'])->get();
        $otherIncome = ChartOfAccount::whereIn('account_type', ['other_income'])->get();
        $incomeTax = ChartOfAccount::whereIn('account_type', ['income_tax'])->get();
        return Inertia::render('Clients/IncomeStatements/Create', [
            'client' => $client,
            'sales' => $sales,
            'costsOfGoodsSold' => $costsOfGoodsSold,
            'otherExpenses' => $otherExpenses,
            'otherIncome' => $otherIncome,
            'incomeTax' => $incomeTax,
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
            'name' => ['required'],
        ]);
        $statement = new IncomeStatement();
        $statement->created_by_id = Auth::id();
        $statement->client_id = $client->id;
        $statement->year = $request->year;
        $statement->as_at_date = $request->as_at_date;
        $statement->total_operating_expenses = $request->total_operating_expenses;
        $statement->total_gross_margin = $request->total_gross_margin;
        $statement->total_other_income = $request->total_other_income;
        $statement->total_other_expenses = $request->total_other_expenses;
        $statement->total_income_before_tax = $request->total_income_before_tax;
        $statement->net_profit = $request->net_profit;
        $statement->description = $request->description;
        $statement->save();
        //save the charts
        foreach ($request->charts as $key) {
            $statementData = new IncomeStatementData();
            $statementData->client_id = $client->id;
            $statementData->income_statement_id = $statement->id;
            $statementData->chart_of_account_id = $key['chart_of_account_id'];
            $statementData->amount = $key['amount'];
            $statementData->save();
        }
        activity()
            ->performedOn($client)
            ->log('Create Income Statement');
        return redirect()->route('clients.statements.index', [$client->id])->with('success', 'Income Statement created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param IncomeStatement $statement
     * @return \Illuminate\Http\Response
     */
    public function show(IncomeStatement $statement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param IncomeStatement $statement
     * @return \Inertia\Response
     */
    public function edit(IncomeStatement $statement)
    {
        $client = $statement->client;

        return Inertia::render('Clients/IncomeStatements/Edit', [
            'client' => $client,
            'statement' => $statement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param IncomeStatement $statement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, IncomeStatement $statement)
    {

        $client = $statement->client;
        $request->validate([
            'name' => ['required'],
        ]);
        $statement->name = $request->name;
        $statement->gender = $request->gender;
        $statement->itc_date = $request->itc_date;
        $statement->dob = $request->dob;
        $statement->shares = $request->shares;
        $statement->itc_ref_no = $request->itc_ref_no;
        $statement->itc_ref_date = $request->itc_ref_date;
        $statement->number_of_paid_debts = $request->number_of_paid_debts;
        $statement->number_of_defaulted_debts = $request->number_of_defaulted_debts;
        $statement->number_of_judgements = $request->number_of_judgements;
        $statement->number_of_trace_alerts = $request->number_of_trace_alerts;
        $statement->blacklisted = $request->blacklisted ? 1 : 0;
        $statement->fraud_alert = $request->fraud_alert ? 1 : 0;
        $statement->description = $request->description;
        $statement->save();

        activity()
            ->performedOn($client)
            ->log('Update Income Statement');
        return redirect()->route('clients.statements.index', [$client->id])->with('success', 'Income Statement updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param IncomeStatement $statement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(IncomeStatement $statement)
    {
        $statement->data->each->delete();
        $statement->delete();
        activity()
            ->performedOn($statement)
            ->log('Delete Income Statement');
        return redirect()->route('clients.statements.index', [$statement->client_id])->with('success', 'Client deleted successfully.');

    }
}
