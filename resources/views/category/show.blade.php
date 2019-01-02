@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Category: {{ $category->name }}
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul>
                            <li>
                                Name: {{ $category->name }}
                            </li>
                            <li>
                                Color: {{ $category->color }}
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
