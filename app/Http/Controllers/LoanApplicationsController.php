<?php

namespace App\Http\Controllers;

use App\Events\LoanApplicationCreated;
use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Models\LoanApplication;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LoanApplicationScore;
use App\Models\LoanProduct;
use App\Models\LoanProductCategory;
use App\Models\Client;
use App\Models\LoanProductScoringAttribute;
use App\Models\LoanProductScoringAttributeOptionValue;
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
        $this->middleware(['permission:loans.applications.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.applications.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.applications.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.applications.destroy'])->only(['destroy']);
        $this->middleware(['permission:loans.applications.approve'])->only(['changeStatus']);
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
        $applications = LoanApplication::with(['staff', 'client', 'product'])
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
        $products = LoanProduct::with(['attributes', 'attributes.attribute', 'attributes.options'])->get();
        $products->each(function (&$product) {
            $groups = LoanProductScoringAttribute::where('is_group', 1)->where('loan_product_id', $product->id)->get();
            $groups->transform(function ($group) use ($product) {
                $attributes = LoanProductScoringAttribute::with(['attribute'])->where('is_group', 0)->where('scoring_attribute_group_id', $group->scoring_attribute_group_id)->where('loan_product_id', $product->id)->orderBy('order_position')->get();
                $attributes->transform(function ($item) {
                    if (!empty($item->attribute)) {
                        if ($item->attribute->field_type === 'dropdown' || $item->attribute->field_type === 'radio' || $item->attribute->field_type === 'checkbox') {
                            $options = json_decode($item->attribute->options);
                            $optionsArray = [];
                            foreach ($options as $option) {
                                if ($opt = LoanProductScoringAttributeOptionValue::where('loan_product_scoring_attribute_id', $item->id)->where('name', $option)->first()) {
                                    $optionsArray[] = $opt;
                                } else {
                                    $optionsArray[] = [
                                        'id' => '',
                                        'loan_product_scoring_attribute_id' => '',
                                        'scoring_attribute_id' => $item->id,
                                        'name' => $option,
                                        'weight' => '',
                                        'score' => '',
                                        'effective_weight' => '',
                                        'weighted_score' => '',
                                        'description' => '',
                                        'active' => true,
                                    ];
                                }
                            }
                            $item->attribute->options = $optionsArray;
                        }
                    }
                    $item->value = '';
                    return $item;
                });
                $group->attributes = $attributes;
                return $group;
            });
            $product->scoring_attributes = $groups;
        });
        return Inertia::render('LoanApplications/Create', [
            'products' => $products,
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

        $attributes = $request->json('attributes');

        $client = Client::find($request->client_id);
        dump($client);
        dump($attributes);
        dd($request);
        $totalScore = 0;
        $application = new LoanApplication();
        $application->created_by_id = Auth::id();
        $application->loan_product_id = $request->loan_product_id;
        $application->currency_id = Setting::where('setting_key', 'currency')->first()->setting_value;
        $application->client_id = $client->id;
        $application->branch_id = $client->branch_id;
        $application->staff_id = $request->staff_id;
        $application->date = $request->date;
        $application->amount = $request->amount;
        $application->applied_amount = $request->amount;
        $application->description = $request->description;
        $application->save();
        //save scores
        foreach ($attributes as $group) {
            foreach ($group['attributes'] as $field) {
                $score = new LoanApplicationScore();
                $score->created_by_id = Auth::id();
                $score->loan_application_id = $application->id;
                $score->scoring_attribute_id = $field['scoring_attribute_id'];
                $score->loan_product_scoring_attribute_id = $field['id'];
            }
        }
        event(new LoanApplicationCreated($application));
        activity()
            ->performedOn($application)
            ->log('Create Loan');
        return redirect()->route('loan_applications.show', $application->id)->with('success', 'Loan application created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param LoanApplication $application
     * @return \Inertia\Response
     */
    public function show(LoanApplication $application)
    {
        $application->load(['staff', 'client', 'product']);
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
            if ($application->status == 'approved') {
                $application->approved_date = $request->approved_date ?? date('Y-m-d');
                $application->amount = $request->amount ?? $application->applied_amount;
            }
            if ($application->status == 'rejected') {
                $application->approved_date = $request->approved_date ?? date('Y-m-d');
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
