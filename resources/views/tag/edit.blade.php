@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Tag</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @include('tag.widgets.edit_tag')
                        <br>
                        @include('tag.widgets.delete_tag')
                    </div>
                </div>

                @if($tag->days->isNotEmpty())
                    <div class="card mt-3">
                        <div class="card-header">Days</div>
                        <div class="card-body">
                            Days: <ul>
                                @foreach($tag->days as $day)
                                    <li><a href="{{ route('days.edit', $day->id) }}"
                                        >{{ $day->date }}, {{ DateTime::createFromFormat('Y-m-d', $day->date)->format('l') }}</a> {!! $day->category->name !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
