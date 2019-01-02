@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Day</div>

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
