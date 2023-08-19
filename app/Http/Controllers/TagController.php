<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagEditRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tag::class, 'tag');
    }

    public function index(Request $request): View
    {
        $sortOrder = $request->query('sort', 'a-z');

        return view('tag.index', [
            'tags' => $request->user()->tags()->withCount('days')
                ->sortBy($sortOrder)
                ->sortBy("name", 'asc')
                ->paginate()->withQueryString(),
            'sortOrder' => $sortOrder
        ]);
    }

    public function create(): View
    {
        return view('tag.create', [
            'tag' => new Tag()
        ]);
    }

    public function store(TagEditRequest $request): RedirectResponse
    {
        $tag = new Tag();
        $tag->fill($request->validated());

        $request->user()->tags()->save($tag);

        return redirect()->route('tags.index');
    }

    public function edit(Tag $tag): View
    {
        return view('tag.edit', [
            'tag' => $tag->load('days', 'days.category', 'days.tags')
        ]);
    }

    public function update(TagEditRequest $request, Tag $tag): RedirectResponse
    {
        $tag->fill($request->validated());
        $tag->archived_at = $request->has('archive') ? now() : null;
        $tag->save();

        return redirect()->route('tags.index');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index');
    }
}
