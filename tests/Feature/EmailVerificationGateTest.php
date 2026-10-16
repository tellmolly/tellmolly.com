<?php

namespace Tests\Feature;

use App\Mail\DailyReminder;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\BaseSeeder::class);
    }

    public function test_new_users_get_a_confirmation_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Notification::assertSentTo(User::where('email', 'new@example.com')->first(), VerifyEmail::class);
    }

    public function test_unconfirmed_users_are_sent_to_the_confirmation_page(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('verification.notice'));
        $this->actingAs($user)->get('/days/create')->assertRedirect(route('verification.notice'));
        $this->actingAs($user)->get('/verify-email')->assertOk();
        // The account itself stays reachable, e.g. to fix a typo in the address.
        $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Confirm your email address to turn on daily reminders');
    }

    public function test_confirmed_users_use_the_app_as_before(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk();
    }

    public function test_unconfirmed_api_users_are_refused_with_a_way_to_resend(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')->assertForbidden();
        $this->getJson('/api/profile')->assertOk();
        $this->postJson('/api/email/verification-notification')->assertOk()->assertJson(['status' => 'verification-link-sent']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_the_resend_notice_is_shown(): void
    {
        Notification::fake();

        $this->actingAs(User::factory()->unverified()->create())
            ->from('/verify-email')
            ->followingRedirects()
            ->post('/email/verification-notification')
            ->assertSee('A fresh verification link has been sent to your email address.');
    }

    public function test_reminders_only_go_to_confirmed_addresses(): void
    {
        Mail::fake();
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(17, 2));
        foreach ([User::factory()->unverified()->create(), User::factory()->create()] as $user) {
            $user->forceFill(['timezone' => 'Europe/Vienna', 'reminder_enabled' => true])->save();
        }

        $this->artisan('tm:send-daily-reminders');

        Mail::assertSent(DailyReminder::class, 1);
    }

    public function test_existing_accounts_with_entries_are_marked_as_confirmed(): void
    {
        $writer = User::factory()->unverified()->create();
        DB::table('users')->where('id', $writer->id)->update(['created_at' => '2024-03-01 10:00:00']);
        $writer->days()->create(['date' => '2024-03-02', 'category_id' => Category::where('order', Category::GOOD)->value('id')]);
        $neverWrote = User::factory()->unverified()->create(['created_at' => '2024-05-01 10:00:00']);

        (require database_path('migrations/2026_10_17_000000_mark_existing_users_as_verified.php'))->up();

        $this->assertSame('2024-03-01 10:00:00', $writer->fresh()->email_verified_at->format('Y-m-d H:i:s'));
        $this->assertNull($neverWrote->fresh()->email_verified_at);

        // The next cleanup removes the account that never wrote anything.
        $this->artisan('tm:delete-unverified-users');
        $this->assertModelExists($writer);
        $this->assertModelMissing($neverWrote);
    }
}
