@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5 mx-auto bg-white shadow p-4">
            <div class="#">
                パスワードをお忘れですか？メールアドレスを入力していただければ、パスワードをリセットするためのリンクをお送りします。そのリンクから新しいパスワードを設定できます。
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="input-group mt-3">
                    <x-input-label for="email" style="font-size: 14px;" class="input-group-text" value="メールアドレス" />
                    <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="alert alert-danger mt-2" />
                </div>

                <div class="text-center mt-4">
                    <x-primary-button class="update_btn">
                        リンクを送信する
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection