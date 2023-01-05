@extends('layouts.app', [
    'title' => 'Calendar',
    'description' => 'Check our your memories using the calendar.'
])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">

                <div class="d-flex align-items-center justify-content-between">
                    <h1>{{ $year }}</h1>
                    <span>
                        <a class="" href="{{ route('year-month.index', $year - 1) }}">Previous</a>
                        <a class="" href="{{ route('year-month.index', $year + 1) }}">Next</a>
                    </span>
                </div>

            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-4 g-4 year-month">
            @foreach(range(1, 12) as $month)
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            {{
                                match ($month) {
                                    1 => 'January',
                                    2 => 'February',
                                    3 => 'March',
                                    4 => 'April',
                                    5 => 'May',
                                    6 => 'June',
                                    7 => 'July',
                                    8 => 'August',
                                    9 => 'September',
                                    10 => 'October',
                                    11 => 'November',
                                    12 => 'December',
                                }
                             }}
                        </div>
                        <div class="card-body" {{--style="padding: 0"--}} >
                            <div id="calendar-{{ $month }}" style="border: none"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        window.tellmolly_year = {{ $year }}
        window.tellmolly_events = @json($days)
    </script>
@endsection
