<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoardEase - Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --navy: #1a2340;
            --navy-light: #243050;
            --teal: #2ec4a5;
            --orange: #e8692a;
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
            font-size: 1.4rem; color: #fff;
        }
        .logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 2rem; font-weight: 800; color: #fff;
        }
        .logo-sub {
            font-size: 0.7rem; font-weight: 600;
            letter-spacing: 2px; color: var(--teal);
            text-transform: uppercase; display: block;
            margin-top: -4px;
        }
        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: 2.4rem; font-weight: 700;
            color: #fff; line-height: 1.2; margin-bottom: 18px;
        }
        .hero-title span { color: var(--orange); font-style: italic; }
        .hero-sub {
            font-size: 0.95rem; color: rgba(255,255,255,0.55);
            line-height: 1.6; margin-bottom: 44px; max-width: 360px;
        }
        .auth-right {
            flex: 1; background: #f5f7fa;
            display: flex; align-items: center;
            justify-content: center; padding: 40px 32px;
        }
        .auth-card {
            background: #fff; border-radius: 18px;
            padding: 40px 36px; width: 100%; max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--text-muted); text-decoration: none;
            font-size: 0.85rem; font-weight: 600; margin-bottom: 24px;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--teal); }
        .icon-wrap {
            width: 56px; height: 56px;
            background: #f0fdf4; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: var(--teal); margin-bottom: 20px;
        }
        .auth-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;
        }
        .auth-card > p {
            font-size: 0.875rem; color: var(--text-muted); margin-bottom: 28px;
            line-height: 1.6;
        }
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 0.85rem;
            font-weight: 600; margin-bottom: 7px; color: var(--text);
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;
        }
        .form-input {
            width: 100%; padding: 11px 14px 11px 38px;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'DM Sans', sans-serif; font-size: 0.9rem;
            color: var(--text); background: #f8fafc; outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus { border-color: var(--teal); background: #fff; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: var(--navy); color: #fff;
            border: none; border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem; font-weight: 700;
            cursor: pointer; transition: background 0.2s; margin-bottom: 16px;
        }
        .btn-submit:hover { background: var(--navy-light); }
        .success-msg {
            color: #16a34a; font-size: 0.83rem; margin-bottom: 12px;
            display: flex; align-items: center; gap: 6px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 8px; padding: 10px 12px;
        }
        .auth-footer {
            text-align: center; margin-top: 20px;
            font-size: 0.85rem; color: var(--text-muted);
        }
        .auth-footer a { color: var(--teal); text-decoration: none; font-weight: 600; }
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
</div>

<div class="auth-right">
    <div class="auth-card">
        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Sign In
        </a>

        <div class="icon-wrap">
            <i class="fas fa-lock"></i>
        </div>

        <h2>Forgot Password?</h2>
        <p>No worries! Enter your email address below and we'll send you a link to reset your password.</p>

        @if(session('status'))
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="#" onsubmit="handleSubmit(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-input"
                        placeholder="example@gmail.com" required
                        value="{{ old('email') }}">
                </div>
            </div>

            <div id="successBanner" style="display:none;" class="success-msg">
                <i class="fas fa-check-circle"></i> Password reset link sent! Check your email.
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>

        <div class="auth-footer">
            Remember your password? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</div>

<script>
function handleSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const banner = document.getElementById('successBanner');
    const email = e.target.email.value;

    if (!email) return;

    btn.textContent = 'Sending...';
    btn.disabled = true;

    setTimeout(() => {
        banner.style.display = 'flex';
        btn.textContent = 'Link Sent!';
        btn.style.background = '#16a34a';
    }, 1500);
}
</script>

</body>
</html>