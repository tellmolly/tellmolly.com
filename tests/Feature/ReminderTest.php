<?php

namespace Tests\Feature;

use App\Mail\DailyReminder;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
        Mail::fake();
        // 17:02 UTC is 19:02 in Vienna (CEST).
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(17, 2));
    }

    private function viennaUser(array $attributes = []): User
    {
        $user = User::factory()->create();
        $user->forceFill($attributes + ['timezone' => 'Europe/Vienna', 'reminder_enabled' => true])->save();

        return $user;
    }

    public function test_reminders_are_off_unless_the_user_turns_them_on(): void
    {
        $user = User::factory()->create()->fresh();
        $this->assertFalse($user->reminder_enabled);
        $this->assertSame('19:00:00', $user->reminder_time);

        $this->artisan('tm:send-daily-reminders')->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_a_reminder_goes_out_at_the_chosen_local_time_once_a_day(): void
    {
        $user = $this->viennaUser();

        $this->artisan('tm:send-daily-reminders');
        $this->artisan('tm:send-daily-reminders');

        Mail::assertSent(DailyReminder::class, 1);
        Mail::assertSent(DailyReminder::class, fn($mail) => $mail->hasTo($user->email));
    }

    public function test_no_reminder_before_the_time_or_long_after_it(): void
    {
        $this->viennaUser(['reminder_time' => '19:30:00']);
        $this->viennaUser(['reminder_time' => '17:00:00']);

        $this->artisan('tm:send-daily-reminders');

        Mail::assertNothingSent();
    }

    public function test_no_reminder_when_today_is_already_logged(): void
    {
        $user = $this->viennaUser();
        $user->days()->create(['date' => '2026-10-16', 'category_id' => Category::where('order', Category::GOOD)->value('id')]);

        $this->artisan('tm:send-daily-reminders');

        Mail::assertNothingSent();
    }

    public function test_the_reminder_time_is_in_the_users_own_time_zone(): void
    {
        // 19:00 in New York is 23:00 UTC, so nothing yet at 17:02 UTC.
        $this->viennaUser(['timezone' => 'America/New_York']);

        $this->artisan('tm:send-daily-reminders');
        Mail::assertNothingSent();

        $this->travelTo(now()->setTime(23, 1));
        $this->artisan('tm:send-daily-reminders');
        Mail::assertSent(DailyReminder::class, 1);
    }

    public function test_settings_can_be_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile/reminders', [
            'reminder_enabled' => '1',
            'reminder_time' => '21:15',
            'timezone' => 'Asia/Tokyo',
        ])->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertTrue($user->reminder_enabled);
        $this->assertSame('21:15:00', $user->reminder_time);
        $this->assertSame('Asia/Tokyo', $user->timezone);
        $this->assertFalse($user->timezone_detect);

        $this->actingAs($user)->patch('/profile/reminders', [
            'reminder_enabled' => '0',
            'reminder_time' => '21:15',
            'timezone' => 'auto',
        ]);

        $user->refresh();
        $this->assertFalse($user->reminder_enabled);
        $this->assertTrue($user->timezone_detect);
    }

    public function test_invalid_settings_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())->patch('/profile/reminders', [
            'reminder_time' => '25:00',
            'timezone' => 'Mars/Olympus',
        ])->assertSessionHasErrors(['reminder_time', 'timezone']);
    }

    public function test_the_profile_page_shows_the_settings(): void
    {
        $this->actingAs(User::factory()->create())->get('/profile')
            ->assertOk()
            ->assertSee('Send me a daily reminder')
            ->assertSee('value="19:00"', false)
            ->assertSee('<option value="auto" selected', false);
    }

    public function test_settings_are_available_through_the_api(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/profile/reminders')
            ->assertOk()
            ->assertJson(['reminder_enabled' => false, 'reminder_time' => '19:00', 'timezone_detect' => true]);

        $this->patchJson('/api/profile/reminders', ['reminder_enabled' => true, 'reminder_time' => '08:00', 'timezone' => 'Europe/Vienna'])
            ->assertOk()
            ->assertJson(['reminder_enabled' => true, 'reminder_time' => '08:00', 'timezone' => 'Europe/Vienna', 'timezone_detect' => false]);
    }

    public function test_the_email_link_turns_reminders_off_after_confirming(): void
    {
        $user = $this->viennaUser();
        $url = URL::signedRoute('reminders.unsubscribe', ['user' => $user->id], absolute: false);

        $this->get($url)->assertOk()->assertSee('Turn off reminders');
        $this->assertTrue($user->fresh()->reminder_enabled);

        $this->post($url)->assertOk()->assertSee('Daily reminders are turned off');
        $this->assertFalse($user->fresh()->reminder_enabled);
    }

    public function test_the_unsubscribe_link_needs_a_valid_signature(): void
    {
        $user = $this->viennaUser();

        $this->post('/reminders/unsubscribe/' . $user->id)->assertForbidden();
        $this->assertTrue($user->fresh()->reminder_enabled);
    }

    public function test_the_email_links_to_today_and_to_unsubscribing(): void
    {
        $mail = new DailyReminder($this->viennaUser());

        $mail->assertSeeInHtml(route('days.today'));
        $mail->assertSeeInHtml('reminders/unsubscribe/');
        $this->assertStringContainsString('reminders/unsubscribe/', $mail->headers()->text['List-Unsubscribe']);
    }
}
