@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <h2 class="h4 mt-2 text-center">以下の内容で予約しますか？</h2>
            <div class="mb-2">
                <a href="{{ route('setting') }}" class="d-block mt-3 text-decoration-none h-6">&lt; 戻る</a>
            </div>

            <table class="table g-2">
                <tbody>
                    <tr>
                        <th class="th2" style="width:30%">番組名</th>
                        <td class="td2">{{ session('nhk.title') }}<br>-<span>{{ session('nhk.sub_title') }}</span></td>
                    </tr>
                    <tr>
                        <th class="th2" style="width:30%">開始時間</th>
                        <td class="td2">{{ \Carbon\Carbon::parse(session('nhk.start'))->format('H:i') }} ~</td>
                    </tr>
                        <th class="th2" style="width:30%">通知時間</th>
                        <td class="td2">{{ $notifyTime }} ごろ</td>
                </tbody>
            </table>

            <div class="mt-3 text-center">
                <form action="{{ route('store', Auth::user()) }}" method="post">
                    @csrf
                    <input type="hidden" name="set" value="{{ $set }}">
                    <button class="btn btn-primary" type="submit">予約する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection