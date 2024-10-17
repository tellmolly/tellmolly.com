<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagEditRequest;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tag::class, 'tag');
    }

    public function index(Request $request): array
    {
        return (new TagService)->index($request);
    }

    public function store(TagEditRequest $request): Tag
    {
        return (new TagService)->store($request);
    }

    public function edit(Tag $tag): Tag
    {
        return $tag->load('days', 'days.category', 'days.tags');
    }

    public function update(TagEditRequest $request, Tag $tag): Tag
    {
        return (new TagService)->update($request, $tag);
    }

    public function destroy(Tag $tag): Tag
    {
        return (new TagService)->destroy($tag);
    }
}
