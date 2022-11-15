<p>Ensure your account is using a long, random password to stay secure.</p>

<form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('put')

    @if (session('status') == 'password-updated')
        <div class="alert alert-success" role="alert">
            Saved.
        </div>
    @endif

    <div class="mb-3">
        <label for="current_password" class="form-label">Current password</label>
        <input type="password" class="form-control {{ $errors->has('current_password') ? ' is-invalid ' : '' }}" id="current_password" name="current_password" placeholder="Current password" value="" required autocomplete="current-password">
        @if($errors->has('current_password'))
            <div class="invalid-feedback">
                {{ $errors->first('current_password') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New password</label>
        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid ' : '' }}" id="password" name="password" placeholder="New password" value="" required autocomplete="new-password">
        @if($errors->has('password'))
            <div class="invalid-feedback">
                {{ $errors->first('password') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid ' : '' }}" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" value="" required autocomplete="new-password">
        @if($errors->has('password_confirmation'))
            <div class="invalid-feedback">
                {{ $errors->first('password_confirmation') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        Save
    </button>
</form>
