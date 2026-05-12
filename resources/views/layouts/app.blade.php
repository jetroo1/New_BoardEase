<!DOCTYPE html>
<html lang="en" data-theme="{{ auth()->check() ? auth()->user()->theme : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>BoardEase - @yield('title', 'Boarding House Finder')</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navy: #0f3f5f;
            --navy-light: #075985;
            --navy-lighter: #0e7490;
            --teal: #06b6d4;
            --teal-dark: #0891b2;
            --orange: #0ea5e9;
            --blue-accent: #38bdf8;
            --green: #22c55e;
            --red: #ef4444;
            --yellow: #f59e0b;
            --purple: #8b5cf6;
            --bg: #edfaff;
            --card: rgba(255, 255, 255, 0.78);
            --text: #0f2741;
            --text-muted: #64748b;
            --border: rgba(125, 211, 252, 0.34);
            --glass-card: rgba(255, 255, 255, 0.74);
            --glass-border: rgba(125, 211, 252, 0.38);
            --glass-shadow: 0 18px 46px rgba(14, 116, 144, 0.12);
            --app-bg: linear-gradient(135deg, #f8fdff 0%, #edfaff 42%, #ffffff 100%);
            --sidebar-w: 230px;
            --topbar-h: 64px;
        }

        html[data-theme="dark"] {
            --app-bg: linear-gradient(135deg, #071826 0%, #082f49 48%, #0f2741 100%);
            --bg: #082f49;
            --card: rgba(15, 39, 65, 0.78);
            --glass-card: rgba(15, 39, 65, 0.74);
            --glass-border: rgba(103, 232, 249, 0.20);
            --text: #e0f7ff;
            --text-muted: #9bd3e6;
            --border: rgba(103, 232, 249, 0.20);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--app-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            transition: background 0.2s, color 0.2s;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(14, 165, 233, 0.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(14, 165, 233, 0.045) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(180deg, transparent 0, #000 12%, #000 84%, transparent 100%);
        }

        .sidebar {
            width: var(--sidebar-w);
            background:
                linear-gradient(180deg, rgba(8, 47, 73, 0.98), rgba(8, 145, 178, 0.90));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
            box-shadow: 16px 0 50px rgba(14, 116, 144, 0.16);
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(255,255,255,0.10), transparent 36%),
                repeating-linear-gradient(90deg, rgba(255,255,255,0.04) 0 1px, transparent 1px 26px);
            opacity: 0.7;
        }

        .sidebar-logo {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo-icon {
            width: 38px; height: 38px;
            background: var(--teal);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 12px 28px rgba(34, 211, 238, 0.28);
        }

        .sidebar-logo-text { display: flex; flex-direction: column; }
        .sidebar-logo-name { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 800; color: #fff; line-height: 1.1; }
        .sidebar-logo-sub { font-size: 0.5rem; font-weight: 600; letter-spacing: 1px; color: var(--teal); text-transform: uppercase; line-height: 1.2; }

        .sidebar-nav { padding: 16px 10px; flex: 1; display: flex; flex-direction: column; gap: 4px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.875rem; font-weight: 500;
            transition: all 0.2s; cursor: pointer;
        }

        .nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-item i { width: 18px; text-align: center; font-size: 0.95rem; }

        .sidebar-bottom { padding: 16px 10px; border-top: 1px solid rgba(255,255,255,0.08); }

        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            cursor: pointer; transition: background 0.2s;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.08); }
        .sidebar-user img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
        .sidebar-user-info { flex: 1; overflow: hidden; }
        .sidebar-user-name { font-size: 0.8rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 0.7rem; color: rgba(255,255,255,0.45); }

        .btn-add-property {
            margin: 0 10px 16px;
            background: var(--blue-accent); color: #fff;
            border: none; border-radius: 10px;
            padding: 11px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem; font-weight: 600;
            cursor: pointer;
            width: calc(100% - 20px);
            transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            text-decoration: none;
        }

        .btn-add-property:hover { background: #2563eb; }

        .main-wrapper { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        .topbar {
            height: var(--topbar-h);
            background: rgba(248, 253, 255, 0.78);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px; gap: 16px;
            position: sticky; top: 0; z-index: 50;
            transition: background 0.2s, border-color 0.2s;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(14, 116, 144, 0.07);
        }

        .search-bar { flex: 1; max-width: 420px; position: relative; }
        .search-bar input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            color: var(--text);
            background: rgba(255,255,255,0.74);
            outline: none;
            transition: border-color 0.2s;
        }
        .search-bar input:focus { border-color: var(--teal); }
        .search-bar > i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; pointer-events: none; }

        .search-suggestions {
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 999;
            overflow: hidden;
            display: none;
        }

        .search-suggestions.show { display: block; }

        .suggestion-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
            color: var(--text);
        }

        .suggestion-item:hover { background: var(--bg); }

        .suggestion-icon {
            width: 34px; height: 34px;
            background: #f0fdfb;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--teal);
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .suggestion-info { flex: 1; min-width: 0; }
        .suggestion-name { font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .suggestion-addr { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .suggestion-price { font-size: 0.8rem; font-weight: 700; color: var(--teal); flex-shrink: 0; }

        .suggestion-footer {
            padding: 8px 14px;
            border-top: 1px solid var(--border);
            font-size: 0.78rem;
            color: var(--teal);
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background 0.15s;
        }

        .suggestion-footer:hover { background: var(--bg); }

        .suggestion-empty {
            padding: 16px 14px;
            font-size: 0.875rem;
            color: var(--text-muted);
            text-align: center;
        }

        .topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }

        .icon-btn {
            width: 36px; height: 36px; border-radius: 50%;
            border: 1.5px solid var(--border);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-muted); font-size: 0.9rem;
            transition: all 0.2s; text-decoration: none; position: relative;
        }

        .icon-btn:hover { border-color: var(--teal); color: var(--teal); }

        .notif-badge {
            position: absolute; top: -3px; right: -3px;
            min-width: 16px; height: 16px; padding: 0 4px;
            background: var(--red); border-radius: 50%;
            font-size: 0.6rem; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; border: 2px solid var(--card);
        }

        .notification-shell { position: relative; }
        .notification-dropdown {
            position: absolute;
            top: 46px;
            right: 0;
            width: min(380px, calc(100vw - 28px));
            max-height: 520px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1200;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(14, 116, 144, 0.22);
            backdrop-filter: blur(22px);
            transform-origin: top right;
        }
        .notification-dropdown.show {
            display: flex;
            animation: dropdownPop 0.16s ease both;
        }
        .notification-dropdown-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .notification-dropdown-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }
        .notification-mark-all {
            border: none;
            background: transparent;
            color: var(--teal-dark);
            font: 700 0.78rem 'DM Sans', sans-serif;
            cursor: pointer;
            white-space: nowrap;
        }
        .notification-list {
            overflow-y: auto;
            max-height: 390px;
            padding: 8px;
        }
        .notification-item {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 14px;
            padding: 11px 10px;
            display: flex;
            gap: 10px;
            text-align: left;
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
        }
        .notification-item:hover {
            background: rgba(14, 165, 233, 0.08);
            border-color: rgba(14, 165, 233, 0.18);
            transform: translateY(-1px);
        }
        .notification-item.unread {
            background: rgba(6, 182, 212, 0.12);
            border-color: rgba(6, 182, 212, 0.22);
        }
        .notification-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            color: #0284c7;
            background: rgba(14, 165, 233, 0.13);
        }
        .notification-item-icon.booking { color: #0ea5e9; background: rgba(14, 165, 233, 0.14); }
        .notification-item-icon.message { color: #06b6d4; background: rgba(6, 182, 212, 0.14); }
        .notification-item-icon.property { color: #0891b2; background: rgba(8, 145, 178, 0.14); }
        .notification-item-icon.review { color: #f59e0b; background: rgba(245, 158, 11, 0.14); }
        .notification-item-icon.payment { color: #16a34a; background: rgba(34, 197, 94, 0.14); }
        .notification-item-icon.system { color: #64748b; background: rgba(100, 116, 139, 0.13); }
        .notification-item-main { min-width: 0; flex: 1; }
        .notification-item-top {
            display: flex;
            gap: 8px;
            justify-content: space-between;
            align-items: flex-start;
        }
        .notification-item-title {
            color: var(--text);
            font-size: 0.84rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .notification-unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--teal);
            flex-shrink: 0;
            margin-top: 4px;
        }
        .notification-item-message {
            margin-top: 3px;
            color: var(--text-muted);
            font-size: 0.78rem;
            line-height: 1.35;
        }
        .notification-item-time {
            margin-top: 6px;
            color: #0284c7;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .notification-empty,
        .notification-loading {
            padding: 28px 18px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.86rem;
        }
        .notification-spinner {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid rgba(14, 165, 233, 0.18);
            border-top-color: var(--teal);
            animation: spin 0.8s linear infinite;
            margin: 0 auto 10px;
        }
        .notification-dropdown-footer {
            border-top: 1px solid var(--border);
            padding: 10px;
            display: flex;
            justify-content: center;
        }
        .notification-dropdown-footer a {
            color: var(--teal-dark);
            font-size: 0.82rem;
            font-weight: 800;
            text-decoration: none;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .topbar-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .topbar-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .topbar-profile-info { text-align: right; }
        .topbar-profile-name { font-size: 0.875rem; font-weight: 600; color: var(--text); }
        .topbar-profile-role { font-size: 0.72rem; color: var(--text-muted); }

        .page-content { padding: 28px; flex: 1; position: relative; }

        .card { background: var(--card); border-radius: 14px; border: 1px solid var(--border); transition: background 0.2s, border-color 0.2s; backdrop-filter: blur(14px); }

        .badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.3px; }
        .badge-confirmed { background: #dcfce7; color: #16a34a; }
        .badge-pending { background: #fff7ed; color: #c2410c; }
        .badge-active { background: #dcfce7; color: #16a34a; }
        .badge-completed { background: #f1f5f9; color: #475569; }
        .badge-cancelled { background: #fee2e2; color: #dc2626; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 18px; border-radius: 9px; border: none; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, var(--navy-light), var(--teal)); color: #fff; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--navy), var(--teal-dark)); }
        .btn-teal { background: var(--teal); color: #fff; }
        .btn-teal:hover { background: var(--teal-dark); }
        .btn-orange { background: var(--orange); color: #fff; }
        .btn-orange:hover { background: #d4581f; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--navy); }
        .btn-blue { background: var(--blue-accent); color: #fff; }
        .btn-blue:hover { background: #2563eb; }
        .btn-green { background: var(--green); color: #fff; }
        .btn-green:hover { background: #16a34a; }
        .btn-sm { padding: 6px 13px; font-size: 0.8rem; }
        .btn-xs { padding: 4px 10px; font-size: 0.75rem; }

        .text-teal { color: var(--teal); }
        .text-orange { color: var(--orange); }
        .text-muted { color: var(--text-muted); }
        .text-blue { color: var(--blue-accent); }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }

        .leaflet-container { font-family: 'DM Sans', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .dropdown-menu-card { background: var(--card); border: 1.5px solid var(--border); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .dropdown-menu-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 0.875rem; font-weight: 500; color: var(--text); text-decoration: none; transition: background 0.2s; }
        .dropdown-menu-item:hover { background: rgba(0,0,0,0.05); }
        .dropdown-menu-divider { height: 1px; background: var(--border); }

        /* ── PAGE TRANSITION OVERLAY ── */
        #page-transition-overlay {
            position: fixed;
            inset: 0;
            background: var(--navy);
            z-index: 99999;
            pointer-events: none;
            transform: translateX(-100%);
        }

        /* ── SIDEBAR ENTRANCE ── */
        .sidebar {
            animation: sidebarSlideIn 0.3s ease both;
        }
        @keyframes sidebarSlideIn {
            from { transform: translateX(-12px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }

        /* ── SIDEBAR LOGO POP ── */
        .sidebar-logo-icon {
            animation: logoPop 0.3s ease 0.15s both;
        }
        @keyframes logoPop {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }

        /* ── NAV ITEMS STAGGER ── */
        .nav-item {
            opacity: 0;
            animation: navFadeIn 0.25s ease forwards;
        }
        .nav-item:nth-child(1) { animation-delay: 0.10s; }
        .nav-item:nth-child(2) { animation-delay: 0.13s; }
        .nav-item:nth-child(3) { animation-delay: 0.16s; }
        .nav-item:nth-child(4) { animation-delay: 0.19s; }
        .nav-item:nth-child(5) { animation-delay: 0.22s; }
        .nav-item:nth-child(6) { animation-delay: 0.25s; }
        .nav-item:nth-child(7) { animation-delay: 0.28s; }
        .nav-item:nth-child(8) { animation-delay: 0.31s; }
        @keyframes navFadeIn {
            from { opacity: 0; transform: translateX(-6px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── NAV ITEM ACTIVE INDICATOR ── */
        .nav-item.active {
            position: relative;
            overflow: hidden;
        }
        .nav-item.active::after {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--teal);
            border-radius: 0 3px 3px 0;
            animation: activeBar 0.2s ease 0.35s both;
        }
        @keyframes activeBar {
            from { transform: scaleY(0); }
            to   { transform: scaleY(1); }
        }

        /* ── TOPBAR DROP IN ── */
        .topbar {
            animation: topbarDrop 0.3s ease 0.05s both;
        }
        @keyframes topbarDrop {
            from { transform: translateY(-8px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* ── TOPBAR ICON BUTTONS ── */
        .icon-btn {
            transition: all 0.18s ease !important;
        }
        .icon-btn:hover {
            transform: scale(1.08);
            border-color: var(--teal);
            color: var(--teal);
        }

        /* ── PAGE CONTENT FADE + RISE ── */
        .page-content {
            animation: pageRise 0.35s ease 0.08s both;
        }
        @keyframes pageRise {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── CARD HOVER LIFT ── */
        .card {
            transition: transform 0.18s ease,
                        box-shadow 0.18s ease,
                        background 0.2s, border-color 0.2s !important;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        /* ── BUTTON PRESS FEEDBACK ── */
        .btn {
            transition: all 0.15s ease !important;
        }
        .btn:hover  { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.12); }
        .btn:active { transform: scale(0.97); }

        /* ── NOTIFICATION BADGE PULSE ── */
        .notif-badge {
            animation: badgePulse 2.5s ease-in-out infinite;
        }
        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
            50%       { box-shadow: 0 0 0 4px rgba(239,68,68,0); }
        }

        /* ── DROPDOWN ANIMATE ── */
        #profileMenu[style*="block"],
        #sidebarMenu[style*="block"] {
            animation: dropdownPop 0.15s ease;
        }
        @keyframes dropdownPop {
            from { opacity: 0; transform: translateY(-5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── SEARCH SUGGESTIONS ANIMATE ── */
        .search-suggestions.show {
            animation: suggestionsDrop 0.15s ease;
        }
        @keyframes suggestionsDrop {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── HELP MODAL ANIMATE ── */
        #helpModal > div {
            animation: modalPop 0.2s ease;
        }
        @keyframes modalPop {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.96); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        /* ── SIDEBAR USER AVATAR ON HOVER ── */
        .sidebar-user:hover img,
        .topbar-profile:hover img {
            animation: avatarWobble 0.3s ease;
        }
        @keyframes avatarWobble {
            0%,100% { transform: rotate(0deg); }
            25%     { transform: rotate(-3deg); }
            75%     { transform: rotate(3deg); }
        }

        /* ── PAGE EXIT ANIMATION ── */
        .page-exit {
            animation: pageLeave 0.2s ease forwards !important;
        }
        @keyframes pageLeave {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-6px); }
        }

        .sidebar > * { position: relative; z-index: 1; }
        .nav-item {
            border: 1px solid transparent;
        }
        .nav-item:hover,
        .nav-item.active {
            background: rgba(255,255,255,0.14);
            border-color: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
        }
        .sidebar-logo {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(10px);
        }
        .sidebar-logo-icon {
            background: linear-gradient(135deg, #22d3ee, #0284c7);
            border-radius: 10px;
        }
        .sidebar-logo-sub { color: #67e8f9; }
        .main-wrapper {
            background: transparent;
        }
        .card,
        .stat-card,
        .map-section,
        .bookings-section,
        .recent-section,
        .listings-card,
        .requests-card,
        .filter-panel,
        .prop-card,
        .booking-card,
        .review-item,
        .room-card,
        .modal-card,
        .fav-card,
        .settings-card,
        .profile-card,
        .notification-card {
            background: var(--glass-card) !important;
            border-color: var(--glass-border) !important;
            box-shadow: var(--glass-shadow);
            backdrop-filter: blur(16px);
        }
        .card:hover,
        .stat-card:hover,
        .prop-card:hover,
        .booking-card-mini:hover,
        .request-item:hover,
        .listing-item:hover {
            box-shadow: 0 22px 54px rgba(14, 116, 144, 0.16) !important;
        }
        .search-suggestions,
        .dropdown-menu,
        .notification-dropdown,
        #profileMenu,
        #sidebarMenu {
            background: rgba(255,255,255,0.88) !important;
            border-color: var(--glass-border) !important;
            backdrop-filter: blur(18px);
            box-shadow: var(--glass-shadow) !important;
        }
        .badge-active,
        .badge-confirmed {
            background: rgba(6, 182, 212, 0.14) !important;
            color: #0e7490 !important;
        }
        .btn-teal,
        .btn-orange,
        .apply-btn,
        .reserve-btn,
        .map-toggle-btn,
        .btn-approve,
        .send-btn {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4) !important;
            color: #fff !important;
            box-shadow: 0 12px 28px rgba(14, 165, 233, 0.18);
        }
        .icon-btn,
        .icon-action,
        .toolbar-btn,
        .call-action-btn,
        .pg-btn,
        .star-btn {
            background: rgba(255,255,255,0.68) !important;
            border-color: var(--glass-border) !important;
            backdrop-filter: blur(12px);
        }
        .price-cell,
        .view-all,
        .action-link,
        .suggestion-price,
        .prop-dist i,
        .filter-title i,
        .breadcrumb .sep {
            color: #0284c7 !important;
        }

        [data-theme="dark"] body::before {
            background-image:
                linear-gradient(rgba(103, 232, 249, 0.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(103, 232, 249, 0.04) 1px, transparent 1px);
        }
        [data-theme="dark"] .topbar {
            background: rgba(7, 24, 38, 0.82) !important;
            border-color: var(--glass-border) !important;
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.22);
        }
        [data-theme="dark"] .search-bar input,
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea,
        [data-theme="dark"] .price-input,
        [data-theme="dark"] .sort-select {
            background: rgba(8, 47, 73, 0.72) !important;
            color: var(--text) !important;
            border-color: var(--glass-border) !important;
        }
        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {
            color: rgba(224, 247, 255, 0.48) !important;
        }
        [data-theme="dark"] .search-suggestions,
        [data-theme="dark"] .dropdown-menu,
        [data-theme="dark"] .dropdown-menu-card,
        [data-theme="dark"] .notification-dropdown,
        [data-theme="dark"] #profileMenu,
        [data-theme="dark"] #sidebarMenu {
            background: rgba(7, 24, 38, 0.94) !important;
            border-color: var(--glass-border) !important;
            color: var(--text) !important;
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.35) !important;
        }
        [data-theme="dark"] .suggestion-item:hover,
        [data-theme="dark"] .suggestion-footer:hover,
        [data-theme="dark"] .dropdown-menu-item:hover,
        [data-theme="dark"] .notification-item:hover {
            background: rgba(14, 165, 233, 0.12) !important;
        }
        [data-theme="dark"] .card,
        [data-theme="dark"] .stat-card,
        [data-theme="dark"] .quick-action,
        [data-theme="dark"] .map-section,
        [data-theme="dark"] .map-panel,
        [data-theme="dark"] .bookings-section,
        [data-theme="dark"] .recent-section,
        [data-theme="dark"] .listings-card,
        [data-theme="dark"] .requests-card,
        [data-theme="dark"] .filter-panel,
        [data-theme="dark"] .prop-card,
        [data-theme="dark"] .booking-card,
        [data-theme="dark"] .booking-card-mini,
        [data-theme="dark"] .review-item,
        [data-theme="dark"] .room-card,
        [data-theme="dark"] .modal-card,
        [data-theme="dark"] .fav-card,
        [data-theme="dark"] .settings-card,
        [data-theme="dark"] .profile-card,
        [data-theme="dark"] .notification-card,
        [data-theme="dark"] .notifications-card,
        [data-theme="dark"] .overview-card,
        [data-theme="dark"] .minimap-card {
            background: var(--glass-card) !important;
            border-color: var(--glass-border) !important;
            color: var(--text) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.24) !important;
        }
        [data-theme="dark"] .stat-icon.blue,
        [data-theme="dark"] .stat-icon.green,
        [data-theme="dark"] .stat-icon.orange,
        [data-theme="dark"] .stat-icon.purple,
        [data-theme="dark"] .nearby-icon,
        [data-theme="dark"] .trust-icon,
        [data-theme="dark"] .suggestion-icon {
            background: rgba(14, 165, 233, 0.14) !important;
            color: #67e8f9 !important;
        }
        [data-theme="dark"] .badge-high,
        [data-theme="dark"] .badge-pending,
        [data-theme="dark"] .countdown-wrap,
        [data-theme="dark"] .instant-badge,
        [data-theme="dark"] .ai-orange,
        [data-theme="dark"] .notifications-tab,
        [data-theme="dark"] .tracker-chip,
        [data-theme="dark"] .verified-pill,
        [data-theme="dark"] .verified-inline {
            background: rgba(14, 165, 233, 0.12) !important;
            border-color: rgba(103, 232, 249, 0.20) !important;
            color: #7dd3fc !important;
        }
        [data-theme="dark"] .save-btn,
        [data-theme="dark"] .icon-btn,
        [data-theme="dark"] .icon-action,
        [data-theme="dark"] .toolbar-btn,
        [data-theme="dark"] .call-action-btn,
        [data-theme="dark"] .pg-btn,
        [data-theme="dark"] .star-btn {
            background: rgba(8, 47, 73, 0.74) !important;
            color: var(--text) !important;
            border-color: var(--glass-border) !important;
        }
        [data-theme="dark"] .booking-status-pill,
        [data-theme="dark"] .prop-tag,
        [data-theme="dark"] .gallery-badge {
            color: #fff !important;
        }
        [data-theme="dark"] .status-tracker,
        [data-theme="dark"] .tracker-message,
        [data-theme="dark"] .booking-insight-item,
        [data-theme="dark"] .nearby-item,
        [data-theme="dark"] .trust-chip {
            background: rgba(8, 47, 73, 0.56) !important;
            border-color: var(--glass-border) !important;
            color: var(--text) !important;
        }
        [data-theme="dark"] .leaflet-popup-content-wrapper,
        [data-theme="dark"] .leaflet-popup-tip {
            background: rgba(7, 24, 38, 0.96) !important;
            color: var(--text) !important;
            box-shadow: 0 18px 44px rgba(0,0,0,0.34) !important;
        }
        [data-theme="dark"] .map-popup-title,
        [data-theme="dark"] .dashboard-map-popup .popup-title,
        [data-theme="dark"] .trust-title,
        [data-theme="dark"] .booking-insight-value {
            color: var(--text) !important;
        }
        [data-theme="dark"] .map-popup-address,
        [data-theme="dark"] .dashboard-map-popup .popup-address,
        [data-theme="dark"] .trust-sub {
            color: var(--text-muted) !important;
        }
        [data-theme="dark"] .leaflet-control-zoom a {
            background: rgba(8, 47, 73, 0.92) !important;
            color: var(--text) !important;
            border-color: var(--glass-border) !important;
        }
        [data-theme="dark"] .btn-outline {
            background: rgba(8, 47, 73, 0.45) !important;
            color: var(--text) !important;
            border-color: var(--glass-border) !important;
        }
    </style>

    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon"><i class="fas fa-home" style="color:#fff;"></i></div>
        <div class="sidebar-logo-text">
            <div class="sidebar-logo-name">BoardEase</div>
            <div class="sidebar-logo-sub">Boarding House Finder & Reservation</div>
        </div>
    </div>

    @php $role = auth()->user()->role ?? 'tenant'; @endphp
<nav class="sidebar-nav">

  @if($role === 'tenant')
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="{{ route('search') }}" class="nav-item {{ request()->routeIs('search') ? 'active' : '' }}"><i class="fas fa-search"></i> Search</a>
    <a href="{{ route('bookings.index') }}" class="nav-item {{ request()->routeIs('bookings.index') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> My Bookings</a>
    <a href="{{ route('favorites') }}" class="nav-item {{ request()->routeIs('favorites') ? 'active' : '' }}"><i class="fas fa-heart"></i> Favorites</a>
    <a href="{{ route('reviews') }}" class="nav-item {{ request()->routeIs('reviews') ? 'active' : '' }}"><i class="fas fa-star"></i> Reviews</a>
    <a href="{{ route('notifications') }}" class="nav-item {{ request()->routeIs('notifications') ? 'active' : '' }}"><i class="fas fa-bell"></i> Notifications</a>
    <a href="{{ route('chat') }}" class="nav-item {{ request()->routeIs('chat*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Messages</a>
    <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a>
  @endif

  @if($role === 'owner')
    <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard <span style="font-size:0.65rem;background:rgba(255,255,255,0.15);padding:1px 5px;border-radius:4px;margin-left:2px;">Owner</span></a>
    <a href="{{ route('owner.properties') }}" class="nav-item {{ request()->routeIs('owner.properties*') ? 'active' : '' }}"><i class="fas fa-home"></i> My Properties</a>
    <a href="{{ route('owner.bookings') }}" class="nav-item {{ request()->routeIs('owner.bookings*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a>
    <a href="{{ route('chat') }}" class="nav-item {{ request()->routeIs('chat*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Messages</a>
    <a href="{{ route('notifications') }}" class="nav-item {{ request()->routeIs('notifications') ? 'active' : '' }}"><i class="fas fa-bell"></i> Notifications</a>
    <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a>
  @endif

  @if($role === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard <span style="font-size:0.65rem;background:rgba(255,255,255,0.15);padding:1px 5px;border-radius:4px;margin-left:2px;">Admin</span></a>
    <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> All Users</a>
    <a href="{{ route('admin.properties') }}" class="nav-item {{ request()->routeIs('admin.properties*') ? 'active' : '' }}"><i class="fas fa-home"></i> All Properties</a>
    <a href="{{ route('admin.bookings') }}" class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> All Bookings</a>
    <a href="{{ route('search') }}" class="nav-item {{ request()->routeIs('search') ? 'active' : '' }}"><i class="fas fa-search"></i> Browse</a>
    <a href="{{ route('chat') }}" class="nav-item {{ request()->routeIs('chat*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Messages</a>
    <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a>
  @endif

</nav>

    @auth
    @if($role === 'owner')
<a href="{{ route('owner.properties.create') }}" class="btn-add-property">
    </a>
    @endif

    <div class="sidebar-bottom">
        <div class="sidebar-user" onclick="toggleSidebarMenu()" style="position:relative;">
            <img src="{{ auth()->user()->photo }}"
     alt="avatar"
     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2ec4a5&color=fff'">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">My Account</div>
            </div>
            <div id="sidebarMenu" style="display:none;position:absolute;bottom:60px;left:0;right:0;z-index:999;overflow:hidden;" class="dropdown-menu-card">
                <a href="{{ route('settings') }}" class="dropdown-menu-item">
                    <i class="fas fa-cog" style="color:var(--text-muted);width:16px;"></i> Settings
                </a>
                <div class="dropdown-menu-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-menu-item" style="color:#ef4444;background:transparent;border:none;width:100%;cursor:pointer;font-family:'DM Sans',sans-serif;">
                        <i class="fas fa-sign-out-alt" style="width:16px;"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth
</aside>

<div class="main-wrapper">
    <header class="topbar">

        <!-- Smart Search -->
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input
                type="text"
                placeholder="@yield('search-placeholder', 'Search boarding houses...')"
                id="globalSearch"
                autocomplete="off"
            >
            <div class="search-suggestions" id="searchSuggestions"></div>
        </div>

        <div class="topbar-right">
            <button class="icon-btn" onclick="toggleTheme()" id="themeToggleBtn" title="Toggle dark mode">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            <div class="notification-shell" id="notificationShell">
                <button type="button" class="icon-btn" id="notificationBell" title="Notifications" aria-label="Notifications" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge" id="notificationBadge" style="display:none;">0</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown" aria-live="polite">
                    <div class="notification-dropdown-header">
                        <div>
                            <div class="notification-dropdown-title">Notifications</div>
                            <div style="font-size:0.76rem;color:var(--text-muted);margin-top:2px;">Realtime BoardEase updates</div>
                        </div>
                        <button type="button" class="notification-mark-all" id="notificationMarkAll">Mark all read</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-loading">
                            <div class="notification-spinner"></div>
                            Loading notifications...
                        </div>
                    </div>
                    <div class="notification-dropdown-footer">
                        <a href="{{ route('notifications') }}">View all notifications</a>
                    </div>
                </div>
            </div>
            <a href="#" class="icon-btn" onclick="openHelp(); return false;">
                <i class="fas fa-question-circle"></i>
            </a>
            <a href="{{ route('chat') }}" class="icon-btn"><i class="fas fa-comment-dots"></i></a>

            @auth
            <div class="topbar-profile" onclick="toggleProfileMenu()" style="position:relative;">
                <div class="topbar-profile-info">
                    <div class="topbar-profile-name">{{ auth()->user()->name }}</div>
                    <div class="topbar-profile-role">{{ ucfirst(auth()->user()->role ?? 'Tenant') }}</div>
                </div>
                <img src="{{ auth()->user()->photo }}"
     alt="avatar"
     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2ec4a5&color=fff'">
                <div id="profileMenu" style="display:none;position:absolute;top:48px;right:0;min-width:160px;z-index:999;overflow:hidden;" class="dropdown-menu-card">
                    <a href="{{ route('settings') }}" class="dropdown-menu-item">
                        <i class="fas fa-cog" style="color:var(--text-muted);width:16px;"></i> Settings
                    </a>
                    <div class="dropdown-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-menu-item" style="color:#ef4444;background:transparent;border:none;width:100%;cursor:pointer;font-family:'DM Sans',sans-serif;">
                            <i class="fas fa-sign-out-alt" style="width:16px;"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});

// ── Theme ──────────────────────────────────────────────────────
var _currentTheme = '{{ auth()->check() ? auth()->user()->theme : "light" }}';

function applyTheme(theme) {
    var root = document.documentElement;
    root.setAttribute('data-theme', theme);
    if (theme === 'dark') {
        root.style.setProperty('--app-bg',     'linear-gradient(135deg, #071826 0%, #082f49 48%, #0f2741 100%)');
        root.style.setProperty('--bg',         '#082f49');
        root.style.setProperty('--card',       'rgba(15, 39, 65, 0.78)');
        root.style.setProperty('--glass-card', 'rgba(15, 39, 65, 0.74)');
        root.style.setProperty('--glass-border','rgba(103, 232, 249, 0.20)');
        root.style.setProperty('--text',       '#e0f7ff');
        root.style.setProperty('--text-muted', '#9bd3e6');
        root.style.setProperty('--border',     'rgba(103, 232, 249, 0.20)');
        root.style.setProperty('--navy',       '#0f3f5f');
        root.style.setProperty('--navy-light', '#075985');
        document.getElementById('themeIcon').className = 'fas fa-sun';
    } else {
        root.style.setProperty('--app-bg',     'linear-gradient(135deg, #f8fdff 0%, #edfaff 42%, #ffffff 100%)');
        root.style.setProperty('--bg',         '#edfaff');
        root.style.setProperty('--card',       'rgba(255, 255, 255, 0.78)');
        root.style.setProperty('--glass-card', 'rgba(255, 255, 255, 0.74)');
        root.style.setProperty('--glass-border','rgba(125, 211, 252, 0.38)');
        root.style.setProperty('--text',       '#0f2741');
        root.style.setProperty('--text-muted', '#64748b');
        root.style.setProperty('--border',     'rgba(125, 211, 252, 0.34)');
        root.style.setProperty('--navy',       '#0f3f5f');
        root.style.setProperty('--navy-light', '#075985');
        document.getElementById('themeIcon').className = 'fas fa-moon';
    }
    _currentTheme = theme;
}

function toggleTheme() {
    var newTheme = _currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
    fetch('{{ route("settings.theme.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ theme: newTheme })
    });
}

applyTheme(_currentTheme);

// ── Dropdowns ──────────────────────────────────────────────────
function toggleProfileMenu() {
    var menu = document.getElementById('profileMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
function toggleSidebarMenu() {
    var menu = document.getElementById('sidebarMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.topbar-profile')) {
        var pm = document.getElementById('profileMenu');
        if (pm) pm.style.display = 'none';
    }
    if (!e.target.closest('#notificationShell')) {
        var nd = document.getElementById('notificationDropdown');
        var nb = document.getElementById('notificationBell');
        if (nd) nd.classList.remove('show');
        if (nb) nb.setAttribute('aria-expanded', 'false');
    }
    if (!e.target.closest('.sidebar-user')) {
        var sm = document.getElementById('sidebarMenu');
        if (sm) sm.style.display = 'none';
    }
    if (!e.target.closest('.search-bar')) {
        document.getElementById('searchSuggestions').classList.remove('show');
    }
});

// ── Smart Search ───────────────────────────────────────────────
// Notifications
const currentUserId = @json(auth()->id());
const notificationIndexUrl = @json(route('notifications'));
const notificationReadUrlTemplate = @json(route('notifications.read', ['id' => '__ID__']));
const notificationReadAllUrl = @json(route('notifications.readAll'));
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let notificationItems = [];
let notificationUnreadCount = 0;
let notificationsLoaded = false;
let notificationEchoBound = false;

function notificationReadUrl(id) {
    return notificationReadUrlTemplate.replace('__ID__', encodeURIComponent(id));
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, function(char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char];
    });
}

function iconForNotificationType(type) {
    const icons = {
        message: 'fas fa-comment-dots',
        booking_request: 'fas fa-calendar-plus',
        booking_status: 'fas fa-calendar-check',
        review: 'fas fa-star',
        matching_property: 'fas fa-house-chimney',
        property_updated: 'fas fa-pen-to-square',
        payment_confirmation: 'fas fa-receipt',
        admin_announcement: 'fas fa-bullhorn'
    };
    return icons[type] || 'fas fa-bell';
}

function toneForNotificationType(type) {
    if (type === 'message') return 'message';
    if (['booking_request', 'booking_status'].includes(type)) return 'booking';
    if (type === 'review') return 'review';
    if (['matching_property', 'property_updated'].includes(type)) return 'property';
    if (type === 'payment_confirmation') return 'payment';
    return 'system';
}

function normalizeNotification(notification) {
    return {
        id: notification.id || `live-${Date.now()}`,
        title: notification.title || 'BoardEase update',
        message: notification.message || 'You have a new notification.',
        type: notification.type || 'system',
        category: notification.category || 'system',
        action_url: notification.action_url || notificationIndexUrl,
        read_at: notification.read_at || null,
        unread: notification.unread ?? !notification.read_at,
        time_ago: notification.time_ago || 'Just now',
        icon: notification.icon || iconForNotificationType(notification.type || 'system'),
        tone: notification.tone || toneForNotificationType(notification.type || 'system'),
        metadata: notification.metadata || {}
    };
}

function renderNotificationBadge(count) {
    notificationUnreadCount = Number(count || 0);
    const badge = document.getElementById('notificationBadge');
    if (!badge) return;

    if (notificationUnreadCount > 0) {
        badge.textContent = notificationUnreadCount > 99 ? '99+' : notificationUnreadCount;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

function renderNotificationList() {
    const list = document.getElementById('notificationList');
    if (!list) return;

    if (!notificationItems.length) {
        list.innerHTML = `
            <div class="notification-empty">
                <i class="fas fa-bell-slash" style="font-size:1.35rem;color:#38bdf8;margin-bottom:8px;"></i>
                <div>No notifications yet</div>
            </div>
        `;
        return;
    }

    list.innerHTML = notificationItems.map(item => `
        <button type="button" class="notification-item ${item.unread ? 'unread' : ''}" data-id="${escapeHtml(item.id)}">
            <span class="notification-item-icon ${escapeHtml(item.tone)}"><i class="${escapeHtml(item.icon)}"></i></span>
            <span class="notification-item-main">
                <span class="notification-item-top">
                    <span class="notification-item-title">${escapeHtml(item.title)}</span>
                    ${item.unread ? '<span class="notification-unread-dot"></span>' : ''}
                </span>
                <span class="notification-item-message">${escapeHtml(item.message)}</span>
                <span class="notification-item-time"><i class="fas fa-clock" style="font-size:0.68rem;margin-right:4px;"></i>${escapeHtml(item.time_ago)}</span>
            </span>
        </button>
    `).join('');

    list.querySelectorAll('.notification-item').forEach(button => {
        button.addEventListener('click', function() {
            const item = notificationItems.find(n => String(n.id) === String(this.dataset.id));
            if (item) markNotificationRead(item.id, item.action_url);
        });
    });
}

function loadNotifications() {
    const list = document.getElementById('notificationList');
    if (list && !notificationsLoaded) {
        list.innerHTML = `
            <div class="notification-loading">
                <div class="notification-spinner"></div>
                Loading notifications...
            </div>
        `;
    }

    return fetch(notificationIndexUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            notificationItems = (data.notifications || []).map(normalizeNotification);
            notificationsLoaded = true;
            renderNotificationBadge(data.unread_count || 0);
            renderNotificationList();
        })
        .catch(() => {
            if (list) {
                list.innerHTML = '<div class="notification-empty">Notifications could not load right now.</div>';
            }
        });
}

function markNotificationRead(id, actionUrl) {
    if (!id || String(id).startsWith('live-')) {
        if (actionUrl) window.location.href = actionUrl;
        return;
    }

    fetch(notificationReadUrl(id), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            notificationItems = notificationItems.map(item => {
                if (String(item.id) !== String(id)) return item;
                return { ...item, unread: false, read_at: data.notification?.read_at || new Date().toISOString() };
            });
            renderNotificationBadge(data.unread_count || 0);
            renderNotificationList();
            if (actionUrl) window.location.href = actionUrl;
        })
        .catch(() => {
            if (actionUrl) window.location.href = actionUrl;
        });
}

function markAllNotificationsRead() {
    fetch(notificationReadAllUrl, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(() => {
        notificationItems = notificationItems.map(item => ({ ...item, unread: false, read_at: item.read_at || new Date().toISOString() }));
        renderNotificationBadge(0);
        renderNotificationList();
    });
}

function initNotificationRealtime(retries = 0) {
    if (!currentUserId || notificationEchoBound) return;

    if (typeof window.Echo === 'undefined') {
        if (retries < 20) setTimeout(() => initNotificationRealtime(retries + 1), 500);
        return;
    }

    try {
        notificationEchoBound = true;
        window.Echo.private(`App.Models.User.${currentUserId}`)
            .notification(notification => {
                const item = normalizeNotification({
                    ...notification,
                    unread: true,
                    read_at: null,
                    time_ago: 'Just now'
                });

                notificationItems = [item, ...notificationItems.filter(existing => existing.id !== item.id)].slice(0, 20);
                renderNotificationBadge(notificationUnreadCount + 1);
                renderNotificationList();
                setTimeout(loadNotifications, 700);
            });
    } catch (error) {
        notificationEchoBound = false;
        if (retries < 5) setTimeout(() => initNotificationRealtime(retries + 1), 1000);
    }
}

document.getElementById('notificationBell')?.addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    const isOpen = dropdown.classList.toggle('show');
    this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    if (isOpen && !notificationsLoaded) {
        loadNotifications();
    }
});

document.getElementById('notificationMarkAll')?.addEventListener('click', function(e) {
    e.stopPropagation();
    markAllNotificationsRead();
});

loadNotifications();
initNotificationRealtime();

const searchInput      = document.getElementById('globalSearch');
const suggestionsBox   = document.getElementById('searchSuggestions');
const suggestionsUrl   = '{{ route("search.suggestions") }}';
const searchUrl        = '{{ route("search") }}';

let searchTimer = null;

searchInput.addEventListener('input', function() {
    const q = this.value.trim();
    clearTimeout(searchTimer);

    if (q.length < 2) {
        suggestionsBox.classList.remove('show');
        suggestionsBox.innerHTML = '';
        return;
    }

    searchTimer = setTimeout(() => {
        fetch(`${suggestionsUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    suggestionsBox.innerHTML = `<div class="suggestion-empty"><i class="fas fa-search" style="margin-right:6px;opacity:0.4;"></i>No results for "<strong>${q}</strong>"</div>`;
                } else {
                    let html = data.map(p => `
                        <a href="/properties/${p.id}" class="suggestion-item">
                            <div class="suggestion-icon" style="padding:0;overflow:hidden;border-radius:8px;">
                                ${p.image
                                    ? `<img src="/storage/${p.image}" style="width:34px;height:34px;object-fit:cover;display:block;border-radius:8px;">`
                                    : `<i class="fas fa-home"></i>`
                                }
                            </div>
                            <div class="suggestion-info">
                                <div class="suggestion-name">${p.title}</div>
                                <div class="suggestion-addr"><i class="fas fa-map-marker-alt" style="font-size:0.7rem;margin-right:3px;"></i>${p.address}</div>
                            </div>
                            <div class="suggestion-price">₱${Number(p.price).toLocaleString()}/mo</div>
                        </a>
                    `).join('');
                    html += `<div class="suggestion-footer" onclick="window.location='${searchUrl}?q=${encodeURIComponent(q)}'">
                        <i class="fas fa-search" style="margin-right:5px;"></i>See all results for "<strong>${q}</strong>"
                    </div>`;
                    suggestionsBox.innerHTML = html;
                }
                suggestionsBox.classList.add('show');
            });
    }, 300);
});

searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = `${searchUrl}?q=${encodeURIComponent(this.value.trim())}`;
    }
});
</script>

@stack('scripts')

<!-- Help Modal -->
<div id="helpModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);" onclick="if(event.target===this)closeHelp()">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--card);border-radius:20px;width:620px;max-width:95vw;max-height:85vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        
        <!-- Header -->
        <div style="padding:24px 28px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--card);border-radius:20px 20px 0 0;z-index:1;">
            <div>
                <div style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:700;">📖 How to Use BoardEase</div>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:2px;">Your complete guide to finding & booking boarding houses</div>
            </div>
            <button onclick="closeHelp()" style="background:var(--bg);border:none;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <!-- Steps -->
        <div style="padding:24px 28px;display:flex;flex-direction:column;gap:20px;">

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">🔍</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">1. Search for Boarding Houses</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Use the <strong>Search</strong> page or the top search bar to find boarding houses near you. Filter by price range, room type, and amenities like WiFi, AC, or Security.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#f0fdfb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">🏠</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">2. View Property Details</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Click <strong>View Details</strong> on any listing to see photos, amenities, available room types, pricing, location map, and tenant reviews.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#fff7ed;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">📅</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">3. Make a Reservation</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Choose your <strong>room type</strong>, set your <strong>move-in date</strong> and <strong>duration</strong>, then click <strong>Reserve Now</strong>. You won't be charged yet — the host will confirm within 24 hours.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">✅</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">4. Track Your Bookings</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Go to <strong>My Bookings</strong> to see all your reservations. Confirmed bookings show a live <strong>move-out countdown</strong>. Pending bookings are waiting for host approval.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#fef9c3;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">❤️</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">5. Save Favorites</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Click the <strong>heart icon</strong> on any property to save it to your <strong>Favorites</strong> list for easy access later.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">⭐</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">6. Leave a Review</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">After your stay, go to <strong>Reviews</strong> to rate and review your boarding house. Your feedback helps other tenants make better decisions.</div>
                </div>
            </div>

            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:42px;height:42px;background:#fce7f3;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">💬</div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:4px;">7. Message the Host</div>
                    <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6;">Use the <strong>Messages</strong> section to chat directly with boarding house owners about availability, rules, or any questions you may have.</div>
                </div>
            </div>

            <div style="background:var(--bg);border-radius:14px;padding:16px 18px;margin-top:4px;">
                <div style="font-weight:700;font-size:0.875rem;margin-bottom:10px;">💡 Quick Tips</div>
                <div style="display:flex;flex-direction:column;gap:6px;font-size:0.82rem;color:var(--text-muted);">
                    <div>🌙 Toggle <strong>dark mode</strong> using the moon icon in the top bar</div>
                    <div>🗺️ Use <strong>Show Map View</strong> on the search page to see properties on a map</div>
                    <div>🔔 Check <strong>Notifications</strong> for booking updates and messages</div>
                    <div>⚙️ Update your profile and preferences in <strong>Settings</strong></div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div style="padding:16px 28px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            <button onclick="closeHelp()" style="background:var(--navy);color:#fff;border:none;padding:10px 24px;border-radius:10px;font-family:'DM Sans',sans-serif;font-weight:600;font-size:0.875rem;cursor:pointer;">Got it! 👍</button>
        </div>
    </div>
</div>

<script>
function openHelp() {
    document.getElementById('helpModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function closeHelp() {
    document.getElementById('helpModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeHelp();
});
</script>

<!-- ── PAGE TRANSITION OVERLAY ── -->
<div id="page-transition-overlay"></div>

<script>
(function() {
    var overlay = document.getElementById('page-transition-overlay');

    // Slide overlay OUT on page load (reveal)
    overlay.style.transition = 'none';
    overlay.style.transform = 'translateX(0%)';
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            overlay.style.transition = 'transform 0.3s ease';
            overlay.style.transform = 'translateX(100%)';
        });
    });

    // Intercept nav link clicks → slide overlay IN, then navigate
    document.querySelectorAll('a.nav-item, a.btn-add-property').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;
            e.preventDefault();
            var content = document.querySelector('.page-content');
            if (content) content.classList.add('page-exit');
            overlay.style.transition = 'none';
            overlay.style.transform = 'translateX(-100%)';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    overlay.style.transition = 'transform 0.25s ease';
                    overlay.style.transform = 'translateX(0%)';
                });
            });
            setTimeout(function() { window.location.href = href; }, 240);
        });
    });
})();
</script>
</body>
</html>
