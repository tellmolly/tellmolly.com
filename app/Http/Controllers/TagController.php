<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagEditRequest;
use App\Models\Tag;
use App\Services\TagService;
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
        return view('tag.index', (new TagService)->index($request));
    }

    public function create(): View
    {
        return view('tag.create', [
            'tag' => new Tag()
        ]);
    }

    public function store(TagEditRequest $request): RedirectResponse
    {
        (new TagService)->store($request);

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
        (new TagService)->update($request, $tag);

        return redirect()->route('tags.index');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        (new TagService)->destroy($tag);

        return redirect()->route('tags.index');
    }
}
