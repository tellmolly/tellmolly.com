<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InactiveUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_active_through_the_api_are_not_deleted(): void
    {
        $apiUser = User::factory()->create();
        $apiUser->forceFill(['last_login_at' => now()->subDays(400), 'last_active_at' => now()->subDay()])->save();
        $gone = User::factory()->create();
        $gone->forceFill(['last_login_at' => now()->subDays(400), 'last_active_at' => now()->subDays(400)])->save();

        $this->artisan('tm:delete-inactive-users')->assertSuccessful();

        $this->assertModelExists($apiUser);
        $this->assertModelMissing($gone);
    }
}
