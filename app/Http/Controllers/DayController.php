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
    public function __construct()
    {
        $this->authorizeResource(Day::class, 'day');
    }

    public function index(Request $request): View
    {
        return view('day.index', [
            'days' => $request->user()->days()->search($request->get('search'))->orderByDesc('date')->paginate(30)->withQueryString()
        ]);
    }

    public function create(Request $request): View
    {
        return view('day.create', [
            'day' => new Day(['date' => date('Y-m-d')]),
            'categories' => $request->user()->categories,
            'tags' => $request->user()->tags
        ]);
    }

    public function store(DayStoreRequest $request): RedirectResponse
    {
        $day = new Day();
        $day->fill($request->validated());
        $day->category_id = $request->category_id;

        $request->user()->days()->save($day);

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function show(Day $day): View
    {
        return view('day.show', [
            'day' => $day
        ]);
    }

    public function edit(Request $request, Day $day): View
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => $request->user()->categories,
            'tags' => $request->user()->tags
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
