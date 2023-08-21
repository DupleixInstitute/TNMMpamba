<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\JournalEntry;
use App\Models\PaymentDetail;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:expenses.index'])->only(['index', 'show', 'get_expenses']);
        $this->middleware(['permission:expenses.create'])->only(['create', 'store']);
        $this->middleware(['permission:expenses.update'])->only(['edit', 'update']);
        $this->middleware(['permission:expenses.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $expenses = Expense::with(['assetChart', 'expenseChart', 'type'])
            ->filter(\request()->only('search', 'expense_type_id', 'currency_id', 'branch_id','date_range'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Expenses/Index', [
            'filters' => \request()->all('search', 'expense_type_id', 'currency_id', 'branch_id','date_range'),
            'expenses' => $expenses,
            'expenseTypes' => ExpenseType::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'expense_chart_of_account_id' => $item->expense_chart_of_account_id,
                    'asset_chart_of_account_id' => $item->asset_chart_of_account_id,
                ];
            }),
            'currencies' => Currency::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Expenses/Create', [
            'assets' => ChartOfAccount::where('account_type', 'asset')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'expenses' => ChartOfAccount::where('account_type', 'expense')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'currencies' => Currency::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'expenseTypes' => ExpenseType::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'expense_chart_of_account_id' => $item->expense_chart_of_account_id,
                    'asset_chart_of_account_id' => $item->asset_chart_of_account_id,
                ];
            }),
            'invoiceAllowEditingExchangeRate' => Setting::where('setting_key', 'invoice_edit_exchange_rate')->first()->setting_value,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
        ]);
        $invoiceAllowEditingExchangeRate = Setting::where('setting_key', 'invoice_edit_exchange_rate')->first()->setting_value;
        $currency = Currency::find($request->currency_id);
        $expense = new Expense();
        $expense->created_by_id = Auth::id();
        $expense->expense_type_id = $request->expense_type_id;
        $expense->currency_id = $request->currency_id;
        //set xrate
        if ($invoiceAllowEditingExchangeRate === 'yes') {
            $expense->xrate = $request->xrate ?? $currency->xrate;
        } else {
            $expense->xrate = $currency->xrate;
        }
        $expense->branch_id = $request->branch_id;
        $expense->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense->amount = $request->amount;
        $expense->date = $request->date;
        $expense->recurring = $request->recurring ? 1 : 0;
        if ($request->recurring) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            $expense->recur_end_date = $request->recur_end_date;
            $expense->recur_type = $request->recur_type;
            $expense->selected_days = $request->selected_days;
        }
        $expense->description = $request->description;
        $expense->save();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'expense';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->expense_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->expense_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $journal_entry->debit = $request->amount;
            $journal_entry->xrate = $expense->xrate;
            $journal_entry->reference = $expense->id;
            $journal_entry->description = $request->description;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $journal_entry->credit = $request->amount;
            $journal_entry->xrate = $expense->xrate;
            $journal_entry->reference = $expense->id;
            $journal_entry->description = $request->description;
            $journal_entry->save();
        }
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Create Expense');
        return redirect()->route('expenses.index')->with('success', 'Expense saved successfully.');

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Expense $expense)
    {
        $expense->load(['assetChart', 'expenseChart', 'type', 'currency']);
        return Inertia::render('Expenses/Show', [
            'expense' => $expense,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit(Expense $expense)
    {
        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'assets' => ChartOfAccount::where('account_type', 'asset')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'expenses' => ChartOfAccount::where('account_type', 'expense')->get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'currencies' => Currency::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'expenseTypes' => ExpenseType::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'invoiceAllowEditingExchangeRate' => Setting::where('setting_key', 'invoice_edit_exchange_rate')->first()->setting_value,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Expense $expense
     * @return Response
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
        ]);
        $invoiceAllowEditingExchangeRate = Setting::where('setting_key', 'invoice_edit_exchange_rate')->first()->setting_value;
        $currency = Currency::find($request->currency_id);
        $expense->expense_type_id = $request->expense_type_id;
        $expense->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense->branch_id = $request->branch_id;
        $expense->currency_id = $request->currency_id;
        //set xrate
        if ($invoiceAllowEditingExchangeRate === 'yes') {
            $expense->xrate = $request->xrate ?? $currency->xrate;
        } else {
            $expense->xrate = $currency->xrate;
        }
        $expense->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense->amount = $request->amount;
        $expense->date = $request->date;
        $expense->recurring = $request->recurring ? 1 : 0;
        if ($request->recurring) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            $expense->recur_end_date = $request->recur_end_date;
            $expense->recur_type = $request->recur_type;
            $expense->selected_days = $request->selected_days;
        }
        $expense->description = $request->description;
        $expense->save();
        JournalEntry::where('transaction_number', $expense->id)->where('transaction_type', 'expense')->delete();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'expense';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->expense_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->expense_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $journal_entry->debit = $request->amount;
            $journal_entry->xrate = $expense->xrate;
            $journal_entry->reference = $expense->id;
            $journal_entry->description = $request->description;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $journal_entry->credit = $request->amount;
            $journal_entry->xrate = $expense->xrate;
            $journal_entry->reference = $expense->id;
            $journal_entry->description = $request->description;
            $journal_entry->save();
        }
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Update Expense');
        return redirect()->route('expenses.index')->with('success', 'Expense  updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        JournalEntry::where('transaction_number', $expense->id)->where('transaction_type', 'expense')->delete();
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Delete Expense');
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');

    }
}
