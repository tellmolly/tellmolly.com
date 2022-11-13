@extends('layouts.app', [
    'title' => 'Dashboard',
    'description' => 'Review your memories.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="row row-cols-2 g-4 mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $longestStreak[0]->max_streak ?? 0 }}</h5>
                                <p class="card-text">Longest Streak</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $currentStreak[0]->streak ?? 0 }}</h5>
                                <p class="card-text">Current Streak</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-2 g-4 mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    @if($lastWeek)
                                    <a href="{{ route('days.edit', $lastWeek) }}" style="text-decoration: none">
                                        {!! $lastWeek->category->name !!}
                                    </a>
                                    @else
                                    No entry
                                    @endif
                                </h5>
                                <p class="card-text">Last {{ now()->subWeek()->format('l') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    @if($onThisDay)
                                        <a href="{{ route('days.edit', $onThisDay) }}" style="text-decoration: none">
                                            {!! $onThisDay->category->name !!}
                                        </a>
                                    @else
                                        No entry
                                    @endif
                                </h5>
                                <p class="card-text">On this day {{ now()->subYear()->format('Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
