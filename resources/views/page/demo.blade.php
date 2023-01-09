@extends('layouts.app', [
    'title' => 'Demo - Try Tell Molly',
    'description' => 'Check out Tell Molly using the demo account. Find out if it is right for you. '
])

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Demo - Try Tell Molly</h1>
                <p class="card-text">Give Tell Molly a try using the demo mode. The demo is reset every hour. </p>
                <p class="card-text">The demo lets you try all features before you sign up. </p>
                <form method="post" action="{{ route('demo.login') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Try Tell Molly</button>
                </form>
            </div>
        </div>
    </div>
@endsection
