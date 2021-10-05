@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Day: {{ $day->date }}
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p>
                            <a href="{{ route('days.edit', $day->id) }}">Edit</a>
                        </p>

                        <ul>
                            <li>
                                Date: {{ date('l, Y-m-d', strtotime($day->date)) }}
                            </li>
                            <li>
                                Comment: {{ $day->comment }}
                            </li>
                            <li>
                                Category: {{ $day->category->name }}
                            </li>
                            @if($day->tags->isNotEmpty())
                            <li>
                                Tags: <ul>
                                    @foreach ($day->tags as $tag)
                                        <li><a href="{{ route('tags.show', $tag) }}">{{ $tag->name }}</a></li>
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
