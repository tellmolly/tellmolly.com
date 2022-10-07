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

                        <div class="list-group">
                            @foreach($tags as $tag)
                                <a href="{{ route('tags.edit', $tag->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                   {{ $tag->name }} <span class="badge bg-secondary">{{ $tag->days_count }}</span>
                                </a>
                            @endforeach
                        </div>

                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
