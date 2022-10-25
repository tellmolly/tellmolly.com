@extends('layouts.app', [
    'title' => 'New - Tags',
    'description' => 'Create a new tag to aid you in organizing your memories.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">New tag</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @include('tag.widgets.create_tag')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
