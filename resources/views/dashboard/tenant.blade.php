@extends('layouts.app')

@section('title', 'Dashboard')
@section('search-placeholder', 'Search boarding houses...')

@push('styles')
<style>
    .welcome-header { margin-bottom: 24px; }
    .welcome-header h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 700; }
    .welcome-header .date { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; display:flex;align-items:center;gap:6px; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--card);
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }

    .stat-card:nth-child(1)::before { background: var(--blue-accent); }
    .stat-card:nth-child(2)::before { background: var(--green); }
    .stat-card:nth-child(3)::before { background: var(--orange); }
    .stat-card:nth-child(4)::before { background: var(--purple); }

    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display:flex;align-items:center;justify-content:center; font-size: 1rem; }
    .stat-icon.blue { background: #eff6ff; color: var(--blue-accent); }
    .stat-icon.green { background: #f0fdf4; color: var(--green); }
    .stat-icon.orange { background: #fff7ed; color: var(--orange); }
    .stat-icon.purple { background: #f5f3ff; color: var(--purple); }

    .stat-badge { font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; }
    .badge-up { background: #dcfce7; color: #16a34a; }
    .badge-stable { background: #f0fdf4; color: #22c55e; }
    .badge-high { background: #fff7ed; color: var(--orange); }
    .badge-positive { background: #f5f3ff; color: var(--purple); }

    .stat-value { font-family: 'Syne', sans-serif; font-size: 1.7rem; font-weight: 700; }
    .stat-label { font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px; }

    /* Map Section */
    .map-section { background: var(--card); border-radius: 14px; border: 1px solid var(--border); margin-bottom: 24px; overflow: hidden; }
    .map-header { padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
    .map-header h3 { font-size: 1rem; font-weight: 700; display:flex;align-items:center;gap:8px; }
    #dashboardMap { height: 260px; }

    /* Content Grid */
    .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }

    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .section-header h3 { font-size: 1rem; font-weight: 700; }
    .view-all { font-size: 0.82rem; color: var(--teal); font-weight: 600; text-decoration: none; }

    /* Upcoming Bookings */
    .bookings-section { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }

    .booking-card-mini {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border);
        transition: box-shadow 0.2s;
    }

    .booking-card-mini:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

    .booking-card-img {
        height: 110px;
        background: linear-gradient(135deg, #1a2340, #2d3a5e);
        position: relative;
        overflow: hidden;
    }

    .booking-card-img img { width: 100%; height: 100%; object-fit: cover; }

    .booking-status-pill {
        position: absolute;
        top: 8px; left: 8px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .pill-confirmed { background: #22c55e; color: #fff; }
    .pill-pending { background: var(--orange); color: #fff; }

    .booking-card-body { padding: 12px; }
    .booking-card-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 6px; }
    .booking-card-meta { display:flex;align-items:center;gap:5px;font-size:0.78rem;color:var(--text-muted);margin-bottom:8px; }

    .booking-card-footer { display:flex;align-items:center;justify-content:space-between;margin-top:10px; }

    .booking-avatars { display:flex; }
    .booking-avatars img { width:24px;height:24px;border-radius:50%;border:2px solid #fff;margin-right:-6px; }
    .booking-count { width:24px;height:24px;border-radius:50%;background:var(--bg);border:2px solid #fff;font-size:0.68rem;font-weight:700;display:flex;align-items:center;justify-content:center;color:var(--text-muted); }

    .bookings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    /* Countdown timer */
    .countdown-wrap { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 8px 12px; margin-top: 8px; }
    .countdown-label { font-size: 0.72rem; color: var(--orange); font-weight: 600; margin-bottom: 3px; }
    .countdown-timer { font-size: 0.9rem; font-weight: 700; color: var(--text); font-variant-numeric: tabular-nums; }

    /* Waitout */
    .wait-text { font-size: 0.75rem; color: var(--text-muted); display:flex;align-items:center;gap:4px; }
    .wait-dot { width:6px;height:6px;border-radius:50%;background:var(--orange);animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.3} }

    /* Recent Listings */
    .recent-section { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }

    .listing-item { display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border); }
    .listing-item:last-child { border-bottom: none; padding-bottom: 0; }

    .listing-thumb { width:52px;height:52px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--bg); }

    .listing-info { flex:1; }
    .listing-name { font-size:0.9rem;font-weight:600;margin-bottom:2px; }
    .listing-address { font-size:0.75rem;color:var(--text-muted); }
    .listing-price { font-size:0.82rem;font-weight:700;color:var(--text); white-space:nowrap; }
    .listing-rating { font-size:0.75rem;color:var(--text-muted);display:flex;align-items:center;gap:3px; }
    .listing-rating i { color: var(--yellow); }

    .listing-price-block { text-align: right; }
    .price-mo { font-size: 0.68rem; color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="welcome-header">
    <h1>Dashboard</h1>
    <div class="date">
        <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
        Welcome back, <strong style="margin-left:2px">{{ auth()->user()->name }}!</strong>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue"><i class="fas fa-list"></i></div>
            <span class="stat-badge badge-up">+12%</span>
        </div>
        <div class="stat-label">Active Listings</div>
        <div class="stat-value">{{ $recentProperties->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <span class="stat-badge badge-stable">Stable</span>
        </div>
        <div class="stat-label">My Bookings</div>
        <div class="stat-value">{{ $bookings->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon orange"><i class="fas fa-heart"></i></div>
            <span class="stat-badge badge-high">Saved</span>
        </div>
        <div class="stat-label">Saved Boards</div>
        <div class="stat-value">{{ $savedCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon purple"><i class="fas fa-star"></i></div>
            <span class="stat-badge badge-positive">Pending</span>
        </div>
        <div class="stat-label">Pending Reviews</div>
        <div class="stat-value">{{ $bookings->where('status', 'pending')->count() }}</div>
    </div>
</div>

<!-- Map -->
<div class="map-section">
    <div class="map-header">
        <h3><span style="color:var(--teal)">●</span> Nearby Boarding Houses</h3>
        <a href="{{ route('search') }}" class="btn btn-sm btn-outline">Expand Map</a>
    </div>
    <div id="dashboardMap"></div>
</div>

<!-- Content Grid -->
<div class="content-grid">

    <!-- Upcoming Bookings -->
    <div class="bookings-section">
        <div class="section-header">
            <h3>Upcoming Bookings</h3>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:0.8rem;color:var(--text-muted)">Manage your active reservations</span>
                <a href="{{ route('bookings') }}" class="view-all">View History</a>
            </div>
        </div>
        <div class="bookings-grid">

            <!-- Confirmed booking -->
            @if($confirmedBooking)
            <div class="booking-card-mini">
                <div class="booking-card-img">
                    <img src="{{ $confirmedBooking->property->image ? Storage::url($confirmedBooking->property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&q=80' }}"
                         alt="{{ $confirmedBooking->property->title }}">
                    <span class="booking-status-pill pill-confirmed">CONFIRMED</span>
                </div>
                <div class="booking-card-body">
                    <div class="booking-card-title">
                        {{ $confirmedBooking->property->title }} - {{ ucfirst($confirmedBooking->room_type) }}
                    </div>
                    <div class="booking-card-meta">
                        <i class="fas fa-calendar-alt"></i>
                        Check-in: {{ \Carbon\Carbon::parse($confirmedBooking->check_in)->format('M d, Y') }}
                    </div>
                    <div class="countdown-wrap">
                        <div class="countdown-label">⏱ Move-out countdown</div>
                        <div class="countdown-timer" id="countdown-1">Calculating...</div>
                    </div>
                    <div class="booking-card-footer">
                        <div class="booking-avatars">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2ec4a5&color=fff" alt="">
                        </div>
                        <a href="{{ route('bookings') }}" class="btn btn-xs btn-outline">Details</a>
                    </div>
                </div>
            </div>
            @else
            <div class="booking-card-mini" style="display:flex;align-items:center;justify-content:center;padding:20px;color:var(--text-muted);font-size:0.85rem;">
                No confirmed bookings yet.
            </div>
            @endif

            <!-- Pending booking -->
            @if($pendingBooking)
            <div class="booking-card-mini">
                <div class="booking-card-img">
                    <img src="{{ $pendingBooking->property->image ? Storage::url($pendingBooking->property->image) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&q=80' }}"
                         alt="{{ $pendingBooking->property->title }}">
                    <span class="booking-status-pill pill-pending">PENDING</span>
                </div>
                <div class="booking-card-body">
                    <div class="booking-card-title">
                        {{ $pendingBooking->property->title }} - {{ ucfirst($pendingBooking->room_type) }}
                    </div>
                    <div class="booking-card-meta">
                        <i class="fas fa-calendar-alt"></i>
                        Check-in: {{ \Carbon\Carbon::parse($pendingBooking->check_in)->format('M d, Y') }}
                    </div>
                    <div class="wait-text">
                        <span class="wait-dot"></span>
                        Waiting for host approval...
                    </div>
                    <div class="booking-card-footer" style="margin-top:14px;">
                        <div class="booking-avatars">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=e8692a&color=fff" alt="">
                        </div>
                        <a href="{{ route('bookings') }}" class="btn btn-xs btn-outline">Details</a>
                    </div>
                </div>
            </div>
            @else
            <div class="booking-card-mini" style="display:flex;align-items:center;justify-content:center;padding:20px;color:var(--text-muted);font-size:0.85rem;">
                No pending bookings.
            </div>
            @endif

        </div>
    </div>

    <!-- Recent Listings -->
    <div class="recent-section">
        <div class="section-header">
            <h3>Recent Listings</h3>
        </div>

        @foreach($recentProperties as $property)
        <a href="{{ route('property.show', $property->id) }}" style="text-decoration:none;color:inherit;">
            <div class="listing-item">
                <img class="listing-thumb"
                     src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=80&q=80' }}"
                     alt="{{ $property->title }}">
                <div class="listing-info">
                    <div class="listing-name">{{ $property->title }}</div>
                    <div class="listing-address">{{ $property->address }}</div>
                    <div class="listing-rating">
                        <i class="fas fa-star"></i>
                        {{ number_format($property->reviews_avg_rating ?? 0, 1) }}
                    </div>
                </div>
                <div class="listing-price-block">
                    <div class="listing-price">₱{{ number_format($property->price, 0) }}</div>
                    <div class="price-mo">/mo</div>
                </div>
            </div>
        </a>
        @endforeach

        <a href="{{ route('search') }}" class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">
            Explore All Listings
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ─── Leaflet Map ───────────────────────────────────────────────
const map = L.map('dashboardMap', { zoomControl: true, scrollWheelZoom: false })
    .setView([7.4479, 125.8085], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const customIcon = L.divIcon({
    html: `<div style="background:#e8692a;color:#fff;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:700;font-family:DM Sans,sans-serif;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,0.2)">🏠</div>`,
    className: '', iconAnchor: [20, 16]
});

const places = [
    { name: 'Lola Doths Boarding House', lat: 7.4481, lng: 125.8020 },
    { name: 'M. Dormitory',              lat: 7.4460, lng: 125.8050 },
    { name: 'FR Boarding House',         lat: 7.4430, lng: 125.8035 },
    { name: "Yama's BH",                 lat: 7.4415, lng: 125.8060 },
    { name: "Inday's Boarding House",    lat: 7.4400, lng: 125.8040 },
    { name: 'Shalom Boarding House',     lat: 7.4470, lng: 125.8095 },
    { name: 'Rhys Room for Rent',        lat: 7.4488, lng: 125.8065 },
    { name: 'Galagala Boarding House',   lat: 7.4498, lng: 125.8075 },
    { name: 'RM Boarding House',         lat: 7.4510, lng: 125.8080 },
    { name: "Ekoyom's Boarding House",   lat: 7.4505, lng: 125.8090 },
];

places.forEach(p => {
    L.marker([p.lat, p.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(`<strong style="font-family:DM Sans,sans-serif">${p.name}</strong>`);
});

// ─── Real-time Countdown Timer ─────────────────────────────────
@if($confirmedBooking && $confirmedBooking->check_in)
@php
    $moveOut = \Carbon\Carbon::parse($confirmedBooking->check_in)->addMonths(6)->toIso8601String();
@endphp
function updateCountdown(elId, moveOutDate) {
    const el = document.getElementById(elId);
    if (!el) return;
    function tick() {
        const diff = new Date(moveOutDate) - new Date();
        if (diff <= 0) {
            el.textContent = '⚠️ Move-out date reached!';
            el.style.color = '#ef4444';
            return;
        }
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = `${d}d ${String(h).padStart(2,'0')}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    }
    tick();
    setInterval(tick, 1000);
}
updateCountdown('countdown-1', '{{ $moveOut }}');
@endif
</script>
@endpush