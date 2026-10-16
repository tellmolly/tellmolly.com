<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Last time zone the user was seen in, or the one they picked.
            $table->string('timezone', 64)->nullable()->default(null)->after('last_login_at');
            // Keep following the user's location instead of a fixed time zone.
            $table->boolean('timezone_detect')->default(true)->after('timezone');
            // Opt-in only: nobody gets reminders until they turn them on.
            $table->boolean('reminder_enabled')->default(false)->after('timezone_detect');
            $table->time('reminder_time')->default('19:00:00')->after('reminder_enabled');
            // The user's local date of the last reminder, so each day gets at most one.
            $table->date('reminded_on')->nullable()->default(null)->after('reminder_time');
            // Any use of the app, including API tokens; inactive-user cleanup goes by this.
            $table->timestamp('last_active_at')->nullable()->default(null)->after('reminded_on');
        });

        // Start from the last sign-in, then count API token use made since.
        DB::table('users')->update(['last_active_at' => DB::raw('last_login_at')]);

        DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->whereNotNull('last_used_at')
            ->groupBy('tokenable_id')
            ->select('tokenable_id', DB::raw('MAX(last_used_at) as last_used_at'))
            ->get()
            ->each(function ($token) {
                DB::table('users')
                    ->where('id', $token->tokenable_id)
                    ->where(fn($query) => $query->whereNull('last_active_at')->orWhere('last_active_at', '<', $token->last_used_at))
                    ->update(['last_active_at' => $token->last_used_at]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'timezone_detect', 'reminder_enabled', 'reminder_time', 'reminded_on', 'last_active_at']);
        });
    }
};
