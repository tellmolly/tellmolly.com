<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryEditRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(Request $request): View
    {
        return view('category.index', [
            'categories' => $request->user()->categories()->withCount('days')->paginate()
        ]);
    }

    public function create(): View
    {
        return view('category.create', [
            'category' => new Category()
        ]);
    }

    public function store(CategoryEditRequest $request): RedirectResponse
    {
        $category = new Category();
        $category->fill($request->validated());

        $request->user()->categories()->save($category);

        return redirect()->route('categories.index');
    }

    public function show(Category $category): View
    {
        return view('category.show', [
            'category' => $category
        ]);
    }

    public function edit(Category $category): View
    {
        return view('category.edit', [
            'category' => $category
        ]);
    }

    public function update(CategoryEditRequest $request, Category $category): RedirectResponse
    {
        $category->fill($request->validated());
        $category->save();

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
