<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YearTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
    }

    private function categoryId(int $order): int
    {
        return Category::where('order', $order)->value('id');
    }

    public function test_year_pages_default_to_the_current_year(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16));
        $user = User::factory()->create();

        $this->actingAs($user)->get('/year')->assertOk()->assertViewHas('year', 2026);
        $this->actingAs($user)->get('/year/months')->assertOk()->assertViewHas('year', 2026);
    }

    public function test_year_in_review_covers_the_current_year_in_december(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 28));

        $this->actingAs(User::factory()->create())->get('/year-in-review')
            ->assertOk()
            ->assertViewHas('year', 2026);
    }

    public function test_year_in_review_covers_the_previous_year_in_january(): void
    {
        $this->travelTo(now()->setDate(2027, 1, 5));

        $this->actingAs(User::factory()->create())->get('/year-in-review')
            ->assertOk()
            ->assertViewHas('year', 2026);
    }

    public function test_year_in_review_sums_up_the_year(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 28));
        $user = User::factory()->create();
        $great = $this->categoryId(Category::GREAT);
        $tag = $user->tags()->create(['name' => 'Hiking', 'color' => '#2465bf', 'slug' => 'hiking']);

        foreach (['2026-05-01', '2026-05-02', '2026-05-03', '2026-03-10'] as $date) {
            $user->days()->create(['date' => $date, 'category_id' => $great])->tags()->attach($tag);
        }
        $user->days()->create(['date' => '2026-03-11', 'category_id' => $this->categoryId(Category::BAD), 'grateful_for' => 'Tea']);
        // Last year's entries must not count.
        $user->days()->create(['date' => '2025-05-05', 'category_id' => $great, 'grateful_for' => 'Old']);

        $this->actingAs($user)->get('/year-in-review')
            ->assertOk()
            ->assertViewHas('daysTracked', 5)
            ->assertViewHas('greatDays', 4)
            ->assertViewHas('badDays', 1)
            ->assertViewHas('gratefulDays', 1)
            ->assertViewHas('longestGreatDayStreak', 3)
            ->assertViewHas('bestMonth', 'May')
            ->assertViewHas('mostUsedTag', 'Hiking')
            ->assertViewHas('mostUsedTagUsages', 4)
            ->assertSee('You had the most great days in May!');
    }

    public function test_year_in_review_skips_the_best_month_without_great_days(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 28));
        $user = User::factory()->create();
        $user->days()->create(['date' => '2026-06-01', 'category_id' => $this->categoryId(Category::GOOD)]);

        $this->actingAs($user)->get('/year-in-review')
            ->assertOk()
            ->assertViewHas('bestMonth', '-')
            ->assertDontSee('You had the most great days in');
    }
}
