<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignupProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['honeypot.enabled' => true]);
    }

    private function form(array $overrides = [], int $shownSecondsAgo = 10): array
    {
        return $overrides + [
            'name' => 'Real Person',
            'email' => 'person@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
            'username' => '',
            'form_shown_at' => encrypt(now()->getTimestamp() - $shownSecondsAgo),
        ];
    }

    public function test_the_form_carries_the_hidden_fields(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('name="username"', false)
            ->assertSee('name="form_shown_at"', false);
    }

    public function test_a_person_can_register(): void
    {
        $this->post('/register', $this->form())->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'person@example.com']);
    }

    public function test_bots_that_fill_the_hidden_field_are_ignored(): void
    {
        $this->post('/register', $this->form(['username' => 'bot']))->assertOk()->assertContent('');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_posts_without_the_timestamp_or_with_a_forged_one_are_ignored(): void
    {
        $form = $this->form();
        unset($form['form_shown_at']);
        $this->post('/register', $form)->assertContent('');

        $this->post('/register', $this->form(['form_shown_at' => (string) (now()->getTimestamp() - 60)]))->assertContent('');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_sending_the_form_too_quickly_is_refused_with_a_message(): void
    {
        $this->from('/register')->post('/register', $this->form([], 1))
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', 'person@example.com')
            ->assertSessionMissing('_old_input.password');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registrations_are_rate_limited_per_address(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/register', $this->form(['email' => "person$i@example.com"]))->assertRedirect();
            Auth::logout();
        }

        $this->post('/register', $this->form(['email' => 'person6@example.com']))->assertTooManyRequests();
        $this->assertDatabaseCount('users', 5);
    }

    public function test_the_api_registers_unconfirmed_accounts_and_is_rate_limited(): void
    {
        Notification::fake();

        $tokens = [];
        for ($i = 1; $i <= 5; $i++) {
            $tokens[] = $this->postJson('/api/register', $this->form(['email' => "app$i@example.com"]))->assertOk()->json('token');
            Auth::logout();
        }
        $this->postJson('/api/register', $this->form(['email' => 'app6@example.com']))->assertTooManyRequests();

        $this->assertDatabaseCount('users', 5);
        $user = User::where('email', 'app1@example.com')->first();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        // Until the address is confirmed, the token only reaches the account itself.
        $this->withToken($tokens[0])->getJson('/api/dashboard')->assertForbidden();
        $this->withToken($tokens[0])->getJson('/api/profile')->assertOk();
    }

    public function test_unconfirmed_accounts_are_deleted_after_a_week(): void
    {
        $old = User::factory()->unverified()->create(['created_at' => now()->subDays(8)]);
        $recent = User::factory()->unverified()->create(['created_at' => now()->subDays(6)]);
        $confirmed = User::factory()->create(['created_at' => now()->subDays(400)]);

        $this->artisan('tm:delete-unverified-users')->expectsOutput('Deleted 1 unconfirmed account')->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
        $this->assertModelExists($confirmed);
    }

    public function test_the_confirmation_page_mentions_the_deadline(): void
    {
        $this->actingAs(User::factory()->unverified()->create())->get('/verify-email')
            ->assertSee('Accounts that are not confirmed within 7 days are deleted.');
    }
}
