<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Same defaults as the database, so new models have them before a refresh.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'timezone_detect' => true,
        'reminder_enabled' => false,
        'reminder_time' => '19:00:00',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_active_at' => 'datetime',
        'timezone_detect' => 'boolean',
        'reminder_enabled' => 'boolean',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(Day::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * The user's own time zone: the last one detected from their location or
     * the one they picked, falling back to the app's.
     */
    public function localTimezone(): string
    {
        return $this->timezone ?? config('app.timezone');
    }

    /**
     * The current time where the user is. Use it for anything that depends on
     * what "today" is for them; stored timestamps stay in the app's time zone.
     */
    public function localNow(): CarbonImmutable
    {
        return CarbonImmutable::now($this->localTimezone());
    }

    public function localToday(): string
    {
        return $this->localNow()->format('Y-m-d');
    }
}
