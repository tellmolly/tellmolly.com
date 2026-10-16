<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Email verification is required from now on. Existing accounts that have
 * written at least one day count as verified, so nobody who uses the app gets
 * locked out.
 *
 * Accounts that never wrote anything stay unverified on purpose: most are bot
 * sign-ups. They can still confirm their address, and otherwise the daily
 * tm:delete-unverified-users removes them.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->whereExists(fn($query) => $query->select(DB::raw(1))->from('days')->whereColumn('days.user_id', 'users.id'))
            ->update(['email_verified_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)')]);
    }

    public function down(): void
    {
        // Not reversible: there is no record of who was unverified before.
    }
};
