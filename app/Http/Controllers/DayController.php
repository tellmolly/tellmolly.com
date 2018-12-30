<?php

namespace App\Http\Controllers;

use App\Day;
use App\Category;
use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('day.index', [
            'days' => Day::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('day.create', [
            'day' => new Day(),
            'categories' => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  DayEditRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DayEditRequest $request)
    {
        $day = new Day();
        $day->fill($request->validated());
        $day->category_id = $request->category_id;
        $day->save();

        return redirect()->route('days.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Day $day
     *
     * @return \Illuminate\Http\Response
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
     * @param  \App\Day $day
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Day $day)
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => Category::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  DayStoreRequest $request
     * @param  \App\Day        $day
     *
     * @return \Illuminate\Http\Response
     */
    public function update(DayStoreRequest $request, Day $day)
    {
        $day->fill($request->validated());
        $day->save();

        return redirect()->route('days.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Day $day
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Day $day)
    {
        $day->delete();

        return redirect()->route('days.index');
    }
}
