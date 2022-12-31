@extends('layouts.app', [
    'title' => 'Year in review',
    'description' => 'Review your year.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Your {{ $year }} in review</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif


                            <div class="">
                                <div class="mycards stacked-cards stacked-cards-slide fs-3">
                                    <ul>
                                        <li>Let's have a look at {{ $year }}! <br>Swipe or click the next card to continue!</li>
                                        <li>On {{ $gratefulDays }} {{ Str::plural('day', $gratefulDays) }} you were grateful for something!</li>
                                        <li>You had {{ $greatDays }} great {{ Str::plural('day', $greatDays) }} in {{ $year }}!</li>
                                        <li>You had {{ $goodDays }} good {{ Str::plural('day', $goodDays) }}!</li>
                                        <li>You had {{ $normalDays }} average {{ Str::plural('day', $normalDays) }}!</li>
                                        <li>You had {{ $badDays }} bad {{ Str::plural('day', $badDays) }}!</li>
                                        <li>You had {{ $worstDays }} worst {{ Str::plural('day', $worstDays) }}!</li>

                                        <li>Your longest great day streak was {{ $longestGreatDayStreak }} {{ Str::plural('day', $longestGreatDayStreak) }} long!</li>
                                        <li>You had the most great days in {{ $bestMonth }}!</li>
                                        <li>You tracked a total of {{ $daysTracked }} {{ Str::plural('day', $daysTracked) }} in {{ $year }}!</li>
                                        <li>You used a total of {{ $differentTagsUsed }} tags in {{ $year }} for a total of {{ $overallTagUsage }} {{ Str::plural('usage', $overallTagUsage) }}!</li>
                                        @if($mostUsedTag)
                                        <li>Your most used tag was {{ $mostUsedTag }} for a total of {{ $mostUsedTagUsages }} {{ Str::plural('time', $mostUsedTagUsages) }}!</li>
                                        @endif

                                        <li>We wish you all the best for {{ $year + 1 }}!</li>
                                    </ul>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
