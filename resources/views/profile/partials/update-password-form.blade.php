<section>
    <header>
        <h2 class="mt-2 p-2 h3 text-center">
            パスワードを更新
        </h2>

        <p class="text-center p-2">
            アカウントの安全性を保つため、<br>長くてランダムなパスワードを使用してください。
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="text-center px-4 py-2">
            <div class="row input-group">
                <x-input-label class="col-md-5 input-group-text" for="update_password_current_password" value="現在のパスワード" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="col-md-5 form-control" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 alert alert-danger" />
            </div>

            <div class="row input-group">
                <x-input-label class="col-md-5 input-group-text" for="update_password_password" value="新しいパスワード" />
                <x-text-input id="update_password_password" name="password" type="password" class="col-md-5 form-control" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 alert alert-danger" />
            </div>

            <div class="row input-group">
                <x-input-label class="col-md-5 input-group-text" for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="col-md-5 form-control" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 alert alert-danger" />
            </div>

            <div class="mt-3">
                <x-primary-button class="update_btn">更新する</x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="mt-2 fs-5 bg-success-subtle px-2 py-1 rounded"
                        style="color:#1f2937; border-color:#a3cfbb;"
                    >更新しました</p>
                @endif
            </div>
        </div>
    </form>
</section>
