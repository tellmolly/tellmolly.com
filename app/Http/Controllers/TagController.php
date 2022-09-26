<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagEditRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    public function index(): View
    {
        return view('tag.index', [
            'tags' => Tag::withCount('days')->paginate()
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
        $tag->save();

        return redirect()->route('tags.index');
    }

    public function show(Tag $tag): View
    {
        return view('tag.show', [
            'tag' => $tag
        ]);
    }

    public function edit(Tag $tag): View
    {
        return view('tag.edit', [
            'tag' => $tag
        ]);
    }

    public function update(TagEditRequest $request, Tag $tag): RedirectResponse
    {
        $tag->fill($request->validated());
        $tag->save();

        return redirect()->route('tags.index');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index');
    }
}
