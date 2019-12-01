<?php

namespace App\Http\Controllers;

use App\Day;
use App\Tag;
use Exception;
use App\Category;
use Illuminate\Http\Response;
use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('day.index', [
            'days' => Day::orderByDesc('date')->paginate(30)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('day.create', [
            'day' => new Day(['date' => date('Y-m-d')]),
            'categories' => Category::all(),
            'tags' => Tag::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DayStoreRequest $request
     *
     * @return Response
     */
    public function store(DayStoreRequest $request)
    {
        $day = new Day();
        $day->fill($request->validated());
        $day->category_id = $request->category_id;
        $day->save();

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Day $day
     *
     * @return Response
     */
    public function show(Day $day)
    {
        return view('day.show', [
            'day' => $day
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Day $day
     *
     * @return Response
     */
    public function edit(Day $day)
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => Category::all(),
            'tags' => Tag::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DayEditRequest $request
     * @param Day            $day
     *
     * @return Response
     */
    public function update(DayEditRequest $request, Day $day)
    {
        $day->fill($request->validated());
        $day->save();

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Day $day
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Day $day)
    {
        $day->delete();

        return redirect()->route('days.index');
    }
}
