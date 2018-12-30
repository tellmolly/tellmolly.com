@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create Category</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('categories.store') }}" method="post">
                            @method('POST')
                            @csrf

                            @include('category.partials.edit')

                            <button type="submit" class="btn btn-primary">
                                Create
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
