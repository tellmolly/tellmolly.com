<p>Sign out of Tell Molly on every other browser, phone and app. You stay signed in here.</p>

<form method="post" action="{{ route('profile.logout-other-devices') }}">
    @csrf

    @if (session('status') === 'other-devices-logged-out')
        <div class="alert alert-success" role="alert">
            Signed out everywhere else.
        </div>
    @endif

    <div class="mb-3">
        <label for="other_devices_password" class="form-label">Password</label>
        <input type="password" class="form-control {{ $errors->otherDevices->has('password') ? ' is-invalid ' : '' }}" id="other_devices_password" name="password" placeholder="Password" required autocomplete="current-password">
        @if($errors->otherDevices->has('password'))
            <div class="invalid-feedback">
                {{ $errors->otherDevices->first('password') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        Sign out everywhere else
    </button>
</form>
