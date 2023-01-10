@extends('layouts.app', [
    'title' => 'Year in review',
    'description' => 'Review your year using the convenient year overview.'
])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>{{ $year }}</span>
                        <span>
                            <a class="link-light" href="{{ route('year.index', ['year' => $year - 1, 'tag' => request()->get('tag')]) }}">Previous</a>
                            <a class="link-light" href="{{ route('year.index', ['year' => $year + 1, 'tag' => request()->get('tag')]) }}">Next</a>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm year_overview">
                                <thead>
                                <tr>
                                    @foreach ($months as $month)
                                        <th class="year_overview__item" style="border: none;">
                                            <div class="year_overview__item__content">
                                                {{ $month }}
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach (range(1, 31)  as $dayOfMonth)
                                    <tr>
                                        @foreach ($months as $monthNumber => $month)
                                            @if (checkdate($monthNumber, $dayOfMonth, $year))
                                                @if ($days->where('date', $year . '-' . str_pad($monthNumber, 2, 0, STR_PAD_LEFT) . '-' . str_pad($dayOfMonth, 2, 0, STR_PAD_LEFT))->count())
                                                    <td class="year_overview__item" style="background-color: {{ $days->where('date', $year . '-' . str_pad($monthNumber, 2, "0", STR_PAD_LEFT) . '-' . str_pad($dayOfMonth, 2, "0", STR_PAD_LEFT))->first()->category->color }}">
                                                        <div class="year_overview__item__content" style="display: flex; position:relative;">
                                                            <a href="{{ route('days.edit', $days->where('date', $year . '-' . str_pad($monthNumber, 2, 0, STR_PAD_LEFT) . '-' . str_pad($dayOfMonth, 2, 0, STR_PAD_LEFT))->first()) }}"
                                                            class="stretched-link text-decoration-none">&nbsp;</a>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="year_overview__item">
                                                        <div class="year_overview__item__content">

                                                        </div>
                                                    </td>
                                                @endif
                                            @else
                                                @if ($monthNumber == 0)
                                                    <td style="border-left: none; border-bottom: none; border-top: none; background-color: unset !important;" class="year_overview__item">

                                                        <div class="year_overview__item__content">
                                                            {{ $dayOfMonth }}
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="year_overview__item" style="background-color: black;">
                                                        <div class="year_overview__item__content">

                                                        </div>
                                                    </td>
                                                @endif

                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Legend</div>

                    <div class="card-body">
                        <ul class="list-unstyled">
                            @foreach($categories as $category)
                                <li>
                                    <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $category->color }};">&nbsp;</span> {!!  $category->name !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
