@extends('layouts.app', [
    'title' => 'Daily reminders',
    'description' => 'Turn off daily reminder emails.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Daily reminders</div>

                    <div class="card-body">
                        @if($done)
                            <p class="card-text">Daily reminders are turned off for {{ $user->email }}. You can turn them back on in your profile at any time.</p>
                        @else
                            <p class="card-text">Stop sending daily reminder emails to {{ $user->email }}?</p>
                            <form method="post" action="{{ request()->fullUrl() }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Turn off reminders</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
