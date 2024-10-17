<?php

namespace App\Services;

use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Category;
use App\Models\Day;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DayService
{
    public function index(Request $request): array
    {
        return [
            'isSearch' => $request->get('search') !== null,
            'days' => $request->user()->days()->with('category', 'tags')->search($request->get('search'))->orderByDesc('date')->paginate(30)->withQueryString(),
        ];
    }

    public function create(Request $request): array
    {
        $validated = $request->validate([
            'initial' => ['sometimes', 'date_format:Y-m-d']
        ]);

        return [
            'day' => new Day(['date' => $validated['initial'] ?? date('Y-m-d')]),
            'categories' => Category::orderBy('order')->get(),
            'tags' => $request->user()->tags()->whereNull('archived_at')->get()
        ];
    }

    public function store(DayStoreRequest $request): Day
    {
        $day = new Day();
        $day->fill($request->validated());

        $request->user()->days()->save($day);

        $day->tags()->sync(
            $request->tag_ids
                ? $request->user()->tags()->whereIn('slug', $request->tag_ids)->pluck('id')->toArray()
                : []
        );

        return $day;
    }

    public function edit(Request $request, Day $day): array
    {
        return [
            'day' => $day,
            'categories' => Category::orderBy('order')->get(),
            'tags' => $request->user()->tags
        ];
    }

    public function update(DayEditRequest $request, Day $day): Day
    {
        $day->fill($request->validated());
        $day->save();

        $day->tags()->sync(
            $request->tag_ids
                ? $request->user()->tags()->whereIn('slug', $request->tag_ids)->pluck('id')->toArray()
                : []
        );

        return $day;
    }

    public function destroy(Day $day): Day
    {
        $day->delete();

        return $day;
    }

    public function jump(Request $request): ?Day
    {
        $validated = $request->validate([
            'jump' => ['required', 'date']
        ]);

        return $request->user()->days()->where('date', '=', $validated['jump'])->first();
    }

    public function previous(Request $request, Day $day): ?Day
    {
        return $request->user()->days()
            ->where('date', '<', $day->date)
            ->orderByDesc('date')
            ->first();
    }

    public function next(Request $request, Day $day): ?Day
    {
        return $request->user()->days()
            ->where('date', '>', $day->date)
            ->orderBy('date', 'asc')
            ->first();
    }

    public function today(Request $request): ?Day
    {
        $today = Carbon::now()->toDateString();

        return $request->user()->days()
            ->where('date', $today)
            ->first();
    }
}
