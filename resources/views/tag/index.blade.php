@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Tags</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ $tags->links() }}

                        <ul class="list-group">
                            @foreach($tags as $tag)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $tag->name }} <span class="badge bg-secondary">{{ $tag->days_count }}</span>
                                    </div>
                                    <span>
                                        <a href="{{ route('tags.show', $tag->id) }}">Show</a>
                                        <a href="{{ route('tags.edit', $tag->id) }}">Edit</a>
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
