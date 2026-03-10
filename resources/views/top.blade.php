@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3 mx-auto">
            <div class="text-center my-2">
                <h1 class="p-2">NHK番組表</h1>
                <h5 class="sab mt-2">総合1</h5>
                <form method="GET" action="{{ route('top') }}">

                <select name="date" class="d-block mt-2 form-select form-control">
                    <option value="" disabled {{ request('date') ? '' : 'selected' }}>
                        日付を選択してください
                    </option>
                    @for ($i = 0; $i <= 6; $i++)
                        @php $date = today()->addDays($i)->format('Y-m-d'); @endphp
                        <option value="{{ $date }}"
                            class="p-1 text-center"
                            {{ request('date') == $date ? 'selected' : '' }}>
                            {{ today()->addDays($i)->isoFormat('MM/DD (ddd)') }}
                        </option>
                    @endfor
                </select>

                <select name="area" class="d-block mt-1 form-select form-control">
                    <option value="" disabled {{ request('area') ? '' : 'selected' }}>
                        エリアを選択してください
                    </option>
                    @foreach($areas as $area)
                        <option value="{{ $area->area_code }}" class="text-center p-1"
                            {{ request('area') == $area->area_code ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>

                @if ($errors->any())
                    <div class="mt-1 alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit" class="btn btn-outline-warning mt-3">
                    番組表を見る
                </button>

                </form>
            </div>
        </div>
    </div>

    <section class="col-md-12">
        <hr>

        @php 
        //曜日判定用　formから渡された値
        $request_date = \Carbon\Carbon::parse($request->date);

        $dayClass = "";
        switch ($request_date->dayOfWeek) {
            case 0:
                $dayClass = 'sunday';
                break;
            case 6:
                $dayClass = 'saturday';
                break;
            default:
                $dayClass = 'freeday';
        }
        @endphp

        @if(request()->filled('date') && request()->filled('area'))
            <table class="table">
                <thead class="{{ $dayClass }} h6">
                    <tr class="align-middle text-center">
                        <th style="width:17%">{{ $request_date->isoFormat('YYYY-MM-DD (ddd)') }}<br><span class="d-block text-center">放送時間</span></th>
                        <th style="width:27%">番組タイトル</th>
                        <th style="width:12%">ジャンル</th>
                        <th style="width:33%">詳細</th>
                        <th style="width:10%">予約</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (data_get($programs, 'g1.publication', []) as $program )
                        <tr>
                            <td class="align-middle p-2 text-center">
                                {{ \Carbon\Carbon::parse($program['startDate'])->format('H:i') }}
                                ~
                                {{ \Carbon\Carbon::parse($program['endDate'])->format('H:i') }}
                            </td>
                            <td class="align-middle ps-6 p-2 ms-6">
                                {{ $program['identifierGroup']['tvSeriesName'] ?? $program['name']}}<br>
                                - {{ $program['identifierGroup']['tvEpisodeName'] ?? '' }}
                            </td>
                            <td class="align-middle text-center p-2">{{ data_get($program, 'identifierGroup.genre.0.name1', '-') }}</td>
                            <td class="align-middle description p-2">{{ $program['description'] }}</td>
                            <td class="align-middle text-center p-2">
                                @if(auth()->check())
                                    <form method="POST" action="#">
                                        @csrf
                                        <button class="btn btn-primary btn-sm">予約</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</div>
@endsection