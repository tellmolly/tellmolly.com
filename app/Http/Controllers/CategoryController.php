<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\CategoryEditRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('category.index', [
            'categories' => Category::withCount('days')->paginate()
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
        $category->save();

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
