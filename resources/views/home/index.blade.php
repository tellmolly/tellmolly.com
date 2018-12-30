@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul>
                            <li>
                                <a href="{{ route('days.create') }}">
                                    Create day
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('days.index') }}">
                                    Show days
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categories.create') }}">
                                    Create category
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categories.index') }}">
                                    Show categories
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
