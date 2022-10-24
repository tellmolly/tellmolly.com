@extends('layouts.app')

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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Days ({{ $days->total() }})</span>
                        <form class="d-inline" method="post" action="{{ route('days.jump') }}">
                            @csrf
                            <div class="input-group input-group-sm ">
                                <input type="date" class="form-control" name="jump" placeholder="Desired date" aria-label="Desired date to jump to" aria-describedby="button-travel">
                                <button class="btn btn-secondary" type="submit" id="button-travel">Go!</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            @if($days->isEmpty())
                                @if($isSearch)
                                    <img src="{{ asset('svgs/undraw_void_-3-ggu.svg') }}" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="280" height="200">
<h2>No results</h2>
                                    <p>Try changing your search term.</p>

                                    @else
                                    <img src="{{ asset('svgs/undraw_events_re_98ue.svg') }}" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="280" height="200">


                                    @endif
                                @else

                        {{ $days->onEachSide(2)->links() }}

                        <div class="list-group">
                            @foreach($days as $day)
                                <a href="{{ route('days.edit', $day->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
<span>                                    {{ $day->date }}, {{ DateTime::createFromFormat('Y-m-d', $day->date)->format('l') }}
                                    @foreach($day->tags as $tag)
        <span class="badge " style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                                    @endforeach
    </span>
                                   <span>
{!! $day->category->name !!}
                                   </span>
                                </a>
                            @endforeach
                        </div>

                        {{ $days->onEachSide(2)->links() }}
                            @endempty
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
