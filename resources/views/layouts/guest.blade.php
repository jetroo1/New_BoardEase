<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BoardEase') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: "DM Sans", system-ui, sans-serif;
                background:
                    linear-gradient(135deg, #082f49 0%, #0e7490 46%, #e0f7ff 100%);
            }
            .guest-shell {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 28px 16px;
                background-image:
                    linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
                background-size: 42px 42px;
            }
            .guest-brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                color: #fff;
                margin-bottom: 24px;
            }
            .guest-logo {
                width: 48px;
                height: 48px;
                display: grid;
                place-items: center;
                border-radius: 12px;
                background: linear-gradient(135deg, #22d3ee, #0284c7);
                box-shadow: 0 14px 34px rgba(34, 211, 238, 0.24);
            }
            .guest-logo svg {
                width: 28px;
                height: 28px;
                color: #fff;
                fill: currentColor;
            }
            .guest-name {
                font-family: "Syne", system-ui, sans-serif;
                font-size: 1.45rem;
                line-height: 1;
                font-weight: 800;
            }
            .guest-sub {
                margin-top: 5px;
                color: #67e8f9;
                font-size: 0.62rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .guest-card {
                width: 100%;
                max-width: 440px;
                padding: 28px;
                border: 1px solid rgba(125, 211, 252, 0.35);
                border-radius: 14px;
                background: rgba(255,255,255,0.80);
                backdrop-filter: blur(18px);
                box-shadow: 0 24px 64px rgba(8, 47, 73, 0.22);
            }
            .guest-card input {
                border-color: rgba(125, 211, 252, 0.45);
                background: rgba(255,255,255,0.78);
            }
            .guest-card input:focus {
                border-color: #06b6d4;
                box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.16);
            }
            .guest-card button[type="submit"] {
                background: linear-gradient(135deg, #075985, #06b6d4);
                border-radius: 9px;
            }
        </style>
    </head>
    <body class="text-slate-900 antialiased">
        <div class="guest-shell">
            <a href="{{ route('landing') }}" class="guest-brand">
                <span class="guest-logo">
                    <x-application-logo />
                </span>
                <span>
                    <span class="guest-name">BoardEase</span>
                    <span class="guest-sub">Boarding House Finder & Reservation</span>
                </span>
            </a>

            <div class="guest-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
