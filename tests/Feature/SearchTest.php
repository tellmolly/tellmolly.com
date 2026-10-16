<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_comments_gratitude_and_tags(): void
    {
        $this->seed(BaseSeeder::class);
        $categoryId = Category::where('order', Category::GOOD)->value('id');

        $user = User::factory()->create();
        $user->days()->create(['date' => '2026-01-01', 'category_id' => $categoryId, 'comment' => 'Went swimming']);
        $user->days()->create(['date' => '2026-01-02', 'category_id' => $categoryId, 'grateful_for' => 'A swimming lesson']);
        $tagged = $user->days()->create(['date' => '2026-01-03', 'category_id' => $categoryId]);
        $tagged->tags()->attach($user->tags()->create(['name' => 'Swimming', 'color' => '#2465bf', 'slug' => 'swimming']));
        $user->days()->create(['date' => '2026-01-04', 'category_id' => $categoryId, 'comment' => 'Went cycling']);
        User::factory()->create()->days()->create(['date' => '2026-01-05', 'category_id' => $categoryId, 'comment' => 'Swimming too']);

        $this->actingAs($user)->get('/days?search=swim')
            ->assertOk()
            ->assertSee('Days (3)')
            ->assertSee(['2026-01-01', '2026-01-02', '2026-01-03'])
            ->assertDontSee('2026-01-04')
            ->assertDontSee('2026-01-05');
    }
}
