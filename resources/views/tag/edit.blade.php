@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Tag</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @include('tag.widgets.edit_tag')
                        <br>
                        @include('tag.widgets.delete_tag')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
