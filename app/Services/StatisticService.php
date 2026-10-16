<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Day;
use Illuminate\Http\Request;

class StatisticService
{
    public function index(Request $request): array
    {
        $categories = Category::query()->whereIn('order', [
            Category::GREAT,
            Category::GOOD,
            Category::AVERAGE
        ])->get();

        $great = $categories->firstWhere('order', '=', Category::GREAT);
        $good = $categories->firstWhere('order', '=', Category::GOOD);
        $average = $categories->firstWhere('order', '=', Category::AVERAGE);

        $dates = $request->user()->days()->pluck('date');
        $streaks = new StreakService;
        $currentStreak = $streaks->current($dates);
        $longestStreak = $streaks->longest($dates);

        return [
            'today' => $request->user()->days()->with('category')->where('date', '=', now()->format('Y-m-d'))->first(),
            'categories' => Category::orderBy('order')->get(),
            'goodDay' => $this->randomGoodDay($request, [$great->id, $good->id]),
            'lastWeek' => $request->user()->days()->with('category')->where('date', '=', now()->subWeek()->format('Y-m-d'))->first(),
            'onThisDay' => $request->user()->days()->with('category')->where('date', '=', now()->subYear()->format('Y-m-d'))->first(),
            // Same shape the old raw streak queries returned, which the API exposes.
            'currentStreak' => [(object) ['streak' => $currentStreak]],
            'longestStreak' => [(object) ['max_streak' => $longestStreak]],
            'streakMessage' => $streaks->message($currentStreak, $longestStreak),
            'greatDays' => $request->user()->days()->where('category_id', '=', $great->id)->count(),
            'goodDays' => $request->user()->days()->where('category_id', '=', $good->id)->count(),
            'averageDays' => $request->user()->days()->where('category_id', '=', $average->id)->count(),
            'greatDays1Month' => $request->user()->days()->where('category_id', '=', $great->id)->where('date', '>', now()->subDays(30)->format('Y-m-d'))->count(),
            'goodDays1Month' => $request->user()->days()->where('category_id', '=', $good->id)->where('date', '>', now()->subDays(30)->format('Y-m-d'))->count(),
            'averageDays1Month' => $request->user()->days()->where('category_id', '=', $average->id)->where('date', '>', now()->subDays(30)->format('Y-m-d'))->count(),
        ];
    }

    /**
     * Pick a random great or good day to remind the user of. Only days at least
     * a month old count as memories, and ones with something written down are
     * preferred, since a bare smiley isn't much to look back on.
     */
    private function randomGoodDay(Request $request, array $categoryIds): ?Day
    {
        $query = fn() => $request->user()->days()->with('category', 'tags')
            ->whereIn('category_id', $categoryIds)
            ->where('date', '<=', now()->subDays(30)->format('Y-m-d'))
            ->inRandomOrder();

        return $query()->where(fn($query) => $query->where('grateful_for', '<>', '')->orWhere('comment', '<>', ''))->first()
            ?? $query()->first();
    }
}
