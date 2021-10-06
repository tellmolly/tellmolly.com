@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        Days
                        <form action="{{ route('search') }}" method="post" class="d-inline">
                            @csrf
                            <label for="search" class="sr-only">Search</label>
                            <input type="search" id="search" name="search" class="form-control  form-control-sm" placeholder="Search">
                        </form>
                    </div>

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
                                    {{ $day->date }}, {{ DateTime::createFromFormat('Y-m-d', $day->date)->format('l') }}
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
