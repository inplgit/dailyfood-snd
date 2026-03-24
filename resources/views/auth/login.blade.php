<!DOCTYPE html>
<html lang="en">
<head>
  @include("partials.compatibility")
  <meta name="description" content="">
  <title>{{env('APP_NAME')}} | Login</title>
  @include("partials.style")
</head>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',system-ui,sans-serif;}
body{position:relative;min-height:100vh;background:linear-gradient(135deg,#f35e2921,#ffffff,#619bf7b0);display:flex;align-items:center;justify-content:center;color:#333;font-family:'Inter',system-ui,sans-serif;}
body::before{content:"";position:absolute;inset:0;background-image: url('{{ url('/public/assets/images/inon.png') }}');background-repeat: no-repeat;background-position: center;background-size:contain;opacity:0.05;z-index:0;pointer-events:none;}
/* Core Card */
.core{position:relative;z-index:1;width:420px;padding:45px;border-radius:26px;background:#ffffff;box-shadow:0 25px 50px rgba(0,0,0,0.1);border:1px solid #e0e0e0;}
/* Logos */
.identity{display:flex;justify-content:center;align-items:center;gap:16px;}
.identity img{height:94px;padding:6px 10px;border-radius:10px;margin:0 auto;}
.identity2{display:flex;justify-content:left;position:absolute;left:0;top:0;}
.identity2 img{height:80px;padding:10px 10px;}
/* Title */
.core h1{text-align:center;font-size:28px;margin-bottom:6px;color:#1e293b;}
.core p{text-align:center;font-size:14px;opacity:0.7;margin-bottom:30px;}
/* Inputs */
.field{margin-bottom:18px;}
.field input{width:100%;padding:15px 16px;border-radius:14px;border:1px solid #cbd5e1;outline:none;background:#f9fafb;color:#1e293b;font-size:14px;transition:0.3s;}
.field input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.2);}
/* Button */
.action{margin-top:10px;}
.action button{width:100%;padding:15px;border:none;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#60a5fa);font-size:16px;font-weight:600;color:#fff;cursor:pointer;transition:0.35s;}
.action button:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(59,130,246,0.4);}
/* Footer */
.meta{display:flex;justify-content:space-between;margin-top:20px;font-size:13px;opacity:0.8;color:#334155;}
.meta a{color:#3b82f6;text-decoration:none;}
/* Powered By */
.powered{text-align:center;margin-top:30px;font-size:12px;color:#64748b;}
.powered a{color:#3b82f6;text-decoration:none;}
.password-field{position:relative;}
.pas

sword-field input{width:100%;padding:15px 45px 15px 16px;/* right padding for eye icon */
 border-radius:16px;border:1px solid #CBD5E1;outline:none;background:#F1F5F9;color:#1F2937;font-size:14px;}
.toggle-eye{position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:18px;opacity:0.7;transition:0.2s;}
.toggle-eye:hover{opacity:1;}

.invalid-feedback{color:red;font-size:13px;margin-top:5px;display:block;}
.is-invalid{border:1px solid red;}


/* Mobile */
@media(max-width:480px){.core{width:92%;padding:35px 25px;}
}

</style>
<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
<!-- <div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">


            <div class="auth-wrapper auth-v1 px-2">
                <div class="auth-inner py-2">
                    <div class="login_logo">
                        <a href="javascript:void(0);" class="brand-logo">
                            <span class="login-im">
                                <img src="public/assets/images/dailyfood_logo.jpeg"style=" margin-bottom:7px;width:63%;height:101%;">
                            </span>
                        </a>
                    </div>
                    <div class="card card_login mb-0">
                        <div class="card-body">
                            <div class="login_head">
                                <h2 class="card-text mb-2">Hello ! Welcome Back</h2>
                            </div>

                            <form class="auth-login-form mt-2" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="login-email" class="form-label">{{ __('Email Address') }}</label>
                                    <input class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus type="email" id="login-email" name="login-email" placeholder="username@example.com" aria-describedby="login-email" tabindex="1"/>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label for="login-password">Password</label>
                                    </div>
                                    <div class="input-group input-group-merge form-password-toggle">
                                        <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="login-password" tabindex="2"  name="password" required autocomplete="current-password"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group remember_forget">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" id="remember-me" tabindex="3" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                                        <label class="custom-control-label" for="remember-me"> Remember Me </label>
                                    </div>
                                    <div class="forget_pasword">
                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="login_button">
                                    <button class="btn btn-login btn-block" tabindex="4">Login in</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> -->

<div class="core">
    <div class="identity">
        <a class="navbar-brand" href="{{ url('dashboard') }}">
            <span class="brand-logo">
                <img src="{{ url('/public/assets/images/dailyfood_logo.jpeg') }}" onerror="this.onerror=null;this.src='{{ asset('logoo.png') }}'" alt="Innovative Network (Pvt.) Ltd."/>
            </span>
        </a>
    </div>
    <h1>Welcome</h1>
    <!-- <p>Multi-company enterprise dashboard</p> -->

    <!-- <form>
        <div class="field">
            <input type="email" placeholder="Email address" required>
        </div>

        <div class="field password-field">
            <input type="password" placeholder="Password" id="password" required>
            <span class="toggle-eye" onclick="togglePassword()">👁️</span>
        </div>

        <script>
        function togglePassword(){
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.toggle-eye');
            if(passwordInput.type === 'password'){
                passwordInput.type = 'text';
                eyeIcon.textContent = '🙈'; // Change icon
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }
        </script>


        <div class="action">
            <button>Enter Dashboard</button>
        </div>

        <div class="meta">
            <label><input type="checkbox"> Remember</label>
            <a href="#">Forgot?</a>
        </div>
    </form> -->

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <input
                type="email"
                name="email"
                placeholder="Email address"
                value="{{ old('email') }}"
                required
                autocomplete="email"
            >
        </div>

        <div class="field password-field">
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Password"
                
                autocomplete="current-password"
                class="@error('password') is-invalid @enderror"
            required/>

            <span class="toggle-eye" onclick="togglePassword()">👁️</span>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


        <script>
        function togglePassword(){
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.toggle-eye');
            if(passwordInput.type === 'password'){
                passwordInput.type = 'text';
                eyeIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }
        </script>

        <div class="action">
            <button type="submit">Enter Dashboard</button>
        </div>

        <div class="meta">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot?</a>
            @endif
        </div>
    </form>

    <p style="margin-bottom: 0;" class="powered">Powered By <strong><a href="https://innovative-net.com/" target="_blank"><img style="height: 12px;margin-right: -6px;" src="{{ url('/public/assets/images/inon.png') }}" alt="Innovative Network (Pvt.) Ltd"> Innovative Network (Pvt.) Ltd.</a></strong></p>

</div>

@include("partials.scripts")
</body>
</html>





{{-- @extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
