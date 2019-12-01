@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Days</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ $days->onEachSide(2)->links() }}

                        <ul class="list-group">
                            @foreach($days as $day)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $day->date }}
                                    <span>
                                        <a href="{{ route('days.show', $day->id) }}">Show</a>
                                        <a href="{{ route('days.edit', $day->id) }}">Edit</a>
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        {{ $days->onEachSide(2)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
