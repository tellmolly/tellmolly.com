<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Category;
use App\Models\Day;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DayController extends Controller
{
    public function index(Request $request): View
    {
        return view('day.index', [
            'days' => Day::search($request->get('search'))->orderByDesc('date')->paginate(30)->withQueryString()
        ]);
    }

    public function create(): View
    {
        return view('day.create', [
            'day' => new Day(['date' => date('Y-m-d')]),
            'categories' => Category::all(),
            'tags' => Tag::all()
        ]);
    }

    public function store(DayStoreRequest $request): RedirectResponse
    {
        $day = new Day();
        $day->fill($request->validated());
        $day->category_id = $request->category_id;
        $day->save();

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function show(Day $day): View
    {
        return view('day.show', [
            'day' => $day
        ]);
    }

    public function edit(Day $day): View
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => Category::all(),
            'tags' => Tag::all()
        ]);
    }

    public function update(DayEditRequest $request, Day $day): RedirectResponse
    {
        $day->fill($request->validated());
        $day->save();

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function destroy(Day $day): RedirectResponse
    {
        $day->delete();

        return redirect()->route('days.index');
    }
}
