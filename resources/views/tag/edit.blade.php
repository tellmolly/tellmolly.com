@extends('layouts.app', [
    'title' => $tag->name . ' - Tags',
    'description' => 'Edit your tags to suit your style.'
])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('home.partials.menu')

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit tag</div>

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

                @if($tag->days->isNotEmpty())
                    <div class="card mt-3">
                        <div class="card-header">Days ({{ $tag->days->count() }})</div>
                        <div class="list-group list-group-flush">
                            @foreach($tag->days as $day)
                                <a href="{{ route('days.edit', $day->id) }}" class="list-group-item list-group-item-action  d-flex justify-content-between align-items-center"
                                    ><span>{{ $day->date }}, {{ DateTime::createFromFormat('Y-m-d', $day->date)->format('l') }}
                                        @foreach($day->tags as $tag)
                                            <span class="badge " style="background-color: {{ $tag->color }}; color: {{ $tag->fontColor() }}">{{ $tag->name }}</span>
                                        @endforeach
                                    </span>
                                    <span>{!! $day->category->name !!}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
