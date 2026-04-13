@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3 mx-auto">
            <div class="text-center my-2">
                <h1 class="p-2">NHK番組表</h1>
                <h5 class="sab mt-2">総合 1</h5>
                <form method="GET" action="{{ route('top') }}">

                <select name="date" class="d-block mt-3 form-select form-control">
                    @for ($i = 0; $i <= 6; $i++)
                        @php $date = today()->addDays($i)->format('Y-m-d'); @endphp
                        <option value="{{ $date }}"
                            class="p-1 text-center"
                            {{ old('date', request('date')) == $date ? 'selected' : '' }}>
                            {{ today()->addDays($i)->isoFormat('MM/DD (ddd)') }}
                        </option>
                    @endfor
                </select>

                <select name="area" class="d-block mt-1 form-select form-control">
                    <option value="" {{ request('area') ? '' : 'selected' }}>
                        エリアを選択してください
                    </option>
                    @foreach($areas as $area)
                        <option value="{{ $area->area_code }}" class="text-center p-1"
                            {{ request('area') == $area->area_code ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>

                {{-- フラッシュメッセージの表示 --}}
                @if (session('message'))
                    <div class="mt-2 text-center alert alert-{{ session('type', 'info') }}">
                        {!! nl2br(e(session('message'))) !!}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-2 alert alert-danger text-start">
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
                        <th class="auth" style="width:10%">予約<br>
                            @if(!auth()->check())
                                <small class="auth ">※認証が必要です</small>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (data_get($programs, 'g1.publication', []) as $program )
                        @php
                            $programId = $program['identifierGroup']['tvSeriesId'] ?? '';
                            $programStart = \Carbon\Carbon::parse($program['startDate'])->format('H:i');
                            $programStartDate = \Carbon\Carbon::parse($program['startDate'])->format('Y:m:d H:i');
                            // 予約済みか判定
                            $isReserved = $reservedReservations->contains(function($reservation) use ($programId, $programStart) {
                                return $reservation->nhk_tvEpisodeId === $programId
                                    && \Carbon\Carbon::parse($reservation->start_time)->format('H:i') === $programStart;
                            });
                            //より厳密な予約済かを判定
                            $isReservedDate = $reservedReservations->contains(function($reservation) use ($programId, $programStartDate) {
                                return $reservation->nhk_tvEpisodeId === $programId
                                    && \Carbon\Carbon::parse($reservation->start_time)->format('Y:m:d H:i') === $programStartDate;
                            });
                        @endphp

                        <tr>
                            <td class="align-middle p-2 text-center">
                                {{ \Carbon\Carbon::parse($program['startDate'])->format('H:i') }}
                                ~
                                {{ \Carbon\Carbon::parse($program['endDate'])->format('H:i') }}
                            </td>
                            <td class="align-middle ps-5 p-2">
                                {{ $program['identifierGroup']['tvSeriesName'] ?? $program['name']}}<br>
                                - {{ $program['identifierGroup']['tvEpisodeName'] ?? '' }}
                            </td>
                            <td class="align-middle text-center p-2">{{ data_get($program, 'identifierGroup.genre.0.name1', '-') }}</td>
                            <td class="align-middle description p-2">{{ $program['description'] ?? '' }}</td>
                            <td class="align-middle text-center p-2">
                                @if(auth()->check())
                                    @if($isReserved)
                                        @if($isReservedDate)
                                            <div class="d-flex align-items-center">
                                                <svg class="p-0" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="32" height="32" viewBox="0 0 48 48">
                                                    <path fill="#c8e6c9" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path><path fill="#4caf50" d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z"></path>
                                                </svg>
                                                <span class="d-block checked text-success">予約済み</span>
                                            </div>
                                        @else
                                        <div class="icons_div">
                                            <img class="img-fluid" src="{{ asset('/image/icons.png') }}" alt="リモコン">
                                        </div>
                                        <p class="icon_font mt-1">過去に<br>予約しています</p>
                                            <form method="POST" action="{{ route('setting') }}">
                                                @csrf
                                                <input type="hidden" name="title" value="{{ $program['identifierGroup']['tvSeriesName'] ?? $program['name'] }}">
                                                <input type="hidden" name="sub_title" value="{{ $program['identifierGroup']['tvEpisodeName'] ?? '' }}">
                                                <input type="hidden" name="description" value="{{ $program['description'] ?? '' }}">
                                                <input type="hidden" name="genres" value="{{ data_get($program, 'identifierGroup.genre.0.name1', '-') }}">
                                                <input type="hidden" name="start" value="{{ \Carbon\Carbon::parse($program['startDate'])->format('Y-m-d H:i:s') }}">
                                                <input type="hidden" name="end" value="{{ \Carbon\Carbon::parse($program['endDate'])->format('Y-m-d h:i:s')  }}">
                                                <input type="hidden" name="nhkId" value="{{ $program['identifierGroup']['tvSeriesId'] ?? '' }}">
                                                <input type="hidden" name="areaId" value="{{ $program['identifierGroup']['areaId'] }}">

                                                <button class="btn btn-primary btn-sm mb-2" type="submit">予約</button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('setting') }}">
                                            @csrf
                                            <input type="hidden" name="title" value="{{ $program['identifierGroup']['tvSeriesName'] ?? $program['name'] }}">
                                            <input type="hidden" name="sub_title" value="{{ $program['identifierGroup']['tvEpisodeName'] ?? '' }}">
                                            <input type="hidden" name="description" value="{{ $program['description'] ?? '' }}">
                                            <input type="hidden" name="genres" value="{{ data_get($program, 'identifierGroup.genre.0.name1', '-') }}">
                                            <input type="hidden" name="start" value="{{ \Carbon\Carbon::parse($program['startDate'])->format('Y-m-d H:i:s') }}">
                                            <input type="hidden" name="end" value="{{ \Carbon\Carbon::parse($program['endDate'])->format('Y-m-d h:i:s')  }}">
                                            <input type="hidden" name="nhkId" value="{{ $program['identifierGroup']['tvSeriesId'] ?? '' }}">
                                            <input type="hidden" name="areaId" value="{{ $program['identifierGroup']['areaId'] }}">

                                            <button class="btn btn-primary btn-sm" type="submit">予約</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <!-- 上に戻るボタン -->
        <button id="backToTop">↑ TOP</button>
    <script>
    const backToTopButton = document.getElementById('backToTop');
    // スクロールイベント
    window.addEventListener('scroll', () => {
    if (window.scrollY > 500) {
        backToTopButton.style.display = 'block';
    } else {
        backToTopButton.style.display = 'none';
    }
    });
    backToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'   // スムーズスクロール
        });
    });
    </script>
</div>
@endsection