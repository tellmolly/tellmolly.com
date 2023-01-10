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
                You are using the demo mode! Data entered in demo mode is visible to all other demo users. Be sure not to enter personal information. The data is reset periodically.
            </div>
        </div>
    @endif
@endauth
