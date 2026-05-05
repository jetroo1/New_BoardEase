<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BoardEase - Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --navy: #1a2340;
            --navy-light: #243050;
            --teal: #2ec4a5;
            --orange: #e8692a;
            --bg: #f0f4f8;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--navy);
        }
        .auth-left {
            flex: 0 0 48%;
            background: var(--navy);
            padding: 52px 56px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(46,196,165,0.06);
            border-radius: 50%;
            top: -80px; right: -80px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(46,196,165,0.04);
            border-radius: 50%;
            bottom: -60px; left: -60px;
        }
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
        }
        .logo-icon {
            width: 52px; height: 52px;
            background: var(--teal);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            color: #fff;
        }
        .logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
        }
        .logo-sub {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--teal);
            text-transform: uppercase;
            display: block;
            margin-top: -4px;
        }
        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 18px;
        }
        .hero-title span { color: var(--orange); font-style: italic; }
        .hero-sub {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.6;
            margin-bottom: 44px;
            max-width: 360px;
        }
        .features-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .feature-icon {
            width: 34px; height: 34px;
            background: rgba(46,196,165,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--teal);
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .feature-text { font-size: 0.9rem; color: rgba(255,255,255,0.75); }
        .auth-right {
            flex: 1;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }
        .auth-card {
            background: #fff;
            border-radius: 18px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }
        .auth-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .auth-card > p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 28px;
        }
        .auth-tabs {
            display: flex;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 28px;
        }
        .auth-tab {
            flex: 1;
            padding: 9px;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
        }
        .auth-tab.active {
            background: #fff;
            color: var(--text);
            box-shadow: 0 1px 6px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 7px;
            color: var(--text);
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text);
            background: #f8fafc;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus { border-color: var(--teal); background: #fff; }
        .form-input.is-error { border-color: #ef4444; }
        .forgot-link { text-align: right; margin-top: 6px; }
        .forgot-link a {
            font-size: 0.82rem;
            color: var(--teal);
            text-decoration: none;
            font-weight: 600;
        }
        .role-select {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }
        .role-btn {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 14px 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .role-btn.selected { border-color: var(--navy); background: #fff; }
        .role-btn i { display: block; font-size: 1.3rem; margin-bottom: 6px; color: var(--text-muted); }
        .role-btn.selected i { color: var(--navy); }
        .role-btn strong { display: block; font-size: 0.85rem; color: var(--text); }
        .role-btn span { font-size: 0.75rem; color: var(--text-muted); }
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 16px;
        }
        .btn-submit:hover { background: var(--navy-light); }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            color: var(--text-muted);
            font-size: 0.82rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .btn-google {
            width: 100%;
            padding: 12px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: border-color 0.2s;
            text-decoration: none;
            color: var(--text);
        }
        .btn-google:hover { border-color: #4285F4; }
        .btn-facebook {
            width: 100%;
            padding: 12px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: border-color 0.2s;
            text-decoration: none;
            color: var(--text);
            margin-top: 10px;
        }
        .btn-facebook:hover { border-color: #1877f2; }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .auth-footer a { color: var(--teal); text-decoration: none; font-weight: 600; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .error-msg {
            color: #ef4444;
            font-size: 0.83rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .info-msg {
            color: #0369a1;
            font-size: 0.83rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 10px 12px;
        }
        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="auth-left">
    <div class="logo-wrap">
        <div class="logo-icon"><i class="fas fa-home"></i></div>
        <div>
            <div class="logo-text">BoardEase</div>
            <span class="logo-sub">Boarding House Finder & Reservation</span>
        </div>
    </div>
    <h1 class="hero-title">Find your <span>perfect</span> boarding house in Tagum City!</h1>
    <p class="hero-sub">Smart search, real-time availability, and hassle-free online booking — all in one place for Filipino students and workers.</p>
    <div class="features-list">
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-search"></i></div>
            <span class="feature-text">Location-based smart search</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-map-marker-alt"></i></div>
            <span class="feature-text">Interactive map with Leaflet API</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
            <span class="feature-text">Online booking & reservation</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-star"></i></div>
            <span class="feature-text">Verified reviews & star ratings</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-bell"></i></div>
            <span class="feature-text">Instant booking notifications</span>
        </div>
    </div>
</div>

<div class="auth-right">
    <div class="auth-card">
        <h2>Welcome to BoardEase</h2>
        <p>Sign in or create your account to continue</p>

        <div class="auth-tabs">
            <button class="auth-tab {{ old('_form', 'login') != 'register' ? 'active' : '' }}" id="tab-btn-login" onclick="switchTab('login', this)">Sign In</button>
            <button class="auth-tab {{ old('_form') == 'register' ? 'active' : '' }}" id="tab-btn-register" onclick="switchTab('register', this)">Register</button>
        </div>

        {{-- CANCELLED / INFO MESSAGE --}}
        @if(session('social_cancelled'))
            <div class="info-msg">
                <i class="fas fa-info-circle"></i> {{ session('social_cancelled') }}
            </div>
        @endif

        {{-- ======================================================= --}}
        {{-- LOGIN PANEL                                              --}}
        {{-- ======================================================= --}}
        <div class="tab-panel {{ old('_form', 'login') != 'register' ? 'active' : '' }}" id="tab-login">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="_form" value="login">

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email"
                            class="form-input {{ old('_form') != 'register' && $errors->any() ? 'is-error' : '' }}"
                            placeholder="example@gmail.com" required
                            value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password"
                            class="form-input {{ old('_form') != 'register' && $errors->any() ? 'is-error' : '' }}"
                            placeholder="Your password" required>
                    </div>
                    <div class="forgot-link"><a href="{{ route('password.request') }}">Forgot password?</a></div>
                </div>

                @if($errors->any() && old('_form') != 'register')
                    <div class="error-msg">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="btn-submit">Sign In to BoardEase</button>
            </form>

            <div class="divider">or</div>

            {{-- Google Login --}}
            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continue with Google
            </a>

            {{-- Facebook Login — uses LOGIN intent --}}
            <a href="{{ route('auth.facebook') }}" class="btn-facebook">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Continue with Facebook
            </a>

            <div class="auth-footer">No account? <a href="#" onclick="switchTab('register', document.getElementById('tab-btn-register'))">Register Here</a></div>
        </div>

        {{-- ======================================================= --}}
        {{-- REGISTER PANEL                                           --}}
        {{-- ======================================================= --}}
        <div class="tab-panel {{ old('_form') == 'register' ? 'active' : '' }}" id="tab-register">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="_form" value="register">

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name"
                            class="form-input {{ old('_form') == 'register' && $errors->has('name') ? 'is-error' : '' }}"
                            placeholder="Juan Dela Cruz" required
                            value="{{ old('name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email"
                            class="form-input {{ old('_form') == 'register' && $errors->has('email') ? 'is-error' : '' }}"
                            placeholder="example@gmail.com" required
                            value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password"
                            class="form-input {{ old('_form') == 'register' && $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="Create a password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation"
                            class="form-input {{ old('_form') == 'register' && $errors->has('password_confirmation') ? 'is-error' : '' }}"
                            placeholder="Re-enter your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">I am a...</label>
                    <div class="role-select">
                        <div class="role-btn selected" onclick="selectRole(this, 'tenant')">
                            <i class="fas fa-user"></i>
                            <strong>Tenant</strong>
                            <span>Looking for a room</span>
                        </div>
                        <div class="role-btn" onclick="selectRole(this, 'owner')">
                            <i class="fas fa-home"></i>
                            <strong>Owner</strong>
                            <span>Checking my property</span>
                        </div>
                    </div>
                    <input type="hidden" name="role" id="roleInput" value="tenant">
                </div>

                @if($errors->any() && old('_form') == 'register')
                    <div class="error-msg">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="btn-submit">Create My Account</button>
            </form>

            <div class="divider">or</div>

            {{-- Google Register --}}
            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Register with Google
            </a>

            {{-- ✅ Facebook Register — uses REGISTER intent (FIXED) --}}
            <a href="{{ route('auth.facebook.register') }}" class="btn-facebook">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Register with Facebook
            </a>
        </div>

    </div>
</div>

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

function selectRole(el, role) {
    document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('roleInput').value = role;
}
</script>
</body>
</html>