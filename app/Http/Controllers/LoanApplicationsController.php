<?php

namespace App\Http\Controllers;

use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Models\LoanApplication;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LoanProductCategory;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Tariff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class LoanApplicationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loans.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.destroy'])->only(['destroy']);
        $this->middleware(['permission:loans.approve'])->only(['changeStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $staffID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('employee') && Auth::user()->hasPermissionTo('loans.view_assigned_loans_only')) {
            $staffID = Auth::id();
        }
        $loans = LoanApplication::with(['staff', 'member', 'category'])
            ->filter(\request()->only('search', 'member_id', 'loan_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->staff($staffID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Loans/Index', [
            'filters' => \request()->all('search', 'member_id', 'loan_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'),
            'loans' => $loans,
            'categories' => LoanProductCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Loans/Create', [
            'categories' => LoanProductCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
        ]);
        $member = Client::find($request->member_id);
        $loan = new LoanApplication();
        $loan->created_by_id = Auth::id();
        $loan->loan_category_id = $request->loan_category_id;
        $loan->currency_id = Setting::where('setting_key', 'currency')->first()->setting_value;
        $loan->member_id = $member->id;
        $loan->province_id = $member->province_id;
        $loan->branch_id = $member->branch_id;
        $loan->district_id = $member->district_id;
        $loan->ward_id = $member->ward_id;
        $loan->village_id = $member->village_id;
        $loan->staff_id = $request->staff_id;
        $loan->date = $request->date;
        $loan->amount = $request->amount;
        $loan->applied_amount = $request->amount;
        $loan->description = $request->description;
        $loan->save();
        event(new LoanCreated($loan));
        activity()
            ->performedOn($loan)
            ->log('Create Loan');
        return redirect()->route('loans.show', $loan->id)->with('success', 'Loan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param LoanApplication $loan
     * @return \Inertia\Response
     */
    public function show(LoanApplication $loan)
    {
        $loan->load(['member','member.province', 'member.branch','member.district','member.ward','member.village','category','staff']);
        return Inertia::render('Loans/Show', [
            'loan' => $loan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LoanApplication $loan
     * @return \Inertia\Response
     */
    public function edit(LoanApplication $loan)
    {
        return Inertia::render('Loans/Edit', [
            'loan' => $loan,
            'categories' => LoanProductCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LoanApplication $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LoanApplication $loan)
    {
        $request->validate([
            'amount' => ['required'],
            'date' => ['required'],
        ]);
        $loan->loan_category_id = $request->loan_category_id;
        $loan->staff_id = $request->staff_id;
        $loan->date = $request->date;
        $loan->amount = $request->amount;
        $loan->description = $request->description;
        $loan->save();
        return redirect()->route('loans.show', $loan->id)->with('success', 'Loan updated successfully.');
    }

    public function changeStatus(Request $request, LoanApplication $loan)
    {
        $loan->status = $request->status;
        $loan->approved_by_id = Auth::id();
        if ($loan->isDirty('status')) {
            if($loan->status=='approved'){
                $loan->approved_date=$request->approved_date??date('Y-m-d');
                $loan->amount=$request->amount??$loan->applied_amount;
            }
            if($loan->status=='rejected'){
                $loan->approved_date=$request->approved_date??date('Y-m-d');
            }
        }
        $loan->save();
        activity()
            ->performedOn($loan)
            ->log('Change Loan status');
        if ($loan->wasChanged('status')) {
            event(new LoanStatusChanged($loan));
        }
        return redirect()->route('loans.show', $loan->id)->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LoanApplication $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoanApplication $loan)
    {
        $loan->delete();
        activity()
            ->performedOn($loan)
            ->log('Delete Loan');
        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }
}
