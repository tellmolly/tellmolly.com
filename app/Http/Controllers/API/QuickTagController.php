<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class QuickTagController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $tag = new Tag();
        $tag->fill($validated);
        $tag->color = Tag::colorFor($validated['name']);

        $tag =  $request->user()->tags()->save($tag);

        return [
            'name' => $tag->name,
            'slug' => $tag->slug
        ];
    }
}
