<?php

namespace App\Http\Controllers;

use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Models\LoanApplication;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LoanProduct;
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
        $this->middleware(['permission:applications.applications.index'])->only(['index', 'show']);
        $this->middleware(['permission:applications.applications.create'])->only(['create', 'store']);
        $this->middleware(['permission:applications.applications.update'])->only(['edit', 'update']);
        $this->middleware(['permission:applications.applications.destroy'])->only(['destroy']);
        $this->middleware(['permission:applications.applications.approve'])->only(['changeStatus']);
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
        $applications = LoanApplication::with(['staff', 'client', 'category'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('LoanApplications/Index', [
            'filters' => \request()->all('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'),
            'applications' => $applications,
            'products' => LoanProduct::get()->map(function ($item) {
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
        return Inertia::render('LoanApplications/Create', [
            'products' => LoanProductCategory::get()->map(function ($item) {
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
            'client_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
        ]);
        $client = Client::find($request->client_id);
        $application = new LoanApplication();
        $application->created_by_id = Auth::id();
        $application->application_category_id = $request->application_category_id;
        $application->currency_id = Setting::where('setting_key', 'currency')->first()->setting_value;
        $application->client_id = $client->id;
        $application->province_id = $client->province_id;
        $application->branch_id = $client->branch_id;
        $application->district_id = $client->district_id;
        $application->ward_id = $client->ward_id;
        $application->village_id = $client->village_id;
        $application->staff_id = $request->staff_id;
        $application->date = $request->date;
        $application->amount = $request->amount;
        $application->applied_amount = $request->amount;
        $application->description = $request->description;
        $application->save();
        event(new LoanCreated($application));
        activity()
            ->performedOn($application)
            ->log('Create Loan');
        return redirect()->route('applications.show', $application->id)->with('success', 'Loan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param LoanApplication $application
     * @return \Inertia\Response
     */
    public function show(LoanApplication $application)
    {
        $application->load(['client','client.province', 'client.branch','client.district','client.ward','client.village','category','staff']);
        return Inertia::render('LoanApplications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LoanApplication $application
     * @return \Inertia\Response
     */
    public function edit(LoanApplication $application)
    {
        return Inertia::render('LoanApplications/Edit', [
            'application' => $application,
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
     * @param LoanApplication $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LoanApplication $application)
    {
        $request->validate([
            'amount' => ['required'],
            'date' => ['required'],
        ]);
        $application->application_category_id = $request->application_category_id;
        $application->staff_id = $request->staff_id;
        $application->date = $request->date;
        $application->amount = $request->amount;
        $application->description = $request->description;
        $application->save();
        return redirect()->route('applications.show', $application->id)->with('success', 'Loan updated successfully.');
    }

    public function changeStatus(Request $request, LoanApplication $application)
    {
        $application->status = $request->status;
        $application->approved_by_id = Auth::id();
        if ($application->isDirty('status')) {
            if($application->status=='approved'){
                $application->approved_date=$request->approved_date??date('Y-m-d');
                $application->amount=$request->amount??$application->applied_amount;
            }
            if($application->status=='rejected'){
                $application->approved_date=$request->approved_date??date('Y-m-d');
            }
        }
        $application->save();
        activity()
            ->performedOn($application)
            ->log('Change Loan status');
        if ($application->wasChanged('status')) {
            event(new LoanStatusChanged($application));
        }
        return redirect()->route('applications.show', $application->id)->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LoanApplication $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoanApplication $application)
    {
        $application->delete();
        activity()
            ->performedOn($application)
            ->log('Delete Loan');
        return redirect()->route('applications.index')->with('success', 'Loan deleted successfully.');
    }
}
