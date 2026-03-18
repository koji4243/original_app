@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mt-2 text-center">予約設定画面</h2>
            <div class="col-md-6 mx-auto">
                <div class="mt-4 mb-2">
                    <a href="{{ route('top') }}" class="text-decoration-none h-6">&lt; 戻る</a>
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
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-2 p-2">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
                        <path fill="#00acc1" d="M44,24c0,11-9,20-20,20S4,35,4,24S13,4,24,4S44,13,44,24z"></path><path fill="#eee" d="M40,24c0,8.8-7.2,16-16,16S8,32.8,8,24S15.2,8,24,8S40,15.2,40,24z"></path><line x1="24" x2="30.5" y1="24" y2="30.5" fill="none" stroke="#263238" stroke-miterlimit="10" stroke-width="2"></line><line x1="24" x2="24" y1="11" y2="24" fill="none" stroke="#263238" stroke-miterlimit="10" stroke-width="2"></line><g><path fill="#263238" d="M27,24c0,1.7-1.3,3-3,3c-1.7,0-3-1.3-3-3s1.3-3,3-3C25.7,21,27,22.3,27,24"></path><path fill="#00acc1" d="M25,24c0,0.6-0.4,1-1,1s-1-0.4-1-1c0-0.6,0.4-1,1-1S25,23.4,25,24"></path></g>
                    </svg>
                </div>
                <div class="parent">
                    <P class="mail_push">メール通知時間</P>
                    <p class="posision_change">※上記開始時間の</p>
                </div>
            </div>

                @error('set')
                    <div style="width: 480px;" class="mx-auto text-center alert alert-danger mt-4">{{ $message }}</div>
                @enderror

            <div class="d-flex justify-content-center mt-2">
                <div class="box">{{ \Carbon\Carbon::parse(session('nhk.start'))->isoFormat('YYYY-MM-DD (ddd)') }}</div>
                <div class="col-4">
                    <form id="my-form" action="{{ route('check') }}" method="post">
                        @csrf
                        <select name="set" class="d-block mt-1 form-select form-control">
                            <option value="" class="text-center p-1" required selected>選択してください</option>
                            <option value="30" class="text-center p-1">30分前</option>
                            <option value="60" class="text-center p-1">1時間前</option>
                            <option value="120" class="text-center p-1">2時間前</option>
                            <option value="180" class="text-center p-1">3時間前</option>
                        </select>
                    </form>
                </div>
                <div class="box">に通知をする</div>
            </div>
            <div class="mt-3 text-center">
                <button class="btn btn-primary" type="submit" form="my-form">確認画面へ</button>
            </div>
        </div>
    </div>
</div>
@endsection