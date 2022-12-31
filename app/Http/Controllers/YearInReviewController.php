<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YearInReviewController extends Controller
{
    public function show(Request $request)
    {
        $year = 2022;
        $user = $request->user();

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

        $tagQuery = DB::select(DB::raw("SELECT tag_id, COUNT(*) amount
FROM day_tag
WHERE day_id IN (SELECT id FROM days WHERE user_id = :user_id AND YEAR(`date`) = :year)
GROUP BY tag_id
ORDER BY amount desc"), [
            'user_id' => auth()->user()->id,
            'year' => $year
        ]);

        $overallTagUsage = 0;
        foreach ($tagQuery as $row) {
            $overallTagUsage += $row->amount;
        }

        if (isset($tagQuery[0])) {
            $mostUsedTag = Tag::find($tagQuery[0]->tag_id)->name;
            $mostUsedTagUsage = $tagQuery[0]->amount;
        } else {
            $mostUsedTag = null;
            $mostUsedTagUsage = null;
        }

        $countQueries = DB::query()->select(DB::raw('count(*) as amount'))->from('days')->where('user_id', $user->id)->whereYear('date', $year)->whereNotNull('grateful_for')
            ->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)->where('category_id', '=', $great->id)
            )
            ->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)->where('category_id', '=', $good->id)
            )->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)->where('category_id', '=', $average->id)
            )->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)->where('category_id', '=', $bad->id)
            )->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)->where('category_id', '=', $worst->id)
            )->unionAll(
                DB::query()->selectRaw('count(*)')->from('days')->where('user_id', $user->id)->whereYear('date', $year)
            )
            ->get();

        return view('year-in-review.show', [
            'year' => $year,
            'gratefulDays' => $countQueries[0]->amount,
            'greatDays' => $countQueries[1]->amount,
            'goodDays' => $countQueries[2]->amount,
            'normalDays' => $countQueries[3]->amount,
            'badDays' => $countQueries[4]->amount,
            'worstDays' => $countQueries[5]->amount,
            'longestGreatDayStreak' => DB::select(DB::raw("SELECT COUNT(*) max_streak
  FROM
     ( SELECT x.*
            , CASE WHEN @prev = `date` - INTERVAL 1 DAY THEN @i:=@i ELSE @i:=@i+1 END i
            , @prev:=`date`
         FROM
            ( SELECT DISTINCT `date` FROM days WHERE user_id = :user_id AND year(`date`) = :year AND category_id = :category_id ) x
         JOIN
            ( SELECT @prev:=null,@i:=-1 ) vars
        ORDER BY `date`
     ) a
 GROUP BY i
 ORDER BY max_streak DESC LIMIT 1"), [
                'user_id' => auth()->user()->id,
                'category_id' => $great->id,
                'year' => $year
            ])[0]->max_streak,
            'bestMonth' => DB::select(DB::raw("SELECT count(*) as amount_days, category_id, MONTHNAME(`date`) as best_month
  FROM days where year(`date`) = :year and user_id = :user_id

 GROUP BY date_format(`date`, '%m'), category_id, best_month
 order by category_id asc, amount_days desc
  LIMIT 1"), [
                'user_id' => auth()->user()->id,
                'year' => $year
            ])[0]->best_month,
            'daysTracked' => $countQueries[6]->amount,
            'differentTagsUsed' => count($tagQuery),
            'overallTagUsage' => $overallTagUsage,
            'mostUsedTag' => $mostUsedTag,
            'mostUsedTagUsages' => $mostUsedTagUsage,
        ]);
    }
}
