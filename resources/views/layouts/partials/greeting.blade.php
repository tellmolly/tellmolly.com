@auth
    @foreach(config('greetings') as $key => $message)
        @if(date('m-d') == $key)
            <div class="container">
                <div class="alert alert-info" role="alert">
                    {{ $message }}
                </div>
            </div>
        @endif
    @endforeach

    @if(config('calendar.demo.email') == auth()->user()->email)
        <div class="container">
            <div class="alert alert-info" role="alert">
                You are using the demo mode! The data is reset periodically.
            </div>
        </div>
    @endif
@endauth
