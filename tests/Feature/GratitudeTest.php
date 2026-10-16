<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GratitudeTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
        $this->categoryId = Category::where('order', Category::GOOD)->value('id');
    }

    public function test_the_jar_lists_what_the_user_was_grateful_for(): void
    {
        $user = User::factory()->create();
        $user->days()->create(['date' => '2026-03-01', 'category_id' => $this->categoryId, 'grateful_for' => 'Fresh bread']);
        $user->days()->create(['date' => '2026-03-02', 'category_id' => $this->categoryId, 'comment' => 'Nothing to be grateful for here']);
        User::factory()->create()->days()->create(['date' => '2026-03-03', 'category_id' => $this->categoryId, 'grateful_for' => 'Someone else']);

        $this->actingAs($user)->get('/gratitude')
            ->assertOk()
            ->assertSee('Gratitude jar (1)')
            ->assertSee('Fresh bread')
            ->assertDontSee('Nothing to be grateful for here')
            ->assertDontSee('Someone else');
    }

    public function test_an_empty_jar_explains_how_to_fill_it(): void
    {
        $this->actingAs(User::factory()->create())->get('/gratitude')
            ->assertOk()
            ->assertSee('Your jar is still empty');
    }

    public function test_the_jar_is_available_through_the_api(): void
    {
        $user = User::factory()->create();
        $user->days()->create(['date' => '2026-03-01', 'category_id' => $this->categoryId, 'grateful_for' => 'Fresh bread']);
        Sanctum::actingAs($user);

        $this->getJson('/api/gratitude')
            ->assertOk()
            ->assertJsonPath('days.total', 1)
            ->assertJsonPath('days.data.0.grateful_for', 'Fresh bread');
    }
}
