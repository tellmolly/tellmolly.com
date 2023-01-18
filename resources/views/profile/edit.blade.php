@extends('layouts.app', [
    'title' => 'Profile',
    'description' => 'Update your e-mail address, change your name or delete your profile.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">{{ __('Profile information') }}</div>

                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">{{ __('Update password') }}</div>

                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">{{ __('Export data') }}</div>

                    <div class="card-body">
                        @include('profile.partials.export-days')
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">{{ __('Delete account') }}</div>

                    <div class="card-body">
                         @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
