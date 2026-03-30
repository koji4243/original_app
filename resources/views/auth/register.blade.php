@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto bg-white shadow p-4">

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" class="form-label m-0" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-2">
                        <x-input-label for="email" class="form-label m-0" :value="__('Email')" />
                        <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-2">
                        <x-input-label for="password" class="form-label m-0" :value="__('Password')" />

                        <x-text-input id="password" class="form-control"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-2">
                        <x-input-label for="password_confirmation" class="form-label m-0" :value="__('Confirm Password')" />

                        <x-text-input id="password_confirmation" class="form-control"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="d-flex align-items-center mt-4">
                        <a class="font_change ms-auto" href="{{ route('login') }}">
                            すでに登録済の方
                        </a>

                        <x-primary-button class="update_btn ms-4">
                            登　録
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </div>    
    </form>
@endsection
