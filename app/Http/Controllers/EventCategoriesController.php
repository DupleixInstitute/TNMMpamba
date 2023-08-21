<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:events.categories.index'])->only(['index', 'show']);
        $this->middleware(['permission:events.categories.create'])->only(['create', 'store']);
        $this->middleware(['permission:events.categories.update'])->only(['edit', 'update']);
        $this->middleware(['permission:events.categories.destroy'])->only(['destroy']);
    }

    public function index()
    {
        $categories = EventCategory::filter(\request()->only('search'))
            ->paginate();
        return Inertia::render('EventCategories/Index', [
            'filters' => \request()->all('search'),
            'categories' => $categories,
        ]);
    }

    public function create()
    {

        return Inertia::render('EventCategories/Create', [

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category = new EventCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Create Event Category');
        return redirect()->route('events.categories.index')->with('success', 'Event Category created successfully.');
    }

    public function show(EventCategory $category)
    {

        return Inertia::render('EventCategories/Show', [
            'category' => $category
        ]);
    }

    public function edit(EventCategory $category)
    {
        return Inertia::render('EventCategories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, EventCategory $category)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Update Event Category');
        return redirect()->route('events.categories.index')->with('success', 'Event Category updated successfully.');
    }

    public function destroy(EventCategory $category)
    {
        $category->delete();
        activity()
            ->performedOn($category)
            ->log('Delete Event Category');
        return redirect()->route('events.categories.index')->with('success', 'Event Category deleted successfully.');
    }
}
