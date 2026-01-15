<!DOCTYPE html>
<html lang="en">
<head>
    @include("partials.compatibility")
    <title>{{ env('APP_NAME') }} | Forgot Password</title>
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
.password-field input{width:100%;padding:15px 45px 15px 16px;/* right padding for eye icon */
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

<div class="core">
    <div class="identity">
        <img src="{{ url('/public/assets/images/dailyfood_logo.jpeg') }}">
    </div>

    <h1>Forgot Password</h1>
    <p>We’ll send you a reset link</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <input
                type="email"
                name="email"
                placeholder="Email address"
                value="{{ old('email') }}"
                required
                autofocus
                class="@error('email') is-invalid @enderror"
            >

            @error('email')
                <span class="invalid-feedback">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="action">
            <button type="submit">Send Reset Link</button>
        </div>

        <div class="meta" style="justify-content:center;gap:20px;">
            <a href="{{ route('login') }}">← Back to Login</a>
            <a href="{{ route('register') }}">Register ←</a>
        </div>
    </form>
</div>

@include("partials.scripts")
</body>
</html>
