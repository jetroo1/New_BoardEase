<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BoardEase - Boarding House Finder in Tagum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --ink: #102236;
            --ink-soft: #31465d;
            --muted: #69798b;
            --line: rgba(125, 211, 252, 0.28);
            --line-strong: rgba(14, 165, 233, 0.34);
            --glass: rgba(255, 255, 255, 0.68);
            --glass-strong: rgba(255, 255, 255, 0.82);
            --white: #ffffff;
            --mist: #f5fcff;
            --cyan: #06b6d4;
            --sky: #0ea5e9;
            --deep: #075985;
            --green: #10b981;
            --amber: #f59e0b;
            --shadow: 0 20px 60px rgba(8, 47, 73, 0.13);
            --shadow-soft: 0 14px 34px rgba(8, 47, 73, 0.09);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            min-height: 100vh;
            font-family: "DM Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                linear-gradient(120deg, rgba(240, 253, 255, 0.96), rgba(255, 255, 255, 0.94) 42%, rgba(239, 249, 255, 0.96)),
                linear-gradient(180deg, #ffffff, #edfaff);
            line-height: 1.5;
            overflow-x: hidden;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background:
                linear-gradient(rgba(14, 165, 233, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(6, 182, 212, 0.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: 0.62;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        button, input, select, textarea { font: inherit; }
        .container { width: min(1160px, calc(100% - 40px)); margin: 0 auto; }
        .section { padding: 86px 0; }
        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            color: var(--deep);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.62);
            backdrop-filter: blur(14px);
        }
        .section-title {
            max-width: 720px;
            margin-top: 14px;
            font-family: "Manrope", "DM Sans", sans-serif;
            font-size: clamp(2rem, 4vw, 3.45rem);
            line-height: 1.04;
            letter-spacing: 0;
        }
        .section-copy {
            max-width: 680px;
            margin-top: 14px;
            color: var(--muted);
            font-size: 1rem;
        }

        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid rgba(125, 211, 252, 0.22);
            background: rgba(246, 253, 255, 0.72);
            backdrop-filter: blur(20px);
        }
        .nav-inner {
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            color: #ffffff;
            border-radius: 10px;
            background: var(--cyan);
            box-shadow: 0 12px 28px rgba(34, 211, 238, 0.28);
            font-size: 1.2rem;
        }
        .brand-copy { display: flex; flex-direction: column; line-height: 1.03; min-width: 0; }
        .brand-name {
            font-family: "Syne", "DM Sans", sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0;
            color: var(--ink);
            line-height: 1.1;
        }
        .brand-sub {
            margin-top: 4px;
            max-width: 210px;
            color: var(--sky);
            font-size: 0.5rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            color: var(--ink-soft);
            font-size: 0.93rem;
            font-weight: 800;
        }
        .nav-links a {
            padding: 10px 12px;
            border-radius: 8px;
            transition: color 0.18s ease, background 0.18s ease;
        }
        .nav-links a:hover { color: var(--deep); background: rgba(255, 255, 255, 0.7); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .mobile-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--ink);
            background: rgba(255, 255, 255, 0.74);
            cursor: pointer;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 11px 16px;
            font-weight: 900;
            font-size: 0.92rem;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, var(--sky), var(--cyan));
            box-shadow: 0 14px 28px rgba(14, 165, 233, 0.24);
        }
        .btn-primary:hover { box-shadow: 0 18px 34px rgba(14, 165, 233, 0.3); }
        .btn-glass {
            color: var(--ink);
            border-color: rgba(255, 255, 255, 0.56);
            background: rgba(255, 255, 255, 0.62);
            backdrop-filter: blur(16px);
        }
        .btn-outline {
            color: var(--deep);
            border-color: var(--line-strong);
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
        }

        .hero {
            position: relative;
            min-height: calc(100vh - 76px);
            display: grid;
            align-items: center;
            isolation: isolate;
            overflow: hidden;
            background-position: center;
            background-size: cover;
        }
        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            background:
                linear-gradient(90deg, rgba(4, 23, 39, 0.72), rgba(4, 23, 39, 0.24) 48%, rgba(255, 255, 255, 0.1)),
                linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(244, 252, 255, 0.95) 96%);
        }
        .hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 150px;
            z-index: -1;
            background: linear-gradient(180deg, rgba(245, 252, 255, 0), rgba(245, 252, 255, 1));
        }
        .hero-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 420px;
            align-items: end;
            gap: 28px;
            padding: 80px 0 70px;
        }
        .hero-copy {
            color: #ffffff;
            padding-top: 40px;
        }
        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 11px;
            border: 1px solid rgba(255, 255, 255, 0.34);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(14px);
            font-size: 0.84rem;
            font-weight: 900;
        }
        .hero h1 {
            max-width: 790px;
            margin-top: 18px;
            font-family: "Manrope", "DM Sans", sans-serif;
            font-size: clamp(3rem, 7.5vw, 6.25rem);
            line-height: 0.96;
            letter-spacing: 0;
        }
        .hero-copy p {
            max-width: 640px;
            margin-top: 22px;
            color: rgba(255, 255, 255, 0.86);
            font-size: clamp(1.02rem, 2vw, 1.22rem);
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }
        .hero-panel {
            border: 1px solid rgba(255, 255, 255, 0.44);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.66);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            padding: 16px;
        }
        .panel-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            color: var(--ink);
            font-weight: 900;
        }
        .panel-label span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--deep);
            font-size: 0.83rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .search-form {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }
        .search-field {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 10px;
            border: 1px solid rgba(125, 211, 252, 0.34);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.72);
        }
        .search-field i {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            color: var(--sky);
            border-radius: 8px;
            background: rgba(224, 247, 255, 0.74);
        }
        .search-field > span > span:first-child {
            display: block;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .search-field input,
        .search-field select {
            width: 100%;
            border: 0;
            outline: 0;
            color: var(--ink);
            background: transparent;
            min-width: 0;
        }
        .micro-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 14px;
        }
        .micro-card {
            min-height: 82px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            color: var(--ink);
            background: rgba(255, 255, 255, 0.64);
            backdrop-filter: blur(16px);
            box-shadow: 0 12px 28px rgba(8, 47, 73, 0.08);
        }
        .micro-card i {
            color: var(--sky);
            margin-bottom: 8px;
        }
        .micro-card strong {
            display: block;
            font-size: 0.88rem;
        }
        .micro-card span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .carousel-section { padding-top: 42px; }
        .carousel-wrap {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.58);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            overflow: hidden;
        }
        .carousel-track {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(260px, 360px);
            gap: 14px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding-bottom: 2px;
        }
        .carousel-track::-webkit-scrollbar { display: none; }
        .carousel-card {
            position: relative;
            min-height: 310px;
            display: flex;
            align-items: end;
            overflow: hidden;
            scroll-snap-align: start;
            border-radius: 8px;
            background: #dff8ff;
            box-shadow: 0 12px 26px rgba(8, 47, 73, 0.1);
        }
        .carousel-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.42s ease;
        }
        .carousel-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(4, 23, 39, 0.04), rgba(4, 23, 39, 0.72));
        }
        .carousel-card:hover img { transform: scale(1.05); }
        .carousel-info {
            position: relative;
            z-index: 1;
            width: calc(100% - 24px);
            margin: 12px;
            padding: 14px;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(14px);
        }
        .carousel-info h3 {
            font-size: 1rem;
            line-height: 1.25;
        }
        .carousel-info p {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 7px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.86rem;
        }
        .carousel-price {
            display: inline-flex;
            margin-top: 10px;
            padding: 6px 9px;
            color: var(--ink);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.82);
            font-size: 0.84rem;
            font-weight: 900;
        }
        .carousel-controls {
            display: flex;
            gap: 8px;
        }
        .icon-btn {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--deep);
            background: rgba(255, 255, 255, 0.72);
            cursor: pointer;
            backdrop-filter: blur(14px);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .icon-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-soft);
        }

        .property-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .property-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(18px);
            box-shadow: 0 12px 30px rgba(8, 47, 73, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
            border-color: var(--line-strong);
            box-shadow: var(--shadow);
        }
        .property-media {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: linear-gradient(135deg, #dff8ff, #ffffff);
        }
        .property-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .property-card:hover .property-media img { transform: scale(1.04); }
        .status-pill {
            position: absolute;
            top: 12px;
            left: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 9px;
            color: var(--deep);
            border: 1px solid rgba(255, 255, 255, 0.48);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.74);
            backdrop-filter: blur(12px);
            font-size: 0.72rem;
            font-weight: 900;
        }
        .card-body { padding: 16px; }
        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }
        .property-title {
            font-size: 1.04rem;
            font-weight: 900;
            line-height: 1.25;
        }
        .rating {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--ink);
            font-size: 0.84rem;
            font-weight: 900;
            white-space: nowrap;
        }
        .rating i { color: var(--amber); }
        .property-location {
            display: flex;
            gap: 7px;
            margin-top: 9px;
            color: var(--muted);
            font-size: 0.9rem;
        }
        .property-location i { color: var(--sky); margin-top: 3px; }
        .property-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 13px;
        }
        .meta-pill {
            padding: 5px 8px;
            color: var(--ink-soft);
            border: 1px solid rgba(125, 211, 252, 0.26);
            border-radius: 8px;
            background: rgba(240, 253, 255, 0.76);
            font-size: 0.75rem;
            font-weight: 800;
        }
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid rgba(125, 211, 252, 0.22);
        }
        .price { font-weight: 900; font-size: 1.06rem; }
        .price span { color: var(--muted); font-weight: 700; font-size: 0.8rem; }
        .view-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--deep);
            font-size: 0.84rem;
            font-weight: 900;
        }
        .empty-featured {
            grid-column: 1 / -1;
            padding: 34px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--line-strong);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(16px);
        }
        .empty-featured strong {
            display: block;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .trust-band {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(239, 249, 255, 0.78) 22%, rgba(255, 255, 255, 0));
        }
        .trust-grid,
        .steps-grid,
        .faq-grid {
            display: grid;
            gap: 16px;
            margin-top: 30px;
        }
        .trust-grid,
        .steps-grid { grid-template-columns: repeat(4, 1fr); }
        .glass-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.66);
            backdrop-filter: blur(18px);
            box-shadow: 0 12px 28px rgba(8, 47, 73, 0.06);
            padding: 21px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-soft);
        }
        .info-icon,
        .step-number {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: var(--deep);
            background: rgba(224, 247, 255, 0.84);
            font-weight: 900;
        }
        .glass-card h3 {
            margin-top: 16px;
            font-size: 1rem;
        }
        .glass-card p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 0.9fr;
            gap: 38px;
            align-items: center;
        }
        .about-panel {
            padding: 28px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow-soft);
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 26px;
        }
        .stat-card {
            min-height: 112px;
            border: 1px solid rgba(125, 211, 252, 0.24);
            border-radius: 8px;
            background: rgba(240, 253, 255, 0.64);
            padding: 16px;
        }
        .stat-card strong {
            display: block;
            font-family: "Manrope", "DM Sans", sans-serif;
            font-size: 2rem;
            line-height: 1;
        }
        .stat-card span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 800;
        }
        .about-visual {
            position: relative;
            min-height: 470px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background-position: center;
            background-size: cover;
            box-shadow: var(--shadow);
        }
        .about-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(4, 23, 39, 0.02), rgba(4, 23, 39, 0.55));
        }
        .about-badge {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            color: var(--ink);
            border: 1px solid rgba(255, 255, 255, 0.48);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.74);
            backdrop-filter: blur(16px);
        }
        .about-badge i {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            color: #ffffff;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--green), var(--cyan));
        }
        .about-badge span { color: var(--muted); font-size: 0.88rem; }

        .contact {
            color: var(--ink);
            background:
                linear-gradient(135deg, rgba(224, 247, 255, 0.84), rgba(255, 255, 255, 0.9) 48%, rgba(236, 253, 245, 0.7));
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            gap: 24px;
            margin-top: 30px;
        }
        .contact-list { display: grid; gap: 12px; }
        .contact-item {
            display: flex;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.62);
            backdrop-filter: blur(16px);
        }
        .contact-item i {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            color: var(--deep);
            border-radius: 8px;
            background: rgba(224, 247, 255, 0.86);
        }
        .contact-item span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 0.88rem;
        }
        .contact-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.62);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow-soft);
        }
        .contact-form label {
            display: grid;
            gap: 7px;
            color: var(--ink-soft);
            font-size: 0.78rem;
            font-weight: 900;
        }
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border: 1px solid rgba(125, 211, 252, 0.3);
            border-radius: 8px;
            padding: 12px;
            color: var(--ink);
            background: rgba(255, 255, 255, 0.72);
            outline: 0;
        }
        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: var(--sky);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        }
        .contact-form textarea { min-height: 126px; resize: vertical; }
        .contact-form .full { grid-column: 1 / -1; }
        .form-note { color: var(--green); font-size: 0.86rem; min-height: 22px; }

        .faq-grid { grid-template-columns: repeat(2, 1fr); }
        .footer {
            padding: 40px 0;
            color: var(--muted);
            border-top: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(18px);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr repeat(3, 1fr);
            gap: 28px;
        }
        .footer p { margin-top: 14px; max-width: 330px; }
        .footer h4 {
            color: var(--ink);
            font-size: 0.95rem;
            margin-bottom: 12px;
        }
        .footer-links {
            display: grid;
            gap: 8px;
            font-size: 0.92rem;
        }
        .footer-links a:hover { color: var(--deep); }
        .socials {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }
        .socials a {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--deep);
            background: rgba(240, 253, 255, 0.72);
        }
        .copyright {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(125, 211, 252, 0.22);
            font-size: 0.88rem;
        }

        .reveal {
            opacity: 0;
            transform: translateY(14px);
            animation: fadeUp 0.7s ease forwards;
        }
        .reveal:nth-child(2) { animation-delay: 0.08s; }
        .reveal:nth-child(3) { animation-delay: 0.14s; }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media (max-width: 1020px) {
            .hero-shell { grid-template-columns: 1fr; align-items: start; }
            .hero-panel { max-width: 640px; }
            .nav-links {
                position: absolute;
                left: 20px;
                right: 20px;
                top: 76px;
                display: none;
                flex-direction: column;
                align-items: stretch;
                padding: 12px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.92);
                box-shadow: var(--shadow);
            }
            .nav-links.open { display: flex; }
            .mobile-toggle { display: grid; }
            .property-grid,
            .trust-grid,
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .about-grid,
            .contact-grid,
            .footer-grid { grid-template-columns: 1fr; }
            .about-visual { min-height: 410px; }
        }
        @media (max-width: 720px) {
            .container { width: min(100% - 28px, 1160px); }
            .section { padding: 64px 0; }
            .nav-actions .btn-glass { display: none; }
            .brand-sub { max-width: 150px; font-size: 0.5rem; }
            .hero { min-height: auto; }
            .hero-shell { padding: 56px 0 52px; }
            .hero h1 { font-size: clamp(2.55rem, 13vw, 4.35rem); }
            .micro-grid,
            .property-grid,
            .trust-grid,
            .steps-grid,
            .stats-row,
            .faq-grid,
            .contact-form { grid-template-columns: 1fr; }
            .contact-form .full { grid-column: auto; }
            .section-head { align-items: start; flex-direction: column; }
            .carousel-track { grid-auto-columns: minmax(240px, 86vw); }
            .carousel-card { min-height: 280px; }
        }
    </style>
</head>
@php
    $featuredProperties = $featuredProperties ?? collect();
    $stats = $stats ?? ['properties' => 0, 'renters' => 0, 'owners' => 0];
    $fallbackImage = asset('storage/properties/fr.png');
    $imageUrl = function ($property) use ($fallbackImage) {
        return filled($property?->image)
            ? \Illuminate\Support\Facades\Storage::url($property->image)
            : $fallbackImage;
    };
    $heroProperty = $featuredProperties->first(fn ($property) => filled($property->image));
    $heroImage = $heroProperty ? $imageUrl($heroProperty) : $fallbackImage;
    $exploreHref = auth()->check() ? route('search') : route('login');
    $listHref = route('register');
    if (auth()->check()) {
        $listHref = auth()->user()->isOwner() ? route('owner.properties.create') : route('dashboard');
    }
    $searchAction = auth()->check() ? route('search') : route('login');
@endphp
<body>
    <nav class="nav">
        <div class="container nav-inner">
            <a href="{{ route('landing') }}" class="brand" aria-label="BoardEase home">
                <span class="brand-mark"><i class="fas fa-home"></i></span>
                <span class="brand-copy">
                    <span class="brand-name">BoardEase</span>
                    <span class="brand-sub">Boarding House Finder & Reservation</span>
                </span>
            </a>

            <div class="nav-links" id="navLinks">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ $exploreHref }}">Explore</a>
                <a href="#about">About Us</a>
                <a href="#contact">Contact Us</a>
                <a href="#faq">FAQ</a>
            </div>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-glass"><i class="fas fa-table-columns"></i> Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-glass"><i class="fas fa-right-to-bracket"></i> Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                @endauth
                <button class="mobile-toggle" type="button" aria-label="Open menu" aria-expanded="false" data-menu-toggle>
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <header class="hero" style="background-image: url('{{ $heroImage }}');">
        <div class="container hero-shell">
            <div class="hero-copy reveal">
                <div class="hero-kicker"><i class="fas fa-location-dot"></i> Tagum City boarding house finder</div>
                <h1>Find Your Perfect Boarding House in Tagum</h1>
                <p>Discover safe, affordable, and convenient boarding houses for students, workers, and renters with easy booking and direct owner messaging.</p>

                <div class="hero-actions">
                    <a href="{{ $exploreHref }}" class="btn btn-primary"><i class="fas fa-compass"></i> Explore Boarding Houses</a>
                    <a href="{{ $listHref }}" class="btn btn-glass"><i class="fas fa-key"></i> List Your Property</a>
                </div>
            </div>

            <aside class="hero-panel reveal" aria-label="Boarding house search">
                <div class="panel-label">
                    <strong>Search stays</strong>
                    <span><i class="fas fa-location-crosshairs"></i> Tagum ready</span>
                </div>
                <form class="search-form" method="GET" action="{{ $searchAction }}">
                    <label class="search-field" for="heroLocation">
                        <i class="fas fa-magnifying-glass"></i>
                        <span>
                            <span>Location</span>
                            <input id="heroLocation" type="text" name="q" value="Tagum" placeholder="Search Tagum, school, landmark">
                        </span>
                    </label>
                    <label class="search-field" for="heroPrice">
                        <i class="fas fa-peso-sign"></i>
                        <span>
                            <span>Price range</span>
                            <select id="heroPrice" name="max_price">
                                <option value="">Any budget</option>
                                <option value="5000">Up to &#8369;5,000</option>
                                <option value="8000">Up to &#8369;8,000</option>
                                <option value="12000">Up to &#8369;12,000</option>
                            </select>
                        </span>
                    </label>
                    <label class="search-field" for="heroRoom">
                        <i class="fas fa-bed"></i>
                        <span>
                            <span>Room type</span>
                            <select id="heroRoom" name="room_type">
                                <option value="">Any room</option>
                                <option value="solo">Solo room</option>
                                <option value="shared">Shared room</option>
                                <option value="bedspace">Bedspace</option>
                            </select>
                        </span>
                    </label>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search Boarding Houses</button>
                </form>

                <div class="micro-grid" aria-label="BoardEase highlights">
                    <div class="micro-card"><i class="fas fa-shield-heart"></i><strong>Verified</strong><span>Approved listings</span></div>
                    <div class="micro-card"><i class="fas fa-comments"></i><strong>Message</strong><span>Talk to owners</span></div>
                    <div class="micro-card"><i class="fas fa-calendar-check"></i><strong>Book</strong><span>Reserve faster</span></div>
                </div>
            </aside>
        </div>
    </header>

    <main>
        <section class="section carousel-section" aria-labelledby="carouselTitle">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="eyebrow"><i class="fas fa-images"></i> Boarding house gallery</span>
                        <h2 class="section-title" id="carouselTitle">Fresh places to check out</h2>
                        <p class="section-copy">Swipe through bright, photo-first previews of boarding houses available around Tagum City.</p>
                    </div>
                    <div class="carousel-controls" aria-label="Carousel controls">
                        <button class="icon-btn" type="button" data-carousel-prev aria-label="Previous property"><i class="fas fa-chevron-left"></i></button>
                        <button class="icon-btn" type="button" data-carousel-next aria-label="Next property"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="carousel-wrap reveal">
                    <div class="carousel-track" id="propertyCarousel">
                        @forelse($featuredProperties as $property)
                            <a href="{{ route('property.show', $property->id) }}" class="carousel-card">
                                <img src="{{ $imageUrl($property) }}" alt="{{ $property->title }}">
                                <div class="carousel-info">
                                    <h3>{{ $property->title }}</h3>
                                    <p><i class="fas fa-location-dot"></i> {{ $property->address }}</p>
                                    <span class="carousel-price">&#8369;{{ number_format($property->price, 0) }} / month</span>
                                </div>
                            </a>
                        @empty
                            <div class="empty-featured">
                                <strong>No featured photos yet.</strong>
                                <p>Approved properties with images will show in this carousel automatically.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="featured">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="eyebrow"><i class="fas fa-star"></i> Featured stays</span>
                        <h2 class="section-title">Available boarding houses</h2>
                        <p class="section-copy">Compare photos, location, price, room type, and ratings before opening the full boarding house details.</p>
                    </div>
                    <a href="{{ $exploreHref }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> View all</a>
                </div>

                <div class="property-grid">
                    @forelse($featuredProperties as $property)
                        @php
                            $rating = $property->reviews_avg_rating ? number_format($property->reviews_avg_rating, 1) : 'New';
                            $amenities = $property->amenities
                                ? array_slice(array_filter(array_map('trim', explode(',', $property->amenities))), 0, 2)
                                : [];
                        @endphp
                        <a href="{{ route('property.show', $property->id) }}" class="property-card reveal">
                            <div class="property-media">
                                <img src="{{ $imageUrl($property) }}" alt="{{ $property->title }}">
                                <span class="status-pill"><i class="fas fa-circle-check"></i> {{ $property->is_approved ? 'Verified' : 'Pending' }}</span>
                            </div>
                            <div class="card-body">
                                <div class="card-top">
                                    <h3 class="property-title">{{ $property->title }}</h3>
                                    <span class="rating"><i class="fas fa-star"></i> {{ $rating }}</span>
                                </div>
                                <div class="property-location">
                                    <i class="fas fa-location-dot"></i>
                                    <span>{{ $property->address }}</span>
                                </div>
                                <div class="property-meta">
                                    <span class="meta-pill">{{ ucfirst($property->room_type ?? 'Room') }}</span>
                                    @foreach($amenities as $amenity)
                                        <span class="meta-pill">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                                <div class="card-footer">
                                    <div class="price">&#8369;{{ number_format($property->price, 0) }} <span>/ month</span></div>
                                    <span class="view-chip">View Details <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-featured">
                            <strong>No approved properties yet.</strong>
                            <p>Once owners add and admins approve listings, they will appear here automatically.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section trust-band">
            <div class="container">
                <span class="eyebrow"><i class="fas fa-heart"></i> Why choose BoardEase</span>
                <h2 class="section-title">Calm, clear, and built for renters</h2>
                <p class="section-copy">BoardEase keeps the search practical and transparent while giving owners a simple place to manage listings and requests.</p>
                <div class="trust-grid">
                    <div class="glass-card reveal"><div class="info-icon"><i class="fas fa-circle-check"></i></div><h3>Verified boarding houses</h3><p>Approved listings can show status, images, amenities, and location details.</p></div>
                    <div class="glass-card reveal"><div class="info-icon"><i class="fas fa-calendar-days"></i></div><h3>Easy booking</h3><p>The reservation process stays connected to tenant and owner dashboards.</p></div>
                    <div class="glass-card reveal"><div class="info-icon"><i class="fas fa-message"></i></div><h3>Safe messaging</h3><p>Renters can message owners through BoardEase chat.</p></div>
                    <div class="glass-card reveal"><div class="info-icon"><i class="fas fa-graduation-cap"></i></div><h3>Student-friendly locations</h3><p>Search by location, budget, room type, and boarding house features.</p></div>
                </div>
            </div>
        </section>

        <section class="section" id="how">
            <div class="container">
                <span class="eyebrow"><i class="fas fa-route"></i> How it works</span>
                <h2 class="section-title">From search to reservation</h2>
                <div class="steps-grid">
                    <div class="glass-card reveal"><div class="step-number">1</div><h3>Search</h3><p>Find boarding houses by location, price range, room type, and amenities.</p></div>
                    <div class="glass-card reveal"><div class="step-number">2</div><h3>View details</h3><p>Check photos, rates, address, room types, and tenant reviews.</p></div>
                    <div class="glass-card reveal"><div class="step-number">3</div><h3>Message owner</h3><p>Ask questions before reserving a room or visiting the place.</p></div>
                    <div class="glass-card reveal"><div class="step-number">4</div><h3>Book</h3><p>Submit a reservation and track your request in the dashboard.</p></div>
                </div>
            </div>
        </section>

        <section class="section" id="about">
            <div class="container about-grid">
                <div class="about-panel reveal">
                    <span class="eyebrow"><i class="fas fa-city"></i> About us</span>
                    <h2 class="section-title">Built for renters and owners in Tagum City</h2>
                    <p class="section-copy">BoardEase helps students, workers, and renters find safe and convenient boarding houses in Tagum City. It also gives boarding house owners a reliable place to list properties, receive inquiries, and manage booking requests.</p>
                    <div class="stats-row">
                        <div class="stat-card"><strong>{{ number_format($stats['properties']) }}</strong><span>Properties listed</span></div>
                        <div class="stat-card"><strong>{{ number_format($stats['renters']) }}</strong><span>Happy renters</span></div>
                        <div class="stat-card"><strong>{{ number_format($stats['owners']) }}</strong><span>Verified owners</span></div>
                    </div>
                </div>
                <div class="about-visual reveal" style="background-image: url('{{ $heroImage }}');">
                    <div class="about-badge">
                        <i class="fas fa-location-dot"></i>
                        <div><strong>Tagum City, Philippines</strong><br><span>Local boarding house discovery made simple.</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section contact" id="contact">
            <div class="container">
                <span class="eyebrow"><i class="fas fa-paper-plane"></i> Contact us</span>
                <h2 class="section-title">Need help with a listing or booking?</h2>
                <p class="section-copy">Reach out for help with listings, bookings, owner accounts, or renter questions.</p>
                <div class="contact-grid">
                    <div class="contact-list">
                        <div class="contact-item"><i class="fas fa-envelope"></i><div><strong>Email</strong><span>adminboardease@gmail.com</span></div></div>
                        <div class="contact-item"><i class="fas fa-phone"></i><div><strong>Phone</strong><span>+63 916 970 3318</span></div></div>
                        <div class="contact-item"><i class="fas fa-location-dot"></i><div><strong>Location</strong><span>Tagum City, Philippines</span></div></div>
                    </div>
                    <form class="contact-form" id="contactForm">
                        <label>Name<input type="text" placeholder="Your name" required></label>
                        <label>Email<input type="email" placeholder="you@example.com" required></label>
                        <label class="full">Message<textarea placeholder="Tell us what you need help with" required></textarea></label>
                        <div class="form-note" id="contactNote"></div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send message</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="section" id="faq">
            <div class="container">
                <span class="eyebrow"><i class="fas fa-circle-question"></i> FAQ</span>
                <h2 class="section-title">Common questions</h2>
                <div class="faq-grid">
                    <div class="glass-card"><h3>How do I book a boarding house?</h3><p>Log in as a tenant, open a property, choose your room and move-in details, then submit the booking form.</p></div>
                    <div class="glass-card"><h3>Can I message the owner?</h3><p>Yes. BoardEase includes a messaging area so tenants and owners can talk about availability and house rules.</p></div>
                    <div class="glass-card"><h3>Are properties verified?</h3><p>Listings support approval status, so admins can approve or reject owner-submitted properties.</p></div>
                    <div class="glass-card"><h3>Can owners list properties?</h3><p>Yes. Owners can add properties, manage listing details, and view booking requests in their dashboard.</p></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="{{ route('landing') }}" class="brand">
                        <span class="brand-mark"><i class="fas fa-home"></i></span>
                        <span class="brand-copy"><span class="brand-name">BoardEase</span><span class="brand-sub">Boarding House Finder & Reservation</span></span>
                    </a>
                    <p>A modern boarding house finder for Tagum City renters, students, workers, owners, and admins.</p>
                    <div class="socials">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Messenger"><i class="fab fa-facebook-messenger"></i></a>
                    </div>
                </div>
                <div><h4>Quick links</h4><div class="footer-links"><a href="{{ $exploreHref }}">Explore</a><a href="#about">About Us</a><a href="#contact">Contact Us</a><a href="#faq">FAQ</a></div></div>
                <div><h4>For users</h4><div class="footer-links"><a href="{{ route('login') }}">Login</a><a href="{{ route('register') }}">Register</a><a href="{{ $listHref }}">List Property</a></div></div>
                <div><h4>Contact</h4><div class="footer-links"><span>adminboardease@gmail.com</span><span>+63 916 970 3318</span><span>Tagum City, Philippines</span></div></div>
            </div>
            <div class="copyright">&copy; 2026 BoardEase. All rights reserved.</div>
        </div>
    </footer>

    <script>
        const menuToggle = document.querySelector('[data-menu-toggle]');
        const navLinks = document.getElementById('navLinks');
        menuToggle?.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        const carousel = document.getElementById('propertyCarousel');
        const nextButton = document.querySelector('[data-carousel-next]');
        const prevButton = document.querySelector('[data-carousel-prev]');
        const slideByCard = (direction = 1) => {
            if (!carousel) return;
            const card = carousel.querySelector('.carousel-card');
            const amount = card ? card.getBoundingClientRect().width + 14 : 320;
            carousel.scrollBy({ left: amount * direction, behavior: 'smooth' });
        };
        nextButton?.addEventListener('click', () => slideByCard(1));
        prevButton?.addEventListener('click', () => slideByCard(-1));

        if (carousel && carousel.querySelectorAll('.carousel-card').length > 1) {
            window.setInterval(() => {
                const nearEnd = carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 8;
                carousel.scrollTo({ left: nearEnd ? 0 : carousel.scrollLeft + 320, behavior: 'smooth' });
            }, 5200);
        }

        document.getElementById('contactForm')?.addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('contactNote').textContent = 'Thanks, your message is ready for the BoardEase team.';
            this.reset();
        });
    </script>
</body>
</html>
