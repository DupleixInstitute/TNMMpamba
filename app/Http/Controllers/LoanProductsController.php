<?php

namespace App\Http\Controllers;

use App\Events\LoanProductCreated;
use App\Events\LoanProductStatusChanged;
use App\Models\LoanProductCategory;
use App\Models\LoanProduct;
use App\Models\Comment;
use App\Models\LoanProductScoringAttribute;
use App\Models\LoanProductScoringAttributeOptionValue;
use App\Models\ScoringAttributeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loans.products.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.products.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.products.update'])->only(['edit', 'update', 'syncAttributes']);
        $this->middleware(['permission:loans.products.destroy'])->only(['destroy']);
    }

    public function index()
    {

        $products = LoanProduct::filter(\request()->only('search', 'status'))
            ->with(['category', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('LoanProducts/Index', [
            'filters' => \request()->all('search', 'status', 'type'),
            'products' => $products,
            'categories' => LoanProductCategory::all()->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id,
                ];
            }),
        ]);
    }


    public function create()
    {

        return Inertia::render('LoanProducts/Create', [
            'categories' => LoanProductCategory::all()->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'active' => ['required'],
        ]);
        $product = new LoanProduct();
        $product->created_by_id = Auth::id();
        $product->loan_product_category_id = $request->loan_product_category_id;
        $product->name = $request->name;
        $product->score = $request->score;
        $product->active = $request->active ? 1 : 0;
        $product->description = $request->description;
        $product->save();

        activity()
            ->performedOn($product)
            ->log('Create Loan Product');
        return redirect()->route('loan_products.index')->with('success', 'Loan Product created successfully.');
    }


    public function show(LoanProduct $product)
    {
        $product->load(['category', 'createdBy', 'attributes', 'attributes.attribute']);
        $groups = ScoringAttributeGroup::with(['attributes'])->get();
        $groups->transform(function ($group) use ($product) {
            if ($product->attributes->where('scoring_attribute_group_id', $group->id)->count()) {
                $group->used = true;
            } else {
                $group->used = false;
            }
            $group->attributes->transform(function ($item) use ($product) {
                if ($product->attributes->where('scoring_attribute_id', $item->id)->count()) {
                    $item->used = true;
                } else {
                    $item->used = false;
                }
                $optionsArray = [];
                if ($item->field_type === 'dropdown' || $item->field_type === 'radio' || $item->field_type === 'checkbox') {
                    $options = json_decode($item->options);

                    foreach ($options as $option) {
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
                    $item->options = $optionsArray;
                }elseif ($item->field_type === 'number' || $item->field_type === 'text') {
                    $item->options = $optionsArray;
                } else {
                    $item->options = [];
                }
                return $item;
            });

            return $group;
        });
        $product->attributes->transform(function ($item) {
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
                                'loan_product_id' => $item->loan_product_id,
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
                } elseif ($item->attribute->field_type === 'number' || $item->attribute->field_type === 'text') {
                    $optionsArray = [];
                    if ($item->option_type === 'greater_than_or_less_than') {
                        if ($opt = LoanProductScoringAttributeOptionValue::where('loan_product_scoring_attribute_id', $item->id)->where('name', 'Greater Than or Equal To')->first()) {
                            $optionsArray[] = $opt;
                        } else {
                            $optionsArray[] = [
                                'id' => '',
                                'loan_product_scoring_attribute_id' => '',
                                'scoring_attribute_id' => $item->id,
                                'loan_product_id' => $item->loan_product_id,
                                'name' => 'Greater Than or Equal To',
                                'weight' => '',
                                'score' => '',
                                'effective_weight' => '',
                                'weighted_score' => '',
                                'description' => '',
                                'lower_value' => '',
                                'upper_value' => '',
                                'median_value' => '',
                                'active' => true,
                            ];
                        }
                        if ($opt = LoanProductScoringAttributeOptionValue::where('loan_product_scoring_attribute_id', $item->id)->where('name', 'Less Than')->first()) {
                            $optionsArray[] = $opt;
                        } else {
                            $optionsArray[] = [
                                'id' => '',
                                'loan_product_scoring_attribute_id' => '',
                                'scoring_attribute_id' => $item->id,
                                'loan_product_id' => $item->loan_product_id,
                                'name' => 'Less Than',
                                'weight' => '',
                                'score' => '',
                                'effective_weight' => '',
                                'weighted_score' => '',
                                'description' => '',
                                'lower_value' => '',
                                'upper_value' => '',
                                'median_value' => '',
                                'active' => true,
                            ];
                        }
                    }
                    if ($item->option_type === 'range') {
                        $optionsArray = LoanProductScoringAttributeOptionValue::where('loan_product_scoring_attribute_id', $item->id)->get();
                    }
                    $item->attribute->options = $optionsArray;
                } else {
                    $item->attribute->options = [];
                }
            }
            return $item;
        });
        $product->score_attributes = $product->attributes->where('is_group', 1);

        $product->score_attributes->transform(function ($score) use ($product) {
            $score->attributes = $product->attributes->where('scoring_attribute_group_id', $score->scoring_attribute_group_id)->where('is_group', 0)->toArray();

            return $score;
        });
        return Inertia::render('LoanProducts/Show', [
            'product' => $product->toArray(),
            'groups' => $groups
        ]);
    }

    public function edit(LoanProduct $product)
    {

        return Inertia::render('LoanProducts/Edit', [
            'product' => $product,
            'categories' => LoanProductCategory::all()->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id,
                ];
            }),
        ]);
    }

    public function update(Request $request, LoanProduct $product)
    {

        $request->validate([
            'name' => ['required', 'string'],
            'active' => ['required'],
        ]);
        $product->loan_product_category_id = $request->loan_product_category_id;
        $product->name = $request->name;
        $product->score = $request->score;
        $product->active = $request->active ? 1 : 0;
        $product->description = $request->description;
        $product->save();
        activity()
            ->performedOn($product)
            ->log('Update Loan Product');
        return redirect()->route('loan_products.index')->with('success', 'LoanProduct updated successfully.');
    }

    public function destroy(LoanProduct $product)
    {

        $product->delete();
        activity()
            ->performedOn($product)
            ->log('Delete LoanProduct');
        return redirect()->route('loan_products.index')->with('success', 'LoanProduct deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->s;
        $id = $request->id;
        $data = LoanProduct::with(['coPayers', 'coPayers.coPayer'])->where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%$search%");
            $query->orWhere('last_name', 'like', "%$search%");
            $query->orWhere('middle_name', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
            $query->orWhere('id_number', 'like', "%$search%");
            $query->orWhere('external_id', 'like', "%$search%");
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->get();
        return response()->json($data);
    }

    public function syncAttributes(Request $request, LoanProduct $product)
    {
        $attributes = $request->json('attributes');
        $existingAttributes = [];
        $existingOptions = [];
        $allAttributes = LoanProductScoringAttribute::where('loan_product_id', $product->id)->get();
        $allOptions = LoanProductScoringAttributeOptionValue::where('loan_product_id', $product->id)->get();
        foreach ($attributes as $key) {
            if (!empty($key['id'])) {
                $existingAttributes[] = $key['id'];
                $attribute = LoanProductScoringAttribute::find($key['id']);
            }
            if(empty($attribute)){
                $attribute = new LoanProductScoringAttribute();
                $attribute->created_by_id = Auth::id();
                $attribute->loan_product_id = $product->id;
                $attribute->scoring_attribute_group_id = $key['scoring_attribute_group_id'];
            }
            $attribute->name = $key['name'];
            $attribute->weight = $key['weight'] ?? 0.0;
            $attribute->effective_weight = $key['effective_weight'] ?? 0.0;
            $attribute->score = $key['score'] ?? 0.0;
            $attribute->weighted_score = $key['weighted_score'] ?? 0.0;
            $attribute->min_score = $key['min_score'] ?? 0.0;
            $attribute->max_score = $key['max_score'] ?? 0.0;
            $attribute->reject_value = $key['reject_value'];
            if (is_array($key['accept_value'])) {
                $attribute->accept_value = $key['accept_value'];
            } else {
                $attribute->accept_value = $key['accept_value'];
            }
            $attribute->accept_condition = $key['accept_condition'];
            $attribute->option_type = $key['option_type'];
            $attribute->median_value = $key['median_value'];
            $attribute->order_position = $key['order_position'];
            $attribute->active = $key['active'] ? 1 : 0;
            $attribute->is_group = $key['is_group'] ? 1 : 0;
            $attribute->save();
            foreach ($key['attributes'] as $item) {
                if (!empty($item['id'])) {
                    $existingAttributes[] = $item['id'];
                    $attribute = LoanProductScoringAttribute::find($item['id']);
                }
                if(empty($attribute)){
                    $attribute = new LoanProductScoringAttribute();
                    $attribute->created_by_id = Auth::id();
                    $attribute->loan_product_id = $product->id;
                    $attribute->scoring_attribute_id = $item['scoring_attribute_id'];
                    $attribute->scoring_attribute_group_id = $item['scoring_attribute_group_id'];
                }
                $attribute->name = $item['name'];
                $attribute->weight = $item['weight'] ?? 0.0;
                $attribute->effective_weight = $item['effective_weight'] ?? 0.0;
                $attribute->score = $item['score'] ?? 0.0;
                $attribute->weighted_score = $item['weighted_score'] ?? 0.0;
                $attribute->min_score = $item['min_score'] ?? 0.0;
                $attribute->max_score = $item['max_score'] ?? 0.0;
                $attribute->reject_value = $item['reject_value'];
                $attribute->accept_value = $item['accept_value'];
                $attribute->accept_condition = $key['accept_condition'];
                $attribute->option_type = $item['option_type'];
                $attribute->median_value = $item['median_value'];
                $attribute->order_position = $item['order_position'];
                $attribute->active = $item['active'] ? 1 : 0;
                $attribute->is_group = $item['is_group'] ? 1 : 0;
                $attribute->save();
                //save options
                if (!empty($item['attribute']['options']) && is_array($item['attribute']['options'])) {
                    foreach ($item['attribute']['options'] as $option) {
                        if (!empty($option['id'])) {
                            $attributeOption = LoanProductScoringAttributeOptionValue::find($option['id']);
                            $existingOptions[] = $option['id'];
                        } else {
                            $attributeOption = new LoanProductScoringAttributeOptionValue();
                            $attributeOption->loan_product_scoring_attribute_id = $attribute->id;
                            $attributeOption->scoring_attribute_id = $option['scoring_attribute_id'];
                            $attributeOption->loan_product_id = $product->id;
                        }
                        $attributeOption->weight = $option['weight'] ?? 0.0;
                        $attributeOption->effective_weight = $option['effective_weight'] ?? 0.0;
                        $attributeOption->score = $option['score'] ?? 0.0;
                        $attributeOption->weighted_score = $option['weighted_score'] ?? 0.0;
                        $attributeOption->name = $option['name'];
                        $attributeOption->lower_value = $option['lower_value'] ?? null;
                        $attributeOption->upper_value = $option['upper_value'] ?? null;
                        $attributeOption->median_value = $option['median_value'] ?? null;
                        $attributeOption->active = $option['active'] ? 1 : 0;
                        $attributeOption->save();
                    }
                }
            }
        }
        //delete attributes that have been removed
        $allAttributes->each(function ($item) use ($existingAttributes) {
            if (!in_array($item->id, $existingAttributes)) {
                LoanProductScoringAttribute::where('id', $item->id)->delete();
                if ($item->is_group) {
                    LoanProductScoringAttribute::where('scoring_attribute_group_id', $item->id)->delete();
                }
            }
        });
        $allOptions->each(function ($item) use ($existingOptions) {
            if (!in_array($item->id, $existingOptions)) {
                LoanProductScoringAttributeOptionValue::where('id', $item->id)->delete();
            }
        });
        activity()
            ->performedOn($product)
            ->log('Update Loan Product');
        return redirect()->route('loan_products.show', $product->id)->with('success', 'Product attributes updated successfully.');
    }

    public function storeComment(Request $request, LoanProduct $product)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $comment = new Comment();
        $comment->created_by_id = Auth::id();
        $comment->record_id = $product->id;
        $comment->category = 'product';
        $comment->description = $request->description;
        $comment->save();

        activity()
            ->performedOn($product)
            ->log('Create LoanProduct Comment');
        return redirect()->route('products.show', $product->id)->with('success', 'comment created successfully.');
    }

    public function destroyComment(Comment $comment)
    {

        $comment->delete();
        activity()
            ->performedOn($comment)
            ->log('Delete LoanProduct comment');
        return redirect()->route('products.show', $comment->record_id)->with('success', 'comment deleted successfully.');
    }
}
