<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ExpenseTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:expenses.types.index'])->only(['index', 'show']);
        $this->middleware(['permission:expenses.types.create'])->only(['create', 'store']);
        $this->middleware(['permission:expenses.types.update'])->only(['edit', 'update']);
        $this->middleware(['permission:expenses.types.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $expenseTypes = ExpenseType::with(['assetChart', 'expenseChart'])
            ->filter(\request()->only('search'))
            ->paginate(20);
        return Inertia::render('ExpenseTypes/Index', [
            'filters' => \request()->all('search'),
            'expenseTypes' => $expenseTypes,
        ]);

    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

        return Inertia::render('ExpenseTypes/Create', [
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $expenseType = new ExpenseType();
        $expenseType->name = $request->name;
        $expenseType->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expenseType->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expenseType->save();
        activity()->on($expenseType)
            ->withProperties(['id' => $expenseType->id])
            ->log('Create Expense Type');
        return redirect()->route('expenses.types.index')->with('success', 'Expense Type saved successfully.');

    }

    /**
     * Show the specified resource.
     * @param ExpenseType $expenseType
     * @return Response
     */
    public function show(ExpenseType $expenseType)
    {
        return Inertia::render('ExpenseTypes/Show', [
            'expenseType' => $expenseType,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit(ExpenseType $expenseType)
    {
        return Inertia::render('ExpenseTypes/Edit', [
            'expenseType' => $expenseType,
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $expenseType->name = $request->name;
        $expenseType->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expenseType->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expenseType->save();
        activity()->on($expenseType)
            ->withProperties(['id' => $expenseType->id])
            ->log('Update Expense Type');
        return redirect()->route('expenses.types.index')->with('success', 'Expense Type updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     * @param ExpenseType $expenseType
     * @return Response
     */
    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        activity()->on($expenseType)
            ->withProperties(['id' => $expenseType->id])
            ->log('Delete Expense Type');
        return redirect()->route('expenses.types.index')->with('success', 'Expense Type deleted successfully.');

    }
}
