<nav class="navbar navbar-expand-md navbar-light bg-white">
    <div class="container">
        <div class="navbar-item d-flex align-items-center">
            <a class="navbar-item" href="{{ url('/') }}">
                <img src="{{ asset('/image/tvImg.jpg') }}" height="60" alt="テレビ">
            </a>
            <span class="logo h2 ms-1 my-auto">NHK<small>_予約通知アプリ</small></span>
        </div>


        <div class="navbar-item" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">ログイン</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">会員登録</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <div class="d-flex align-items-center header_poji">
                            <svg class="p-0" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="16" height="16" viewBox="0 0 48 48">
                                <path fill="#c8e6c9" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path><path fill="#4caf50" d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z"></path>
                            </svg>
                            <span class="d-block checked text-success">認証済み</span>
                        </div>
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}さん
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('reservation.list', ['user' => Auth::user()]) }}">
                                予約リスト
                            </a>

                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                プロフィール情報変更
                            </a>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                ログアウト
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>