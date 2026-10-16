<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class StreakService
{
    private const MILESTONES = [
        7 => 'One week',
        14 => 'Two weeks',
        30 => 'One month',
        50 => '50 days',
        100 => '100 days',
        200 => '200 days',
        365 => 'One whole year',
        500 => '500 days',
        730 => 'Two whole years',
        1000 => '1000 days',
    ];

    /**
     * Number of consecutive logged days up to today. A streak that reaches
     * yesterday still counts, so it isn't lost before today has been logged.
     *
     * @param Collection<int, string> $dates Y-m-d dates
     * @param CarbonInterface|null $today "Now" in the user's time zone; defaults to the app's
     */
    public function current(Collection $dates, ?CarbonInterface $today = null): int
    {
        $logged = $dates->flip();
        $day = CarbonImmutable::parse(($today ?? CarbonImmutable::now())->format('Y-m-d'));

        if ( ! $logged->has($day->format('Y-m-d'))) {
            $day = $day->subDay();
        }

        $streak = 0;
        while ($logged->has($day->format('Y-m-d'))) {
            $streak++;
            $day = $day->subDay();
        }

        return $streak;
    }

    /**
     * Length of the longest run of consecutive dates.
     *
     * @param Collection<int, string> $dates Y-m-d dates
     */
    public function longest(Collection $dates): int
    {
        $longest = 0;
        $run = 0;
        $previous = null;

        foreach ($dates->unique()->sort() as $date) {
            $day = CarbonImmutable::parse($date);
            $run = $previous && $previous->addDay()->isSameDay($day) ? $run + 1 : 1;
            $longest = max($longest, $run);
            $previous = $day;
        }

        return $longest;
    }

    /**
     * An encouraging line about the current streak, or null when there is
     * nothing worth saying. A broken streak is framed as a fresh start.
     */
    public function message(int $current, int $longest): ?string
    {
        if (isset(self::MILESTONES[$current])) {
            return self::MILESTONES[$current] . ' in a row! Amazing work.';
        }

        if ($current >= 3 && $current === $longest) {
            return 'This is your longest streak ever. Keep it going!';
        }

        if ($current === 0 && $longest > 0) {
            return 'Welcome back! Log today to start a new streak.';
        }

        return null;
    }
}
