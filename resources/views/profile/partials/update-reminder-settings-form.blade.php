@if (! $user->hasVerifiedEmail())
<p class="mb-0">Confirm your email address to turn on daily reminders. You can request a new confirmation link above.</p>
@else
<p>Get an email reminder to log your day. It is only sent if you have not logged the day yet.</p>

<form method="post" action="{{ route('profile.reminders.update') }}">
    @csrf
    @method('patch')

    @if (session('status') === 'reminders-updated')
        <div class="alert alert-success" role="alert">
            Saved.
        </div>
    @endif

    <div class="form-check form-switch mb-3">
        <input type="hidden" name="reminder_enabled" value="0">
        <input class="form-check-input" type="checkbox" role="switch" id="reminder_enabled" name="reminder_enabled" value="1" {{ old('reminder_enabled', $user->reminder_enabled) ? 'checked' : '' }}>
        <label class="form-check-label" for="reminder_enabled">Send me a daily reminder</label>
    </div>

    <div class="mb-3">
        <label for="reminder_time" class="form-label">Remind me at</label>
        <input type="time" class="form-control {{ $errors->has('reminder_time') ? ' is-invalid ' : '' }}" id="reminder_time" name="reminder_time" value="{{ old('reminder_time', substr($user->reminder_time, 0, 5)) }}" required>
        @if($errors->has('reminder_time'))
            <div class="invalid-feedback">
                {{ $errors->first('reminder_time') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="timezone" class="form-label">Time zone</label>
        <select class="form-select {{ $errors->has('timezone') ? ' is-invalid ' : '' }}" id="timezone" name="timezone" aria-describedby="timezone-help">
            @php($selectedTimezone = old('timezone', $user->timezone_detect ? 'auto' : $user->localTimezone()))
            <option value="auto" {{ $selectedTimezone === 'auto' ? 'selected' : '' }}>Automatic (my location)</option>
            @foreach(DateTimeZone::listIdentifiers() as $timezone)
                <option value="{{ $timezone }}" {{ $selectedTimezone === $timezone ? 'selected' : '' }}>{{ str_replace('_', ' ', $timezone) }}</option>
            @endforeach
        </select>
        <div id="timezone-help" class="form-text">
            Used for what counts as "today" and for the reminder time. Currently {{ str_replace('_', ' ', $user->localTimezone()) }}, where it is {{ $user->localNow()->format('H:i') }}.
        </div>
        @if($errors->has('timezone'))
            <div class="invalid-feedback">
                {{ $errors->first('timezone') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        Save
    </button>
</form>
@endif
