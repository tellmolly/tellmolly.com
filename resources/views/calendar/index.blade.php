@extends('layouts.app', [
    'title' => 'Calendar',
    'description' => 'Check our your memories using the calendar.'
])

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                Calendar
            </div>
            <div class="card-body">
                <div  id="calendar"></div>
            </div>
        </div>
    </div>
@endsection
