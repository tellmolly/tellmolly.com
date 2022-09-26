@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">Quick create day</div>

                    <div class="card-body">
                        @include('day.widgets.create_day', [
                            'day' => new \App\Models\Day(['date' => date('Y-m-d')]),
                            'categories' => \App\Models\Category::all(),
                            'tags' => \App\Models\Tag::all()
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
