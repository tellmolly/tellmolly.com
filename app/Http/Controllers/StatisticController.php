<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Category;
use App\Models\Day;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        return view('statistic.index', [
            'lastWeek' => $request->user()->days()->with('category')->where('date', '=', now()->subWeek()->format('Y-m-d'))->first(),
            'onThisDay' => $request->user()->days()->with('category')->where('date', '=', now()->subYear()->format('Y-m-d'))->first(),
            'currentStreak' => DB::select(DB::raw("SELECT MAX(CAST(streak AS SIGNED)) AS streak
                    FROM (
                    SELECT id, user_id, `date`, DATEDIFF(DATE(NOW()), `date`),
                     @streak := IF(DATEDIFF(DATE(NOW()), `date`) - @days_diff > 1, @streak,
                     IF(@days_diff := DATEDIFF(DATE(NOW()), `date`), @streak+1, @streak+1)) AS streak
                    FROM days CROSS
                    JOIN (
                    SELECT @streak := 0, @days_diff := 0) AS vars
                    WHERE user_id = :user_id AND `date` <= DATE(NOW())
                    ORDER BY `date` DESC) AS t"), [
                'user_id' => auth()->user()->id
            ]),
            'longestStreak' => DB::select(DB::raw("SELECT COUNT(*) max_streak
  FROM
     ( SELECT x.*
            , CASE WHEN @prev = `date` - INTERVAL 1 DAY THEN @i:=@i ELSE @i:=@i+1 END i
            , @prev:=`date`
         FROM
            ( SELECT DISTINCT `date` FROM days WHERE user_id = :user_id ) x
         JOIN
            ( SELECT @prev:=null,@i:=0 ) vars
        ORDER BY `date`
     ) a
 GROUP BY i
 ORDER BY max_streak DESC LIMIT 1"), [
                'user_id' => auth()->user()->id,
            ]),
            'greatDays' => $request->user()->days()->where('category_id', '=', 1)->count(),
            'goodDays' => $request->user()->days()->where('category_id', '=', 2)->count(),
            'averageDays' => $request->user()->days()->where('category_id', '=', 3)->count(),
            'greatDays1Month' => $request->user()->days()->where('category_id', '=', 1)->where('date', '>', now()->subMonth()->format('Y-m-d'))->count(),
            'goodDays1Month' => $request->user()->days()->where('category_id', '=', 2)->where('date', '>', now()->subMonth()->format('Y-m-d'))->count(),
            'averageDays1Month' => $request->user()->days()->where('category_id', '=', 3)->where('date', '>', now()->subMonth()->format('Y-m-d'))->count(),
        ]);
    }
}
