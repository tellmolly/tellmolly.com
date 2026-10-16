<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Day;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YearInReviewService
{
    public function get(Request $request): array
    {
        // The review is offered from late December into mid-January, so it
        // covers the current year in December and the previous one otherwise.
        $year = now()->month === 12 ? now()->year : now()->year - 1;

        $categories = Category::query()->whereIn('order', [
            Category::GREAT,
            Category::GOOD,
            Category::AVERAGE,
            Category::BAD,
            Category::WORST,
        ])->get();

        $great = $categories->firstWhere('order', '=', Category::GREAT);
        $good = $categories->firstWhere('order', '=', Category::GOOD);
        $average = $categories->firstWhere('order', '=', Category::AVERAGE);
        $bad = $categories->firstWhere('order', '=', Category::BAD);
        $worst = $categories->firstWhere('order', '=', Category::WORST);

        $days = $request->user()->days()->year($year)->get(['id', 'date', 'category_id', 'grateful_for']);
        $greatDays = $days->where('category_id', '=', $great->id);

        $tagUsage = DB::table('day_tag')
            ->select('tag_id', DB::raw('COUNT(*) as amount'))
            ->whereIn('day_id', $days->pluck('id'))
            ->groupBy('tag_id')
            ->orderByDesc('amount')
            ->get();

        $mostUsedTag = $tagUsage->first();

        // The month with the most great days; ties go to the earlier month.
        $bestMonth = $greatDays
            ->countBy(fn(Day $day) => (int) substr($day->date, 5, 2))
            ->sortKeys()
            ->sortDesc()
            ->keys()
            ->first();

        return [
            'year' => $year,
            'gratefulDays' => $days->whereNotNull('grateful_for')->count(),
            'greatDays' => $greatDays->count(),
            'goodDays' => $days->where('category_id', '=', $good->id)->count(),
            'normalDays' => $days->where('category_id', '=', $average->id)->count(),
            'badDays' => $days->where('category_id', '=', $bad->id)->count(),
            'worstDays' => $days->where('category_id', '=', $worst->id)->count(),
            'longestGreatDayStreak' => (new StreakService)->longest($greatDays->pluck('date')),
            'bestMonth' => $bestMonth ? Carbon::create($year, $bestMonth)->format('F') : '-',
            'daysTracked' => $days->count(),
            'differentTagsUsed' => $tagUsage->count(),
            'overallTagUsage' => (int) $tagUsage->sum('amount'),
            'mostUsedTag' => $mostUsedTag ? Tag::find($mostUsedTag->tag_id)->name : null,
            'mostUsedTagUsages' => $mostUsedTag?->amount,
        ];
    }
}
