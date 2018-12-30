@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Category</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('categories.update', $category->id) }}" method="post">
                            @method('PUT')
                            @csrf

                            @include('category.partials.edit')

                            <button type="submit" class="btn btn-primary">
                                Edit
                            </button>
                        </form>
                        <br>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="post">
                            @method('DELETE')
                            @csrf

                            <button type="submit" class="btn btn-danger">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
