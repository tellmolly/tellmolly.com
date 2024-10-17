<?php

namespace App\Services;

use App\Http\Requests\TagEditRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagService
{
    public function index(Request $request): array
    {
        $sortOrder = $request->query('sort', 'a-z');

        return [
            'tags' => $request->user()->tags()->withCount('days')
                ->sortBy($sortOrder)
                ->sortBy("name", 'asc')
                ->paginate()->withQueryString(),
            'sortOrder' => $sortOrder
        ];
    }

    public function store(TagEditRequest $request): Tag
    {
        $tag = new Tag();
        $tag->fill($request->validated());

        $request->user()->tags()->save($tag);

        return $tag;
    }

    public function update(TagEditRequest $request, Tag $tag): Tag
    {
        $tag->fill($request->validated());
        $tag->archived_at = $request->has('archive') ? now() : null;
        $tag->save();

        return $tag;
    }

    public function destroy(Tag $tag): Tag
    {
        $tag->delete();

        return $tag;
    }
}
