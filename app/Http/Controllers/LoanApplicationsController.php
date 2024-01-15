<?php

namespace App\Http\Controllers;

use App\Events\LoanApplicationCreated;
use App\Events\LoanApplicationStatusChanged;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Webit\Util\EvalMath\EvalMath;

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
        $products = LoanProduct::with(['scoringAttributes', 'scoringAttributes.attribute', 'scoringAttributes.options'])->get();

        $products->each(function (&$product) {
            $groups = LoanProductScoringAttribute::where('is_group', 1)->where('loan_product_id', $product->id)->get();
            $groups->transform(function ($group) use ($product) {
                $attributes = LoanProductScoringAttribute::with(['attribute'])->where('is_group', 0)->where('scoring_attribute_group_id', $group->scoring_attribute_group_id)->where('loan_product_id', $product->id)->orderBy('order_position')->get();
                $attributes->transform(function ($item) {
                    if (!empty($item->attribute)) {
                        $item->attribute->options = $item->options ?: [];
                        if ($item->attribute->field_type === 'checkbox') {
                            $item->value = [];
                        } else {
                            $item->value = '';
                        }
                    }

                    return $item;
                });
                $group->attributes = $attributes;
                return $group;
            });
            $product->form_attributes = $groups;
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
        $product = LoanProduct::find($request->loan_product_id);
        $client->load(['shareholders', 'industryType', 'ratio']);
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
                $score->weight = $field['weight'];
                $score->effective_weight = $field['effective_weight'];
                $score->weighted_score = $field['weighted_score'];
                if ($field['attribute']['field_type'] === 'checkbox') {
                    $score->value = json_encode($field['value']);
                } else {
                    $score->value = $field['value'];
                }

                //determine the score
                if ($field['attribute']['field_type'] === 'dropdown' || $field['attribute']['field_type'] === 'radio') {
                    foreach ($field['attribute']['options'] as $key) {
                        if ($key['name'] == $field['value']) {
                            $score->score = $key['score'];
                            //check if score is accepted
                            if (in_array($key['name'], $field['accept_value'])) {
                                $score->accepted = 1;
                            }
                        }
                    }
                } elseif ($field['attribute']['field_type'] === 'checkbox') {
                    $tempScore = 0;
                    $tempAccepted = 0;
                    foreach ($field['value'] as $item) {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($key['name'] == $item) {
                                $tempScore += $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $tempAccepted = 1;
                                }
                            }
                        }
                    }
                    $score->score = $tempScore;
                    $score->accepted = $tempAccepted;
                } elseif ($field['attribute']['field_type'] === 'text' || $field['attribute']['field_type'] === 'number') {
                    if ($field['option_type'] === 'range') {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($field['value'] >= $key['lower_value'] && $field['value'] <= $key['upper_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                        }
                    }
                    if ($field['option_type'] === 'greater_than_or_less_than') {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($key['name'] === 'Greater Than or Equal To' && $field['value'] >= $field['median_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                            if ($key['name'] === 'Less Than' && $field['value'] < $field['median_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                        }
                    }
                } else {
                    $score->score = $field['score'];
                }
                $score->save();
            }
        }
        //let's update fields of type formula
        $formulaAttributes = LoanApplicationScore::with(['productAttribute'])
            ->where('loan_application_id', $application->id)
            ->whereHas('scoringAttribute', function (Builder $query) {
                $query->where('field_type', 'formula');
            })->get();
        foreach ($formulaAttributes as $attribute) {
            $formula = $attribute->productAttribute->data;
            preg_match_all(
                '/\{\{field_(\d)+}}/',
                $formula,
                $matches,
                PREG_PATTERN_ORDER
            );
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    //find the value of that field
                    $score = LoanApplicationScore::where('loan_product_scoring_attribute_id', explode('_', $match)[1])->where('loan_application_id', $application->id)->first();
                    if (empty($score)) {
                        //throw error
                    }
                    $formula = str_replace($match, $score->value, $formula);
                }
                //at this point our formula is ready
                $math = new EvalMath;
                $value = $math->evaluate($formula);
                $attribute->value = round($value, 2);
            }
            //update the score now that we have a value
            $options = LoanProductScoringAttributeOptionValue::where('loan_product_id', $product->id)->where('loan_product_scoring_attribute_id', $attribute->productAttribute->id)->get();
            if ($attribute->productAttribute->option_type === 'range') {
                foreach ($options as $key) {
                    if ($attribute->value >= $key->lower_value && $attribute->value <= $key->upper_value) {
                        $attribute->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                }
            }
            if ($attribute->productAttribute->option_type === 'greater_than_or_less_than') {
                foreach ($options as $key) {
                    if ($key->name === 'Greater Than or Equal To' && $attribute->value >= $attribute->productAttribute->median_value) {
                        $score->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                    if ($key->name === 'Less Than' && $attribute->value < $attribute->productAttribute->median_value) {
                        $attribute->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                }
            }
            $attribute->save();
        }
        //let's update fields of type calculated
        $formulaAttributes = LoanApplicationScore::with(['productAttribute'])
            ->where('loan_application_id', $application->id)
            ->whereHas('scoringAttribute', function (Builder $query) {
                $query->where('field_type', 'calculated');
            })->get();
        $industryType = $client->industryType;
        $ratio = $client->ratio;
        $shareholders = $client->shareholders;
        $porter = $client->porter;
        foreach ($formulaAttributes as $attribute) {

            $ratioScore = 0.00;
            $accepted = 0;
            $maxScore = $attribute->productAttribute->score;
            $minScore = $attribute->productAttribute->min_score;
            //update is ratio
            if ($attribute->productAttribute->is_ratio) {
                $ratioValue = $ratio->{$attribute->productAttribute->system_name};
                $ratioBenchmarkValue = $industryType->{$attribute->productAttribute->system_name};
                $ratioScore = $ratioBenchmarkValue ? ($ratioValue * $maxScore / $ratioBenchmarkValue) : 0;
                if ($ratioScore > $maxScore) {
                    $ratioScore = $maxScore;
                }
                if ($ratioScore < 0) {
                    $ratioScore = 0.00;
                }
            }
            if ($attribute->productAttribute->is_shareholder_analysis) {
                if ($attribute->productAttribute->system_name !== 'blacklisted' && $attribute->productAttribute->system_name !== 'fraud_alert') {
                    $average = round($client->shareholders->average($attribute->productAttribute->system_name));
                    switch ($average) {
                        case 0:
                            $ratioScore = $maxScore;
                            $accepted = 1;
                            break;
                        case 1:
                            $ratioScore = $maxScore * 40 / 100;
                            $accepted = 1;
                            break;
                        case 2:
                            $ratioScore = $maxScore * 20 / 100;
                            $accepted = 1;
                            break;
                        default:
                            $ratioScore = 0;
                            $accepted = 0;
                    }
                } else {
                    if ($shareholders->where($attribute->productAttribute->system_name, 1)->count() > 0) {
                        $attribute->value = 'yes';
                        $ratioScore = 0;
                        $accepted = 0;
                    } else {
                        $attribute->value = 'no';
                        $ratioScore = $maxScore;
                        $accepted = 1;
                    }
                }

            }
            if ($attribute->productAttribute->is_industry_analysis) {
                if ($attribute->productAttribute->system_name === 'porters_five_forces_analysis') {
                    $porter = $porter->grand_total;
                    if ($porter->grand_total < 0) {
                        $ratioScore = 0;
                        $accepted = 0;
                    }
                    if ($porter->grand_total == 0) {
                        $ratioScore = $maxScore * 50 / 100;
                        $accepted = 1;
                    }
                    if ($porter->grand_total > 0) {
                        $ratioScore = $maxScore;
                        $accepted = 1;
                    }
                }
            }
            $attribute->score = $ratioScore;
            $attribute->accepted = $accepted;
            $attribute->save();
        }
        $application->score = LoanApplicationScore::where('loan_application_id', $application->id)->sum('score');
        $application->score_percentage = $application->score * 100 / $product->score;
        $application->save();
        event(new LoanApplicationCreated($application));
        activity()
            ->performedOn($application)
            ->log('Create Loan Application');
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
        $application->load(['staff', 'client', 'client.shareholders', 'client.industryType', 'client.ratio', 'product']);
        $error = '';
        $message = '';
        if ($application->client->type === 'corporate') {
            if ($application->client->shareholders->where('blacklisted', 1)->count() > 0) {
                $error = 'One of the shareholders is blacklisted, this application should not be approved.<br>';
            }
            if ($application->client->shareholders->where('fraud_alert', 1)->count() > 0) {
                $error .= 'One of the shareholders has a fraud alert, this application should not be approved.';
            }
        }
        $groups = LoanProductScoringAttribute::where('is_group', 1)->where('loan_product_id', $application->product->id)->get();
        $groups->transform(function ($group) use ($application) {
            $attributes = LoanProductScoringAttribute::with(['attribute'])->where('is_group', 0)->where('scoring_attribute_group_id', $group->scoring_attribute_group_id)->where('loan_product_id', $application->product->id)->orderBy('order_position')->get();
            $group->max_total_score = (float)$attributes->sum('score');
            $groupTotalScore = 0;
            $attributes->transform(function ($item) use ($application, &$groupTotalScore) {
                if (!empty($item->attribute)) {
                    $item->attribute->options = $item->options ?: [];
                }
                //get value
                $score = LoanApplicationScore::where('loan_application_id', $application->id)->where('loan_product_scoring_attribute_id', $item->id)->first();
                if (!empty($score)) {
                    if ($item->attribute->field_type === 'checkbox') {
                        $item->value = json_decode($score->value);
                    } else {
                        $item->value = $score->value;
                    }

                    $item->accepted = $score->accepted;
                    $item->actual_score = $score->score;
                    $item->percentage_score = $item->score>0 ? round($score->score * 100 / $item->score, 2) : 0;
                    $groupTotalScore = $groupTotalScore + $score->score;
                } else {
                    if ($item->attribute->field_type === 'checkbox') {
                        $item->value = [];
                    } else {
                        $item->value = '';
                    }
                    $item->accepted = false;
                    $item->percentage_score = 0;
                    $item->actual_score = 0;
                }
                return $item;
            });
            $group->total_score = $groupTotalScore;
            $group->total_percentage =  $group->max_total_score>0 ? round($group->total_score * 100 /  $group->max_total_score, 2) : 0;
            $group->attributes = $attributes;
            return $group;
        });
        if ($error) {
            session()->flash('error', $error);
        }
        if ($message) {
            session()->flash('success', $message);
        }
        $application->product->scoring_attributes = $groups;
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
        $application->load(['staff', 'client', 'product']);
        $groups = LoanProductScoringAttribute::where('is_group', 1)->where('loan_product_id', $application->product->id)->get();
        $groups->transform(function ($group) use ($application) {
            $attributes = LoanProductScoringAttribute::with(['attribute'])->where('is_group', 0)->where('scoring_attribute_group_id', $group->scoring_attribute_group_id)->where('loan_product_id', $application->product->id)->orderBy('order_position')->get();
            $attributes->transform(function ($item) use ($application) {
                if (!empty($item->attribute)) {
                    $item->attribute->options = $item->options ?: [];
                }
                //get value
                $score = LoanApplicationScore::where('loan_application_id', $application->id)->where('loan_product_scoring_attribute_id', $item->id)->first();
                if (!empty($score)) {
                    if ($item->attribute->field_type === 'checkbox') {
                        $item->value = $score->value ? json_decode($score->value) : [];
                    } else {
                        $item->value = $score->value;
                    }
                } else {
                    if ($item->attribute->field_type === 'checkbox') {
                        $item->value = [];
                    } else {
                        $item->value = '';
                    }
                }
                return $item;
            });
            $group->attributes = $attributes;
            return $group;
        });
        $application->product->form_attributes = $groups;
        return Inertia::render('LoanApplications/Edit', [
            'application' => $application,
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
        $attributes = $request->json('attributes');
        $client = $application->client;
        $client->load(['shareholders', 'industryType', 'ratio']);
        $product = $application->product;
        $application->staff_id = $request->staff_id;
        $application->date = $request->date;
        $application->amount = $request->amount;
        $application->description = $request->description;
        $application->save();
        //save scores
        LoanApplicationScore::where('loan_application_id', $application->id)->delete();
        foreach ($attributes as $group) {
            foreach ($group['attributes'] as $field) {
                $score = new LoanApplicationScore();
                $score->created_by_id = Auth::id();
                $score->loan_application_id = $application->id;
                $score->scoring_attribute_id = $field['scoring_attribute_id'];
                $score->loan_product_scoring_attribute_id = $field['id'];
                $score->weight = $field['weight'];
                $score->effective_weight = $field['effective_weight'];
                $score->weighted_score = $field['weighted_score'];
                if ($field['attribute']['field_type'] === 'checkbox') {
                    $score->value = json_encode($field['value']);
                } else {
                    $score->value = $field['value'];
                }
                //determine the score
                if ($field['attribute']['field_type'] === 'dropdown' || $field['attribute']['field_type'] === 'radio') {
                    foreach ($field['attribute']['options'] as $key) {
                        if ($key['name'] == $field['value']) {
                            $score->score = $key['score'];
                            //check if score is accepted
                            if (in_array($key['name'], $field['accept_value'])) {
                                $score->accepted = 1;
                            }
                        }
                    }
                } elseif ($field['attribute']['field_type'] === 'checkbox') {
                    $tempScore = 0;
                    $tempAccepted = 0;
                    foreach ($field['value'] as $item) {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($key['name'] == $item) {
                                $tempScore += $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $tempAccepted = 1;
                                }
                            }
                        }
                    }
                    $score->score = $tempScore;
                    $score->accepted = $tempAccepted;
                } elseif ($field['attribute']['field_type'] === 'text' || $field['attribute']['field_type'] === 'number') {
                    if ($field['option_type'] === 'range') {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($field['value'] >= $key['lower_value'] && $field['value'] <= $key['upper_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                        }
                    }
                    if ($field['option_type'] === 'greater_than_or_less_than') {
                        foreach ($field['attribute']['options'] as $key) {
                            if ($key['name'] === 'Greater Than or Equal To' && $field['value'] >= $field['median_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                            if ($key['name'] === 'Less Than' && $field['value'] < $field['median_value']) {
                                $score->score = $key['score'];
                                //check if score is accepted
                                if (in_array($key['name'], $field['accept_value'])) {
                                    $score->accepted = 1;
                                }
                            }
                        }
                    }
                } else {
                    $score->score = $field['score'];
                }
                $score->save();
            }
        }
        //let's update fields of type formula
        $formulaAttributes = LoanApplicationScore::with(['productAttribute'])
            ->where('loan_application_id', $application->id)
            ->whereHas('scoringAttribute', function (Builder $query) {
                $query->where('field_type', 'formula');
            })->get();
        foreach ($formulaAttributes as $attribute) {
            $formula = $attribute->productAttribute->data;
            preg_match_all(
                '/\{\{field_(\d)+}}/',
                $formula,
                $matches,
                PREG_PATTERN_ORDER
            );
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    //find the value of that field
                    $score = LoanApplicationScore::where('loan_product_scoring_attribute_id', explode('_', $match)[1])->where('loan_application_id', $application->id)->first();
                    if (empty($score)) {
                        //throw error
                    }
                    $formula = str_replace($match, $score->value, $formula);
                }
                //at this point our formula is ready
                $math = new EvalMath;
                $value = $math->evaluate($formula);
                $attribute->value = round($value, 2);
            }
            //update the score now that we have a value
            $options = LoanProductScoringAttributeOptionValue::where('loan_product_id', $product->id)->where('loan_product_scoring_attribute_id', $attribute->productAttribute->id)->get();
            if ($attribute->productAttribute->option_type === 'range') {
                foreach ($options as $key) {
                    if ($attribute->value >= $key->lower_value && $attribute->value <= $key->upper_value) {
                        $attribute->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                }
            }
            if ($attribute->productAttribute->option_type === 'greater_than_or_less_than') {
                foreach ($options as $key) {
                    if ($key->name === 'Greater Than or Equal To' && $attribute->value >= $attribute->productAttribute->median_value) {
                        $score->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                    if ($key->name === 'Less Than' && $attribute->value < $attribute->productAttribute->median_value) {
                        $attribute->score = $key->score;
                        //check if score is accepted
                        if (in_array($key->name, $attribute->productAttribute->accept_value)) {
                            $attribute->accepted = 1;
                        }
                    }
                }
            }
            $attribute->save();
        }
        //let's update fields of type calculated
        $formulaAttributes = LoanApplicationScore::with(['productAttribute'])
            ->where('loan_application_id', $application->id)
            ->whereHas('scoringAttribute', function (Builder $query) {
                $query->where('field_type', 'calculated');
            })->get();
        $industryType = $client->industryType;
        $ratio = $client->ratio;
        $shareholders = $client->shareholders;
        $porter = $client->porter;
        foreach ($formulaAttributes as $attribute) {

            $ratioScore = 0.00;
            $accepted = 0;
            $maxScore = $attribute->productAttribute->score;
            $minScore = $attribute->productAttribute->min_score;
            //update is ratio
            if ($attribute->productAttribute->is_ratio) {
                $ratioValue = $ratio->{$attribute->productAttribute->system_name};
                $ratioBenchmarkValue = $industryType->{$attribute->productAttribute->system_name};
                $ratioScore = $ratioBenchmarkValue ? ($ratioValue * $maxScore / $ratioBenchmarkValue) : 0;
                if ($ratioScore > $maxScore) {
                    $ratioScore = $maxScore;
                }
                if ($ratioScore < 0) {
                    $ratioScore = 0.00;
                }
            }
            if ($attribute->productAttribute->is_shareholder_analysis) {
                if ($attribute->productAttribute->system_name !== 'blacklisted' && $attribute->productAttribute->system_name !== 'fraud_alert') {
                    $average = round($client->shareholders->average($attribute->productAttribute->system_name));
                    switch ($average) {
                        case 0:
                            $ratioScore = $maxScore;
                            $accepted = 1;
                            break;
                        case 1:
                            $ratioScore = $maxScore * 40 / 100;
                            $accepted = 1;
                            break;
                        case 2:
                            $ratioScore = $maxScore * 20 / 100;
                            $accepted = 1;
                            break;
                        default:
                            $ratioScore = 0;
                            $accepted = 0;
                    }
                } else {
                    if ($shareholders->where($attribute->productAttribute->system_name, 1)->count() > 0) {
                        $attribute->value = 'yes';
                        $ratioScore = 0;
                        $accepted = 0;
                    } else {
                        $attribute->value = 'no';
                        $ratioScore = $maxScore;
                        $accepted = 1;
                    }
                }

            }
            if ($attribute->productAttribute->is_industry_analysis) {
                if ($attribute->productAttribute->system_name === 'porters_five_forces_analysis') {
                    $porter = $porter->grand_total;
                    if ($porter->grand_total < 0) {
                        $ratioScore = 0;
                        $accepted = 0;
                    }
                    if ($porter->grand_total == 0) {
                        $ratioScore = $maxScore * 50 / 100;
                        $accepted = 1;
                    }
                    if ($porter->grand_total > 0) {
                        $ratioScore = $maxScore;
                        $accepted = 1;
                    }
                }
            }
            $attribute->score = $ratioScore;
            $attribute->accepted = $accepted;
            $attribute->save();
        }
        $application->score = LoanApplicationScore::where('loan_application_id', $application->id)->sum('score');
        $application->score_percentage = $application->score * 100 / $product->score;
        $application->save();
        activity()
            ->performedOn($application)
            ->log('Update Loan Application');
        return redirect()->route('loan_applications.show', $application->id)->with('success', 'Loan application updated successfully.');
    }

    public function changeStatus(Request $request, LoanApplication $application)
    {
        $application->status = $request->status;
        $application->approved_by_id = Auth::id();
        if ($application->isDirty('status')) {
            if ($application->status == 'approved') {
                $application->approved_at = Carbon::now();
                $application->amount = $request->amount ?? $application->applied_amount;
            }
            if ($application->status == 'rejected') {
                $application->approved_at = Carbon::now();
            }
        }
        $application->save();
        activity()
            ->performedOn($application)
            ->log('Change Loan Application status');
        if ($application->wasChanged('status')) {
            event(new LoanApplicationStatusChanged($application));
        }
        return redirect()->route('loan_applications.show', $application->id)->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LoanApplication $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoanApplication $application)
    {
        $application->scores->each->delete();
        $application->delete();
        activity()
            ->performedOn($application)
            ->log('Delete Loan Application');
        return redirect()->route('loan_applications.index')->with('success', 'Loan deleted successfully.');
    }
}
