@extends('layouts.app', [
    'title' => 'New - Days',
    'description' => 'Create a new day entry to keep your memories safe.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">New day</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @include('day.widgets.create_day')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
