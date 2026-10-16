<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_signing_out_everywhere_else_needs_the_password(): void
    {
        $user = User::factory()->create();
        $user->createToken('Phone');

        $this->actingAs($user)->post('/profile/logout-other-devices', ['password' => 'wrong'])
            ->assertSessionHasErrorsIn('otherDevices', 'password');

        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_signing_out_everywhere_else_revokes_tokens_and_old_sessions(): void
    {
        $user = User::factory()->create();
        $user->createToken('Phone');
        $user->createToken('Tablet');
        $oldHash = $user->password;

        $this->actingAs($user)->post('/profile/logout-other-devices', ['password' => 'secret'])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'other-devices-logged-out');

        $user->refresh();
        $this->assertSame(0, $user->tokens()->count());
        // Same password, new hash: sessions and remember-me cookies holding the old hash are rejected.
        $this->assertNotSame($oldHash, $user->password);
        $this->assertTrue(Hash::check('secret', $user->password));
        // This browser stays signed in.
        $this->get('/profile')->assertOk();
    }

    public function test_the_api_keeps_the_token_that_asked(): void
    {
        $user = User::factory()->create();
        $user->createToken('Old phone');
        $token = $user->createToken('This phone')->plainTextToken;

        $this->withToken($token)->postJson('/api/profile/logout-other-devices', ['password' => 'secret'])->assertOk();

        $this->assertSame(['This phone'], $user->tokens()->pluck('name')->all());
    }

    public function test_remember_me_is_checked_by_default(): void
    {
        $this->get('/login')->assertSee('name="remember" id="remember" checked', false);
    }

    public function test_remember_me_keeps_the_users_choice_after_a_failed_login(): void
    {
        User::factory()->create(['email' => 'someone@example.com']);

        $this->from('/login')->followingRedirects()
            ->post('/login', ['email' => 'someone@example.com', 'password' => 'wrong'])
            ->assertDontSee('name="remember" id="remember" checked', false);
    }

    public function test_a_too_long_comment_is_refused_and_kept_in_the_form(): void
    {
        $this->seed(BaseSeeder::class);
        $user = User::factory()->create();
        $comment = str_repeat('a', 16001);

        $this->actingAs($user)->from('/days/create')->post('/days', [
            'date' => '2026-10-16',
            'category_id' => Category::where('order', Category::GOOD)->value('id'),
            'comment' => $comment,
        ])->assertSessionHasErrors('comment')->assertSessionHasInput('comment', $comment);

        $this->assertSame(0, $user->days()->count());
    }

    public function test_a_comment_at_the_limit_is_saved_whole_even_with_windows_line_breaks(): void
    {
        $this->seed(BaseSeeder::class);
        $user = User::factory()->create();
        // 16000 characters as the browser counts them; submitted with \r\n line breaks.
        $comment = str_repeat("abc\r\n", 4000);

        $this->actingAs($user)->post('/days', [
            'date' => '2026-10-16',
            'category_id' => Category::where('order', Category::GOOD)->value('id'),
            'comment' => $comment,
        ])->assertSessionHasNoErrors();

        // Trailing whitespace is trimmed from all input, as everywhere in the app.
        $this->assertSame(rtrim(str_repeat("abc\n", 4000)), $user->days()->first()->comment);
    }

    public function test_the_comment_field_shows_the_limit(): void
    {
        $this->seed(BaseSeeder::class);

        $this->actingAs(User::factory()->create())->get('/days/create')
            ->assertSee('maxlength="16000"', false)
            ->assertSee('id="comment-Remaining"', false);
    }

    public function test_tag_colors_must_be_hex(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tags', ['name' => 'Work', 'color' => '#0;b:u('])->assertSessionHasErrors('color');
        $this->actingAs($user)->post('/tags', ['name' => 'Work', 'color' => '#2465bf'])->assertSessionHasNoErrors();
    }

    public function test_generated_tag_colors_are_always_full_hex(): void
    {
        foreach (['a', 'Hiking', 'x', 'Family', '42'] as $name) {
            $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', Tag::colorFor($name));
        }
    }
}
