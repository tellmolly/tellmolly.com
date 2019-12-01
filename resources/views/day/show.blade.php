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

                        <ul>
                            <li>
                                Date: {{ $day->date }}
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
                                        <li>{{ $tag->name }}</li>
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
