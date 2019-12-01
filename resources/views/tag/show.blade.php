@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Tag: {{ $tag->name }}
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul>
                            <li>
                                Name: {{ $tag->name }}
                            </li>
                            <li>
                                Color: {{ $tag->color }}
                            </li>
                            @if($tag->days->isNotEmpty())
                            <li>
                                Days: <ul>
                                @foreach($tag->days as $day)
                                    <li><a href="{{ route('days.show', $day->id) }}">{{ $day->date }}</a></li>
                                @endforeach
                                </ul>
                            </li>
                            @endif
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
