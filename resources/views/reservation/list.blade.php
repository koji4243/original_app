@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 shadow mx-auto bg-white p-4">
            <div class="w-100">
                <h2 class="mt-2 p-2 h4 text-center fs-light">
                    予約リスト
                </h2>
            </div>

            {{-- フラッシュメッセージの表示 --}}
            @if (session('message'))
                <div class="mt-1 alert alert-danger text-center">
                    {{ session('message') }}
                </div>
            @endif

            <div>
                <div class="d-flex justify-content-center align-items-center">
                    <div>
                        <img style="width: 24px;" src="{{ asset('/image/memo.png') }}" alt="メモ">
                    </div>
                    <p class="ms-1 username mb-1">{{ $user->name }}さんの予約リスト</p>
                </div>
                @forelse($users as $list)
                    <ul class="list-group mt-0">
                        @foreach($list->reservations as $reservation)
                            <li class="text-center list-group-item p-3 list_poji mb-1">
                                <dl class="d-flex mb-1">
                                    <dt class="fw-bold me-2">放送日時：</dt>
                                    <dd class="mb-0">{{ $reservation->start_time->format('Y:m:d H:i:s') }}</dd>
                                </dl>
                                <dl class="d-flex mb-1">
                                    <dt class="fw-bold me-2">番組名　：</dt>
                                    <dd class="mb-0">{{ $reservation->nhk_title }}</dd>
                                </dl>
                                <dl class="d-flex mb-1">
                                    <dt class="fw-bold me-2">通　知　：</dt>
                                    <dd class="mb-0">{{ $reservation->notify_at == null ? 'まだ' : '済' }} </dd>
                                </dl>

                                @if ($reservation->notify_at == null)
                                    <form class="poji_abu" action="{{ route('delete', [$user, $reservation]) }}" method="post">
                                    @csrf
                                    @method('DELETE')    
                                        <button type="submit" class="delete">削除</button>
                                    </form>                                
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @empty
                    <p class="text-center mt-2">予約はありません</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection