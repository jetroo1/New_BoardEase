<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BoardEase - Verify Email Code</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --navy: #0f3f5f;
            --navy-light: #075985;
            --teal: #06b6d4;
            --bg: #edfaff;
            --border: rgba(125, 211, 252, 0.34);
            --text: #0f2741;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background:
                linear-gradient(135deg, rgba(8,47,73,0.96), rgba(8,145,178,0.86)),
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: auto, 42px 42px, 42px 42px;
        }
        .verify-card {
            width: min(100%, 460px);
            padding: 34px;
            border: 1px solid rgba(125, 211, 252, 0.35);
            border-radius: 18px;
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(18px);
            box-shadow: 0 24px 64px rgba(8, 47, 73, 0.25);
        }
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, #22d3ee, #0284c7);
            border-radius: 12px;
            box-shadow: 0 14px 34px rgba(34, 211, 238, 0.24);
        }
        .logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1;
        }
        .logo-sub {
            display: block;
            margin-top: 5px;
            color: var(--teal);
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.65rem;
            margin-bottom: 8px;
        }
        .lead {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 0.94rem;
            margin-bottom: 22px;
        }
        .email-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            max-width: 100%;
            padding: 8px 10px;
            margin-bottom: 22px;
            color: var(--navy-light);
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(224, 247, 255, 0.76);
            font-size: 0.85rem;
            font-weight: 700;
            word-break: break-all;
        }
        .code-input {
            width: 100%;
            padding: 15px 16px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: rgba(255,255,255,0.78);
            color: var(--text);
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-align: center;
            outline: none;
        }
        .code-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.14);
            background: #fff;
        }
        .btn-submit {
            width: 100%;
            margin-top: 16px;
            padding: 13px;
            color: #fff;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--navy-light), var(--teal));
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
        }
        .secondary-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            font-size: 0.86rem;
        }
        .link-button,
        .secondary-actions a {
            color: var(--teal);
            border: 0;
            background: transparent;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }
        .message {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 0.85rem;
        }
        .message.info {
            color: #0369a1;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
        }
        .message.error {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .hint {
            margin-top: 18px;
            color: var(--text-muted);
            font-size: 0.8rem;
            line-height: 1.5;
        }
    </style>
</head>
@php
    $verification = session('email_verification_code', []);
    $email = $verification['email'] ?? 'your email';
@endphp
<body>
    <main class="verify-card">
        <a href="{{ route('landing') }}" class="logo-wrap" aria-label="BoardEase home">
            <span class="logo-icon"><i class="fas fa-home"></i></span>
            <span>
                <span class="logo-text">BoardEase</span>
                <span class="logo-sub">Boarding House Finder & Reservation</span>
            </span>
        </a>

        <h1>Check your Gmail</h1>
        <p class="lead">We sent a 6-digit BoardEase verification code to your email. Open your Gmail inbox, copy the code, and paste it below.</p>

        <div class="email-chip"><i class="fas fa-envelope"></i> {{ $email }}</div>

        @if(session('status'))
            <div class="message info"><i class="fas fa-circle-info"></i> {{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="message error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('verification.code.verify') }}">
            @csrf
            <input
                class="code-input"
                type="text"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                pattern="[0-9]{6}"
                placeholder="000000"
                value="{{ old('code') }}"
                required
                autofocus
            >
            <button type="submit" class="btn-submit"><i class="fas fa-circle-check"></i> Verify and Continue</button>
        </form>

        <div class="secondary-actions">
            <form method="POST" action="{{ route('verification.code.resend') }}">
                @csrf
                <button type="submit" class="link-button">Resend code</button>
            </form>
            <a href="{{ route('login') }}">Back to login</a>
        </div>

        <p class="hint">Codes expire after 10 minutes. If you do not see it, check Spam or click Resend code.</p>
    </main>
</body>
</html>
