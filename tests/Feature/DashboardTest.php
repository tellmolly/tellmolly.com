<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
    }

    private function category(int $order): Category
    {
        return Category::where('order', $order)->firstOrFail();
    }

    public function test_dashboard_asks_about_today_when_it_is_not_logged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('How was today?')
            // The smiley buttons carry a word for screen readers.
            ->assertSee('<span class="visually-hidden">Great</span>', false)
            ->assertSee('<span class="visually-hidden">Worst</span>', false);
    }

    public function test_dashboard_does_not_ask_about_today_once_it_is_logged(): void
    {
        $user = User::factory()->create();
        $user->days()->create(['date' => now()->format('Y-m-d'), 'category_id' => $this->category(Category::GOOD)->id]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('How was today?');
    }

    public function test_quick_logging_today_redirects_to_the_new_entry(): void
    {
        $user = User::factory()->create();
        $today = now()->format('Y-m-d');

        $this->actingAs($user)->post('/days', [
            'date' => $today,
            'category_id' => $this->category(Category::GREAT)->id,
            'quick' => 1,
        ])->assertRedirect(route('days.edit', $today));

        $this->assertDatabaseHas('days', ['user_id' => $user->id, 'date' => $today]);
    }

    public function test_creating_a_day_from_the_form_still_redirects_to_the_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/days', [
            'date' => now()->format('Y-m-d'),
            'category_id' => $this->category(Category::GREAT)->id,
        ])->assertRedirect(route('days.index'));
    }

    public function test_dashboard_shows_a_good_day_from_the_past(): void
    {
        $user = User::factory()->create();
        $user->days()->create([
            'date' => now()->subMonths(3)->format('Y-m-d'),
            'category_id' => $this->category(Category::GREAT)->id,
            'grateful_for' => 'A sunny walk by the lake',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Remember this day?')
            ->assertSee('A sunny walk by the lake');
    }

    public function test_dashboard_never_shows_a_bad_day_as_a_memory(): void
    {
        $user = User::factory()->create();
        foreach ([Category::AVERAGE, Category::BAD, Category::WORST] as $i => $order) {
            $user->days()->create([
                'date' => now()->subMonths(3 + $i)->format('Y-m-d'),
                'category_id' => $this->category($order)->id,
                'grateful_for' => 'Not a memory',
            ]);
        }

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Remember this day?')
            ->assertDontSee('Not a memory');
    }

    public function test_dashboard_does_not_show_recent_days_as_memories(): void
    {
        $user = User::factory()->create();
        $user->days()->create([
            'date' => now()->subDays(5)->format('Y-m-d'),
            'category_id' => $this->category(Category::GREAT)->id,
            'grateful_for' => 'Too recent',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Too recent');
    }

    public function test_dashboard_prefers_good_days_with_something_written_down(): void
    {
        $user = User::factory()->create();
        for ($i = 1; $i <= 10; $i++) {
            $user->days()->create([
                'date' => now()->subMonths(2)->subDays($i)->format('Y-m-d'),
                'category_id' => $this->category(Category::GOOD)->id,
            ]);
        }
        $user->days()->create([
            'date' => now()->subYear()->subDays(3)->format('Y-m-d'),
            'category_id' => $this->category(Category::GOOD)->id,
            'comment' => 'Dinner with friends',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Dinner with friends');
    }

    public function test_dashboard_does_not_show_other_users_days(): void
    {
        $user = User::factory()->create();
        User::factory()->create()->days()->create([
            'date' => now()->subMonths(3)->format('Y-m-d'),
            'category_id' => $this->category(Category::GREAT)->id,
            'grateful_for' => 'Someone else',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Someone else');
    }

    public function test_dashboard_celebrates_a_streak_milestone(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 7; $i++) {
            $user->days()->create(['date' => now()->subDays($i)->format('Y-m-d'), 'category_id' => $this->category(Category::GOOD)->id]);
        }
        // An older, longer streak, so this is a milestone but not a record.
        for ($i = 20; $i < 40; $i++) {
            $user->days()->create(['date' => now()->subDays($i)->format('Y-m-d'), 'category_id' => $this->category(Category::GOOD)->id]);
        }

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('currentStreak', fn($streak) => $streak[0]->streak === 7)
            ->assertViewHas('longestStreak', fn($streak) => $streak[0]->max_streak === 20)
            ->assertSee('One week in a row');
    }

    public function test_dashboard_welcomes_back_after_a_broken_streak(): void
    {
        $user = User::factory()->create();
        for ($i = 5; $i < 10; $i++) {
            $user->days()->create(['date' => now()->subDays($i)->format('Y-m-d'), 'category_id' => $this->category(Category::GOOD)->id]);
        }

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Welcome back!');
    }

    public function test_dashboard_sums_up_good_days(): void
    {
        $user = User::factory()->create();
        $user->days()->create(['date' => now()->subDays(3)->format('Y-m-d'), 'category_id' => $this->category(Category::GREAT)->id]);
        $user->days()->create(['date' => now()->subDays(90)->format('Y-m-d'), 'category_id' => $this->category(Category::GREAT)->id]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Your good days')
            ->assertViewHas('greatDays1Month', 1)
            ->assertViewHas('greatDays', 2);
    }
}
