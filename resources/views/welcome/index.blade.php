@extends('layouts.app', [
    'description' => config('app.name') . ' is your personal diary and the easiest way to monitor your day-to-day well-being and mental health.'
])

@section('content')

    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-5 fw-bold text-logo">{{ config('app.name') }}</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4"><span class="text-logo">{{ config('app.name') }}</span> is your personal diary and the easiest way to monitor your day-to-day well-being and mental health. <br>
                Sign-up and let Molly know how your day went with just two clicks. </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 gap-3">Sign-up</a>
                <a href="{{ route('login') }}" type="button" class="btn btn-outline-secondary btn-lg px-4">Login</a>
            </div>
        </div>
    </div>

    <div class="container col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6">
                <img src="{{ asset('svgs/undraw_events_re_98ue.svg') }}" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
            </div>
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold lh-1 mb-3">Look back at your favorite memories!</h2>
                <p class="lead">Quickly search through your memories and dwell in the past. Jump directly to a specific day
                    or utilize the powerful search functionality to find your favorite memories.</p>
            </div>
        </div>
    </div>


    <div class="container col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold lh-1 mb-3">No more restless nights!</h1>
                <p class="lead">Tell Molly and stop worrying! Writing your worries down is the best way to process them and live a happy life. If there is something that's bothering you, just tell Molly and move on.</p>
            </div>
            <div class="col-10 col-sm-8 col-lg-6">
                <img src="{{ asset('svgs/undraw_source_code_re_wd9m.svg') }}" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
            </div>
        </div>
    </div>

    <div class="container col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6">
                <img src="{{ asset('svgs/undraw_gdpr_-3-xfb.svg') }}" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
            </div>
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold lh-1 mb-3">Your thoughts are safe!</h1>
                <p class="lead">Your thoughts are safe with Molly. With Tell Molly you can rest assured that no third-party has access to your memories. </p>
            </div>
        </div>
    </div>

@endsection
