<?php

namespace App\Http\Controllers;

use App\Tag;
use Exception;
use Illuminate\Http\Response;
use App\Http\Requests\TagEditRequest;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('tag.index', [
            'tags' => Tag::paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('tag.create', [
            'tag' => new Tag()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TagEditRequest $request
     *
     * @return Response
     */
    public function store(TagEditRequest $request)
    {
        $tag = new Tag();
        $tag->fill($request->validated());
        $tag->save();

        return redirect()->route('tags.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Tag $tag
     *
     * @return Response
     */
    public function show(Tag $tag)
    {
        return view('tag.show', [
            'tag' => $tag
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tag $tag
     *
     * @return Response
     */
    public function edit(Tag $tag)
    {
        return view('tag.edit', [
            'tag' => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TagEditRequest $request
     * @param Tag            $tag
     *
     * @return Response
     */
    public function update(TagEditRequest $request, Tag $tag)
    {
        $tag->fill($request->validated());
        $tag->save();

        return redirect()->route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('tags.index');
    }
}
