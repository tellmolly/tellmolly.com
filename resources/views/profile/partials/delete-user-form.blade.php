<p>Are you sure your want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>

<form method="post" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')

    <div class="mb-3">
        <label for="delete-password" class="form-label">Password</label>
        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid ' : '' }}" id="delete-password" name="password" placeholder="Password" value="" required autocomplete="current-password">
        @if($errors->has('password'))
            <div class="invalid-feedback">
                {{ $errors->first('password') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-danger">
        Delete account
    </button>
</form>
