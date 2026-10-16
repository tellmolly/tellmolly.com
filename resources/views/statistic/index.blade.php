@extends('layouts.app', [
    'title' => 'Dashboard',
    'description' => 'Review your memories.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                @unless($today)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">How was today?</h5>
                            <form method="POST" action="{{ route('days.store') }}" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="date" value="{{ $localToday }}">
                                <input type="hidden" name="quick" value="1">
                                @foreach($categories as $category)
                                    <button type="submit" name="category_id" value="{{ $category->id }}" class="btn btn-outline-primary flex-fill p-2" style="font-size: xx-large; border-radius: .5rem;"><x-mood :category="$category"/></button>
                                @endforeach
                            </form>
                        </div>
                    </div>
                @endunless

                <div class="card mb-3">
                    <div class="card-body">
                        @if($goodDay)
                            <p class="card-text text-muted mb-1">Remember this day?</p>
                            <h5 class="card-title">
                                <a href="{{ route('days.edit', $goodDay) }}" style="text-decoration: none">
                                    <x-mood :category="$goodDay->category"/> {{ DateTime::createFromFormat('Y-m-d', $goodDay->date)->format('l, j F Y') }}
                                </a>
                            </h5>
                            <p class="card-subtitle text-muted mb-2">{{ \Carbon\Carbon::parse($goodDay->date)->diffForHumans() }}</p>
                            @if($goodDay->grateful_for)
                                <p class="card-text">You were grateful for: {{ $goodDay->grateful_for }}</p>
                            @elseif($goodDay->comment)
                                <p class="card-text">{{ Str::limit($goodDay->comment, 200) }}</p>
                            @endif
                            @if($goodDay->tags->isNotEmpty())
                                <p class="card-text">
                                    @foreach($goodDay->tags as $tag)
                                        <span class="badge" style="background-color: {{ $tag->color }}; color: {{ $tag->fontColor() }}">{{ $tag->name }}</span>
                                    @endforeach
                                </p>
                            @endif
                            <a href="{{ route('statistic.index') }}" class="card-link">Show me another</a>
                        @else
                            <p class="card-text text-muted">Your great and good days will show up here as memories once they are a month old.</p>
                        @endif
                    </div>
                </div>

                @if($tagInsights)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">What your good days have in common</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach($tagInsights as $insight)
                                    <li class="{{ $loop->last ? '' : 'mb-2' }}">
                                        <span class="badge" style="background-color: {{ $insight['color'] }}; color: {{ (new \App\Models\Tag(['color' => $insight['color']]))->fontColor() }}">{{ $insight['name'] }}</span>
                                        {{ $insight['good'] }} of {{ $insight['total'] }} days were good or great
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

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

                @if($streakMessage)
                    <div class="alert alert-success mb-3" role="status">{{ $streakMessage }}</div>
                @endif

                <div class="row row-cols-2 g-4 mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    @if($lastWeek)
                                    <a href="{{ route('days.edit', $lastWeek) }}" style="text-decoration: none">
                                        <x-mood :category="$lastWeek->category"/>
                                    </a>
                                    @else
                                    No entry
                                    @endif
                                </h5>
                                <p class="card-text">Last {{ \Carbon\Carbon::parse($localToday)->subWeek()->format('l') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    @if($onThisDay)
                                        <a href="{{ route('days.edit', $onThisDay) }}" style="text-decoration: none">
                                            <x-mood :category="$onThisDay->category"/>
                                        </a>
                                    @else
                                        No entry
                                    @endif
                                </h5>
                                <p class="card-text">On this day {{ \Carbon\Carbon::parse($localToday)->subYear()->format('Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Your good days</h5>
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col"><span class="visually-hidden">Mood</span></th>
                                    <th scope="col" class="text-end">Last 30 days</th>
                                    <th scope="col" class="text-end">Overall</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    [\App\Models\Category::GREAT, 'Great', $greatDays1Month, $greatDays],
                                    [\App\Models\Category::GOOD, 'Good', $goodDays1Month, $goodDays],
                                    [\App\Models\Category::AVERAGE, 'Average', $averageDays1Month, $averageDays],
                                ] as [$order, $label, $lastMonth, $overall])
                                    <tr>
                                        <th scope="row" class="fw-normal"><span aria-hidden="true">{!! $categories->firstWhere('order', $order)?->name !!}</span> {{ $label }}</th>
                                        <td class="text-end">{{ $lastMonth }}</td>
                                        <td class="text-end">{{ $overall }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
