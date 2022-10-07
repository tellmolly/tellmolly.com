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
use Illuminate\Support\Facades\DB;

class DayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Day::class, 'day');
    }

    public function index(Request $request): View
    {
        return view('day.index', [
            'isSearch' => $request->get('search') !== null,
            'days' => $request->user()->days()->with('category')->search($request->get('search'))->orderByDesc('date')->paginate(30)->withQueryString(),
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
        'user_id' => auth()->user()->id
            ])
        ]);
    }

    public function create(Request $request): View
    {
        return view('day.create', [
            'day' => new Day(['date' => date('Y-m-d')]),
            'categories' => Category::all(),
            'tags' => $request->user()->tags
        ]);
    }

    public function store(DayStoreRequest $request): RedirectResponse
    {
        $day = new Day();
        $day->fill($request->validated());

        $request->user()->days()->save($day);

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function edit(Request $request, Day $day): View
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => Category::all(),
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
