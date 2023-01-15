@extends('layouts.app', [
    'title' => 'List - Tags',
    'description' => 'Review your tags.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Tags ({{ $tags->total() }})</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('tags.index') }}" method="get">
                            <select class="form-select mb-3" name="sort" aria-label="Tag order" onchange="submit()">
                                <option value="a-z" {{ $sortOrder == "a-z" ? ' selected ' : '' }}>Name [A-Z]</option>
                                <option value="z-a" {{ $sortOrder == "z-a" ? ' selected ' : '' }}>Name [Z-A]</option>
                                <option value="uses-asc" {{ $sortOrder == "uses-asc" ? ' selected ' : '' }}>Least uses</option>
                                <option value="uses-desc" {{ $sortOrder == "uses-desc" ? ' selected ' : '' }}>Most uses</option>
                            </select>
                        </form>

                        {{ $tags->links() }}

                        <div class="list-group mb-3">
                            @foreach($tags as $tag)
                                <a href="{{ route('tags.edit', $tag) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                   <span class="badge" style="background-color: {{ $tag->color }}; color: {{ $tag->fontColor() }}">{{ $tag->name }}</span> <span class="badge bg-secondary">{{ $tag->days_count }}</span>
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
