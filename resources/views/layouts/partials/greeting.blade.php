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
@endauth
