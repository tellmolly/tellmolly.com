<p >Update your account's profile information and email address.</p>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    @if (session('status') == 'profile-updated')
        <div class="alert alert-success" role="alert">
            Saved.
        </div>
    @endif
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            {{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid ' : '' }}" id="name" name="name" placeholder="Name" value="{{ old('name', $user->name) }}" required autocomplete="name">
        @if($errors->has('name'))
            <div class="invalid-feedback">
                {{ $errors->first('name') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">E-Mail</label>
        <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid ' : '' }}" id="email" name="email" placeholder="E-Mail" value="{{ old('email', $user->email) }}" required autocomplete="email">
        @if($errors->has('email'))
            <div class="invalid-feedback">
                {{ $errors->first('email') }}
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        Save
    </button>
</form>

@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
<p class="mt-3">Your email address is unverified.</p>

<form method="post" action="{{ route('verification.send') }}">
    @csrf

    <button type="submit" class="btn btn-primary">
        {{ __('Click here to re-send the verification email.') }}
    </button>
</form>
@endif
