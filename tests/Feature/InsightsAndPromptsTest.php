<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Day;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsightsAndPromptsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
    }

    /**
     * @param int[] $orders one mood per tagged day
     */
    private function tagDays(User $user, string $name, array $orders, int $offset): void
    {
        $tag = $user->tags()->create(['name' => $name, 'color' => '#2465bf']);
        foreach ($orders as $i => $order) {
            $user->days()->create([
                'date' => now()->subDays($offset + $i)->format('Y-m-d'),
                'category_id' => Category::where('order', $order)->value('id'),
            ])->tags()->attach($tag);
        }
    }

    public function test_tags_that_come_with_good_days_are_highlighted(): void
    {
        $user = User::factory()->create();
        $this->tagDays($user, 'Hiking', [1, 1, 2, 2, 1, 3], 1);           // 5 of 6 good
        $this->tagDays($user, 'Commute', [4, 5, 4, 3, 2], 20);            // mostly bad
        $this->tagDays($user, 'Gym', [1, 2], 40);                         // too few days
        $this->tagDays(User::factory()->create(), 'Someone else', [1, 1, 1, 1, 1], 1);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('What your good days have in common')
            ->assertSee('Hiking')
            ->assertSee('5 of 6 days were good or great')
            ->assertDontSee('Commute')
            ->assertDontSee('Someone else')
            ->assertViewHas('tagInsights', fn($insights) => count($insights) === 1);
    }

    public function test_no_insights_card_without_enough_data(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')
            ->assertOk()
            ->assertDontSee('What your good days have in common');
    }

    public function test_gratitude_prompts_vary_by_day_and_stay_put_for_a_day(): void
    {
        $prompts = config('gratitude.prompts');
        $seen = [];
        foreach (range(1, 30) as $d) {
            $prompt = (new Day(['date' => sprintf('2026-06-%02d', $d)]))->gratitudePrompt();
            $this->assertContains($prompt, $prompts);
            $seen[$prompt] = true;
        }

        $this->assertGreaterThan(3, count($seen));
        $this->assertSame(
            (new Day(['date' => '2026-06-01']))->gratitudePrompt(),
            (new Day(['date' => '2026-06-01']))->gratitudePrompt()
        );
    }

    public function test_the_day_form_uses_the_prompt(): void
    {
        $prompt = (new Day(['date' => '2026-06-01']))->gratitudePrompt();

        $this->actingAs(User::factory()->create())->get('/days/create?initial=2026-06-01')
            ->assertOk()
            ->assertSee('placeholder="' . e($prompt) . '"', false);
    }
}
