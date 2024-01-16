<?php

namespace App\Http\Controllers;

use App\Models\LoanProductScoringAttribute;
use App\Models\ScoringAttribute;
use App\Models\ScoringAttributeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScoringAttributesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loans.scoring_attributes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.scoring_attributes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.scoring_attributes.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.scoring_attributes.destroy'])->only(['destroy']);
    }

    public function index()
    {

        $attributes = ScoringAttributeGroup::filter(\request()->only('search'))
            ->with(['scoringAttributes', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('ScoringAttributeGroups/Index', [
            'filters' => \request()->all('search', 'status', 'type'),
            'attributes' => $attributes,
        ]);
    }

    public function create()
    {

        return Inertia::render('ScoringAttributeGroups/Create', [

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],

        ]);
        $attribute = new ScoringAttributeGroup();
        $attribute->created_by_id = Auth::id();
        $attribute->parent_id = $request->parent_id;
        $attribute->name = $request->name;
        $attribute->description = $request->description;
        $attribute->active = $request->active ? 1 : 0;
        $attribute->save();

        activity()
            ->performedOn($attribute)
            ->log('Create Scoring Attribute');
        return redirect()->route('scoring_attributes.index')->with('success', 'Scoring Attribute created successfully.');
    }

    public function storeItem(Request $request, ScoringAttributeGroup $group)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'field_type' => ['required'],
        ]);
        if (!empty($request->id)) {
            $attribute = ScoringAttribute::find($request->id);
        } else {
            $attribute = new ScoringAttribute();
            $attribute->created_by_id = Auth::id();
            $attribute->scoring_attribute_group_id = $request->scoring_attribute_group_id;
        }
        $attribute->name = $request->name;
        $attribute->field_type = $request->field_type;
        $attribute->condition = $request->condition;
        $attribute->default_values = $request->default_values;
        if ($request->field_type === 'dropdown' || $request->field_type === 'radio' || $request->field_type === 'checkbox') {
            $attribute->options = json_encode($request->options);
        } else {
            $attribute->options = $request->options;
        }
        $attribute->rules = $request->rules;
        $attribute->class = $request->class;
        $attribute->description = $request->description;
        $attribute->required = $request->required ? 1 : 0;
        $attribute->active = $request->active ? 1 : 0;
        $attribute->save();
        activity()
            ->performedOn($attribute)
            ->log('Create Scoring Attribute');
        return redirect()->back()->with('success', 'Scoring Attribute created successfully.');
    }

    public function show(ScoringAttributeGroup $attribute)
    {
        $attribute->load(['scoringAttributes', 'createdBy']);
        $attribute->scoringAttributes->transform(function ($item) {
            if ($item->field_type === 'dropdown' || $item->field_type === 'radio' || $item->field_type === 'checkbox') {
                $item->options = json_decode($item->options);
            }
            return $item;
        });
        return Inertia::render('ScoringAttributeGroups/Show', [
            'attribute' => $attribute
        ]);
    }

    public function edit(ScoringAttributeGroup $attribute)
    {
        return Inertia::render('ScoringAttributes/Edit', [
            'attribute' => $attribute,
        ]);
    }

    public function update(Request $request, ScoringAttributeGroup $attribute)
    {

        $request->validate([
            'name' => ['required', 'string'],
        ]);
        $attribute->parent_id = $request->parent_id;
        $attribute->name = $request->name;
        $attribute->description = $request->description;
        $attribute->active = $request->active ? 1 : 0;
        $attribute->save();
        activity()
            ->performedOn($attribute)
            ->log('Update Scoring Attribute');

        return redirect()->route('scoring_attributes.index')->with('success', 'Scoring Attribute updated successfully.');
    }

    public function destroy(ScoringAttributeGroup $attribute)
    {
        $count = LoanProductScoringAttribute::where('scoring_attribute_group_id', $attribute->id)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'You cannot delete this attribute, its being used in ' . $count . ' places.');
        }
        $attribute->delete();
        activity()
            ->performedOn($attribute)
            ->log('Delete Scoring Attribute');
        return redirect()->route('scoring_attributes.index')->with('success', 'Scoring Attribute deleted successfully.');
    }

    public function destroyItem(ScoringAttribute $attribute)
    {
        $count = LoanProductScoringAttribute::where('scoring_attribute_id', $attribute->id)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'You cannot delete this attribute, its being used in ' . $count . ' places.');
        }
        $attribute->delete();
        activity()
            ->performedOn($attribute)
            ->log('Delete Scoring Attribute');
        return redirect()->back()->with('success', 'Scoring Attribute deleted successfully.');
    }


}
