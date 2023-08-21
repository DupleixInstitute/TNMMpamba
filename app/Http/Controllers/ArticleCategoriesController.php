<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:articles.categories.index'])->only(['index', 'show']);
        $this->middleware(['permission:articles.categories.create'])->only(['create', 'store']);
        $this->middleware(['permission:articles.categories.update'])->only(['edit', 'update']);
        $this->middleware(['permission:articles.categories.destroy'])->only(['destroy']);
    }

    public function index()
    {
        $categories = ArticleCategory::filter(\request()->only('search'))
            ->paginate();
        return Inertia::render('ArticleCategories/Index', [
            'filters' => \request()->all('search'),
            'categories' => $categories,
        ]);
    }

    public function create()
    {

        return Inertia::render('ArticleCategories/Create', [

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category = new ArticleCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Create Article Category');
        return redirect()->route('articles.categories.index')->with('success', 'Article Category created successfully.');
    }

    public function show(ArticleCategory $category)
    {

        return Inertia::render('ArticleCategories/Show', [
            'category' => $category
        ]);
    }

    public function edit(ArticleCategory $category)
    {
        return Inertia::render('ArticleCategories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, ArticleCategory $category)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        activity()
            ->performedOn($category)
            ->log('Update Article Category');
        return redirect()->route('articles.categories.index')->with('success', 'Article Category updated successfully.');
    }

    public function destroy(ArticleCategory $category)
    {
        $category->delete();
        activity()
            ->performedOn($category)
            ->log('Delete Article Category');
        return redirect()->route('articles.categories.index')->with('success', 'Article Category deleted successfully.');
    }
}
