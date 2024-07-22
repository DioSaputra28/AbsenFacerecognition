<!DOCTYPE html>
<html lang="en">
<head>
    @section('title', 'Masuk')
    @include('layouts.include.head')
    @notifyCss
</head>
<body class="background show-spinner no-footer">
    <div class="fixed-background"></div>
    <main>
        <div class="container">
            <div class="row h-100">
                <div class="col-12 col-md-10 mx-auto my-auto">
                    <div class="card auth-card">
                        <div class="position-relative image-side">
                            <p class="text-white h2">Verifikasi diri anda</p>
                            <h4 class="white mb-0">
                                Mohon isi kredensial Anda untuk masuk.
                                <br>Jika Anda bukan anggota, silakan mendaftar.
                                <a href="{{ route('register') }}" class="white"><strong>Register</strong></a>.
                            </h4>
                        </div>
                        <div class="form-side">
                            <a href="{{ url('/') }}">
                                <h1>{{ config('app.name') }}</h1>
                            </a>
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- Menampilkan pesan error dari sesi -->
                            @if (session('warning'))
                                <div class="alert alert-warning" role="alert">
                                    {{ session('warning') }}
                                </div>
                            @endif

                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">
                                    {{ $error }}
                                </div>
                            @endforeach

                            <h6 class="mb-4">Masuk</h6>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group has-float-label mb-4">
                                    <input type="text" class="form-control" name="username" value="{{ old('username') }}" id="username" autocomplete="off" autofocus />
                                    <span>Username / E-mail</span>
                                </div>
                                
                                <div class="form-group has-float-label mb-4">
                                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" />
                                    <span>Password</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">
                                            {{ __('Lupa Password?') }}
                                        </a>
                                    @endif
                                    <button class="btn btn-primary btn-lg btn-shadow" type="submit">{{ __('MASUK') }}</button>
                                </div>
                                
                                {{-- Uncomment if you're using captcha --}}
                                {{-- {!! app('captcha')->display() !!} --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- @include('notify::messages') --}}
    @notifyJs
    @include('layouts.include.script')
</body>
</html>
