<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LocalTimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
    }

    public function test_the_time_zone_is_picked_up_from_cloudflare(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withHeaders(['CF-IPCountry' => 'AT'])->get('/dashboard')->assertOk();

        $this->assertSame('Europe/Vienna', $user->fresh()->timezone);
    }

    public function test_a_chosen_time_zone_is_not_overwritten_by_the_location(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['timezone' => 'Asia/Tokyo', 'timezone_detect' => false])->save();

        $this->actingAs($user)->withHeaders(['CF-Timezone' => 'Europe/Vienna'])->get('/dashboard')->assertOk();

        $this->assertSame('Asia/Tokyo', $user->fresh()->timezone);
    }

    public function test_today_follows_the_users_time_zone(): void
    {
        // 23:30 UTC on the 16th is already 01:30 on the 17th in Vienna.
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(23, 30));
        $user = User::factory()->create();
        $user->forceFill(['timezone' => 'Europe/Vienna'])->save();

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('localToday', '2026-10-17')
            ->assertSee('name="date" value="2026-10-17"', false);

        $this->actingAs($user)->get('/days/create')->assertViewHas('day', fn($day) => $day->date === '2026-10-17');

        $user->days()->create(['date' => '2026-10-17', 'category_id' => Category::where('order', Category::GOOD)->value('id')]);
        $this->actingAs($user)->get('/today')->assertRedirect(route('days.edit', '2026-10-17'));
    }

    public function test_the_current_streak_counts_from_the_users_today(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(23, 30));
        $user = User::factory()->create();
        $user->forceFill(['timezone' => 'Europe/Vienna'])->save();
        $categoryId = Category::where('order', Category::GOOD)->value('id');
        foreach (['2026-10-17', '2026-10-16', '2026-10-15'] as $date) {
            $user->days()->create(['date' => $date, 'category_id' => $categoryId]);
        }

        $this->actingAs($user)->get('/dashboard')
            ->assertViewHas('currentStreak', fn($streak) => $streak[0]->streak === 3);
    }

    public function test_web_and_api_use_count_as_activity(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(12, 0));
        $user = User::factory()->create();
        $user->forceFill(['last_active_at' => now()->subDays(100)])->save();

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->assertTrue($user->fresh()->last_active_at->isSameDay(now()));

        $apiUser = User::factory()->create();
        $apiUser->forceFill(['last_active_at' => now()->subDays(100)])->save();
        Sanctum::actingAs($apiUser);

        $this->getJson('/api/dashboard')->assertOk();
        $this->assertTrue($apiUser->fresh()->last_active_at->isSameDay(now()));
    }
}
