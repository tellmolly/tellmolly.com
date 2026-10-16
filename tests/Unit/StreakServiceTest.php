<?php

namespace Tests\Unit;

use App\Services\StreakService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class StreakServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-16 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_current_streak_counts_back_from_today(): void
    {
        $this->assertSame(3, (new StreakService)->current(collect(['2026-10-16', '2026-10-15', '2026-10-14', '2026-10-12'])));
    }

    public function test_current_streak_survives_until_today_is_logged(): void
    {
        $this->assertSame(2, (new StreakService)->current(collect(['2026-10-15', '2026-10-14'])));
    }

    public function test_current_streak_is_broken_by_a_missed_day(): void
    {
        $this->assertSame(0, (new StreakService)->current(collect(['2026-10-14', '2026-10-13'])));
    }

    public function test_current_streak_ignores_future_days(): void
    {
        $this->assertSame(1, (new StreakService)->current(collect(['2026-10-18', '2026-10-17', '2026-10-16'])));
    }

    public function test_longest_streak_finds_the_longest_run_in_any_order(): void
    {
        $dates = collect(['2026-01-05', '2026-01-01', '2026-01-02', '2026-01-06', '2026-01-03', '2026-01-07', '2026-01-08']);

        $this->assertSame(4, (new StreakService)->longest($dates));
    }

    public function test_longest_streak_runs_across_month_and_year_boundaries(): void
    {
        $this->assertSame(3, (new StreakService)->longest(collect(['2025-12-31', '2026-01-01', '2026-01-02'])));
    }

    public function test_no_days_means_no_streaks(): void
    {
        $this->assertSame(0, (new StreakService)->current(collect()));
        $this->assertSame(0, (new StreakService)->longest(collect()));
    }

    public function test_messages(): void
    {
        $streaks = new StreakService;

        $this->assertStringContainsString('One week in a row', $streaks->message(7, 20));
        $this->assertStringContainsString('One week in a row', $streaks->message(7, 7));
        $this->assertStringContainsString('longest streak ever', $streaks->message(9, 9));
        $this->assertStringContainsString('Welcome back', $streaks->message(0, 12));
        $this->assertNull($streaks->message(0, 0));
        $this->assertNull($streaks->message(2, 2));
        $this->assertNull($streaks->message(5, 20));
    }
}
