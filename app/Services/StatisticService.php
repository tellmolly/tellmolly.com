<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Day;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $now = $request->user()->localNow();
        $dates = $request->user()->days()->pluck('date');
        $streaks = new StreakService;
        $currentStreak = $streaks->current($dates, $now);
        $longestStreak = $streaks->longest($dates);

        return [
            'localToday' => $now->format('Y-m-d'),
            'today' => $request->user()->days()->with('category')->where('date', '=', $now->format('Y-m-d'))->first(),
            'categories' => Category::orderBy('order')->get(),
            'goodDay' => $this->randomGoodDay($request, [$great->id, $good->id], $now),
            'tagInsights' => $this->tagInsights($request, [$great->id, $good->id]),
            'lastWeek' => $request->user()->days()->with('category')->where('date', '=', $now->subWeek()->format('Y-m-d'))->first(),
            'onThisDay' => $request->user()->days()->with('category')->where('date', '=', $now->subYear()->format('Y-m-d'))->first(),
            // Same shape the old raw streak queries returned, which the API exposes.
            'currentStreak' => [(object) ['streak' => $currentStreak]],
            'longestStreak' => [(object) ['max_streak' => $longestStreak]],
            'streakMessage' => $streaks->message($currentStreak, $longestStreak),
            'greatDays' => $request->user()->days()->where('category_id', '=', $great->id)->count(),
            'goodDays' => $request->user()->days()->where('category_id', '=', $good->id)->count(),
            'averageDays' => $request->user()->days()->where('category_id', '=', $average->id)->count(),
            'greatDays1Month' => $request->user()->days()->where('category_id', '=', $great->id)->where('date', '>', $now->subDays(30)->format('Y-m-d'))->count(),
            'goodDays1Month' => $request->user()->days()->where('category_id', '=', $good->id)->where('date', '>', $now->subDays(30)->format('Y-m-d'))->count(),
            'averageDays1Month' => $request->user()->days()->where('category_id', '=', $average->id)->where('date', '>', $now->subDays(30)->format('Y-m-d'))->count(),
        ];
    }

    /**
     * Tags that tend to come with good days, e.g. "Hiking: 9 of 10 days were
     * good or great". Only ever positive: tags that mostly come with bad days
     * are simply not mentioned.
     *
     * @return array<int, array{name: string, color: string, good: int, total: int}>
     */
    private function tagInsights(Request $request, array $goodCategoryIds): array
    {
        $minimumDays = 5;
        $minimumShare = 0.6;

        return DB::table('day_tag')
            ->join('days', 'days.id', '=', 'day_tag.day_id')
            ->join('tags', 'tags.id', '=', 'day_tag.tag_id')
            ->where('days.user_id', '=', $request->user()->id)
            ->whereNull('tags.archived_at')
            ->groupBy('tags.id', 'tags.name', 'tags.color')
            ->havingRaw('COUNT(*) >= ?', [$minimumDays])
            ->select('tags.name', 'tags.color')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN days.category_id IN (' . implode(',', array_fill(0, count($goodCategoryIds), '?')) . ') THEN 1 ELSE 0 END) as good', $goodCategoryIds)
            ->get()
            ->map(fn($row) => ['name' => $row->name, 'color' => $row->color, 'good' => (int) $row->good, 'total' => (int) $row->total])
            ->filter(fn($tag) => $tag['good'] / $tag['total'] >= $minimumShare)
            ->sortBy([fn($a, $b) => $b['good'] / $b['total'] <=> $a['good'] / $a['total'], fn($a, $b) => $b['total'] <=> $a['total']])
            ->take(3)
            ->values()
            ->all();
    }

    /**
     * Pick a random great or good day to remind the user of. Only days at least
     * a month old count as memories, and ones with something written down are
     * preferred, since a bare smiley isn't much to look back on.
     */
    private function randomGoodDay(Request $request, array $categoryIds, CarbonImmutable $now): ?Day
    {
        $query = fn() => $request->user()->days()->with('category', 'tags')
            ->whereIn('category_id', $categoryIds)
            ->where('date', '<=', $now->subDays(30)->format('Y-m-d'))
            ->inRandomOrder();

        return $query()->where(fn($query) => $query->where('grateful_for', '<>', '')->orWhere('comment', '<>', ''))->first()
            ?? $query()->first();
    }
}
