<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:courses.categories.index'])->only(['index', 'show']);
        $this->middleware(['permission:courses.categories.create'])->only(['create', 'store']);
        $this->middleware(['permission:courses.categories.update'])->only(['edit', 'update']);
        $this->middleware(['permission:courses.categories.destroy'])->only(['destroy']);
    }

    public function index()
    {
        $categories = CourseCategory::filter(\request()->only('search'))
            ->paginate();
        return Inertia::render('CourseCategories/Index', [
            'filters' => \request()->all('search'),
            'categories' => $categories,
        ]);
    }

    public function create()
    {

        return Inertia::render('CourseCategories/Create', [

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category = new CourseCategory();
        $category->name = $request->name;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Create Course Category');
        return redirect()->route('courses.categories.index')->with('success', 'Course Category created successfully.');
    }

    public function show(CourseCategory $category)
    {

        return Inertia::render('CourseCategories/Show', [
            'category' => $category
        ]);
    }

    public function edit(CourseCategory $category)
    {
        return Inertia::render('CourseCategories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, CourseCategory $category)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category->name = $request->name;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Update Course Category');
        return redirect()->route('courses.categories.index')->with('success', 'Course Category updated successfully.');
    }

    public function destroy(CourseCategory $category)
    {
        $category->delete();
        activity()
            ->performedOn($category)
            ->log('Delete Course Category');
        return redirect()->route('courses.categories.index')->with('success', 'Course Category deleted successfully.');
    }
}
