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
                    <div class="card-header">Let's review {{ $year }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif


                            <div class="">
                                <div class="container-fixed mycards stacked-cards stacked-cards-slide">
                                    <ul>
                                        <li>Let's review {{ $year }}! <br>Swipe or click the next card to continue!</li>
                                        <li>On {{ $gratefulDays }} {{ Str::plural('day', $gratefulDays) }} you were grateful for something!</li>
                                        <li>You had {{ $greatDays }} great {{ Str::plural('day', $greatDays) }} in {{ $year }}! @if($greatDays > 0) Amazing! @endif</li>
                                        <li>Look at that, you had {{ $goodDays }} good {{ Str::plural('day', $goodDays) }}!</li>
                                        <li>You had {{ $normalDays }} average {{ Str::plural('day', $normalDays) }}!</li>
                                        <li>You had {{ $badDays }} bad {{ Str::plural('day', $badDays) }}! @if($badDays > 0) Keep your head up! @else Nicely done! @endif</li>
                                        <li>@if($worstDays > 0) Oh no! @endif You had {{ $worstDays }} really bad {{ Str::plural('day', $worstDays) }}! @if($worstDays > 0) Don't let it get you down! @endif</li>

                                        <li>Your longest great day streak was {{ $longestGreatDayStreak }} {{ Str::plural('day', $longestGreatDayStreak) }} long!</li>
                                        <li>You had the most great days in {{ $bestMonth }}!</li>
                                        <li>You tracked a total of {{ $daysTracked }} {{ Str::plural('day', $daysTracked) }} in {{ $year }}!</li>
                                        <li>You used a total of {{ $differentTagsUsed }} tags for a total of {{ $overallTagUsage }} {{ Str::plural('usage', $overallTagUsage) }}!</li>
                                        @if($mostUsedTag)
                                        <li>&ldquo;{{ $mostUsedTag }}&rdquo; was your most used tag. <br>
                                            You used it {{ $mostUsedTagUsages }} {{ Str::plural('time', $mostUsedTagUsages) }}!</li>
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
