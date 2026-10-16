@extends('layouts.app', [
    'title' => 'Gratitude jar',
    'description' => 'Everything you have been grateful for.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Gratitude jar ({{ $days->total() }})</div>

                    <div class="card-body">
                        @if($days->isEmpty())
                            <p class="card-text text-muted">Your jar is still empty. When you log a day, add what you are grateful for and it will be collected here.</p>
                        @else
                            {{ $days->onEachSide(2)->links() }}

                            <div class="list-group mb-3">
                                @foreach($days as $day)
                                    <a href="{{ route('days.edit', $day) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                            <span>{{ DateTime::createFromFormat('Y-m-d', $day->date)->format('l, j F Y') }}</span>
                                            <span>{!! $day->category->name !!}</span>
                                        </div>
                                        {{ $day->grateful_for }}
                                    </a>
                                @endforeach
                            </div>

                            {{ $days->onEachSide(2)->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
