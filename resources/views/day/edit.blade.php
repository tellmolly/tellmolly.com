@extends('layouts.app', [
    'title' => DateTime::createFromFormat('Y-m-d', $day->date)->format('Y-m-d, l') . ' - Days',
    'description' => 'Edit your memories to add new stories or to fix typos.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Edit day</span>
                        <span>
                            <a class="link-light" href="{{ route('days.previous', $day) }}">Previous</a>
                            <a class="link-light" href="{{ route('days.next', $day) }}">Next</a>
                        </span>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @include('day.widgets.edit_day')
                        <br>
                        @include('day.widgets.delete_day')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
