@extends('layouts.app')

@section('title', 'Dashboard')
@section('search-placeholder', 'Search boarding houses...')

@push('styles')
<style>
    .welcome-header { margin-bottom: 24px; }
    .welcome-header h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 700; }
    .welcome-header .date { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; display:flex;align-items:center;gap:6px; }

    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
    .stat-card { background: var(--card); border-radius: 14px; padding: 18px 20px; border: 1px solid var(--border); position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .stat-card:nth-child(1)::before { background: var(--blue-accent); }
    .stat-card:nth-child(2)::before { background: var(--green); }
    .stat-card:nth-child(3)::before { background: var(--orange); }
    .stat-card:nth-child(4)::before { background: var(--purple); }
    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display:flex;align-items:center;justify-content:center; font-size: 1rem; }
    .stat-icon.blue   { background: #eff6ff; color: var(--blue-accent); }
    .stat-icon.green  { background: #f0fdf4; color: var(--green); }
    .stat-icon.orange { background: #fff7ed; color: var(--orange); }
    .stat-icon.purple { background: #f5f3ff; color: var(--purple); }
    .stat-badge { font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; }
    .badge-up       { background: #dcfce7; color: #16a34a; }
    .badge-stable   { background: #f0fdf4; color: #22c55e; }
    .badge-high     { background: #fff7ed; color: var(--orange); }
    .badge-positive { background: #f5f3ff; color: var(--purple); }
    .stat-value { font-family: 'Syne', sans-serif; font-size: 1.7rem; font-weight: 700; }
    .stat-label { font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px; }
    .dashboard-link-card { color: inherit; text-decoration: none; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease; display: block; }
    .dashboard-link-card:hover { transform: translateY(-3px); box-shadow: 0 18px 35px rgba(14, 165, 233, 0.12); }

    .quick-actions { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
    .quick-action { background: rgba(255,255,255,0.74); border: 1px solid var(--glass-border); border-radius: 16px; padding: 15px 16px; display:flex; align-items:center; gap: 12px; color: var(--text); text-decoration:none; box-shadow: 0 12px 26px rgba(14,165,233,0.08); transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
    .quick-action:hover { transform: translateY(-2px); border-color: rgba(6,182,212,.42); box-shadow: 0 18px 36px rgba(14,165,233,.15); }
    .quick-action-icon { width: 42px; height: 42px; border-radius: 13px; display:flex; align-items:center; justify-content:center; color:#fff; background: linear-gradient(135deg,#0ea5e9,#06b6d4); box-shadow: 0 12px 24px rgba(6,182,212,.24); flex-shrink:0; }
    .quick-action strong { display:block; font-size:.88rem; }
    .quick-action span span { display:block; color:var(--text-muted); font-size:.74rem; margin-top:2px; }

    .map-section { background: var(--glass-card); border-radius: 18px; border: 1px solid var(--glass-border); margin-bottom: 24px; overflow: hidden; box-shadow: var(--glass-shadow); backdrop-filter: blur(18px); }
    .map-header { padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
    .map-header h3 { font-size: 1rem; font-weight: 700; display:flex;align-items:center;gap:8px; }
    .map-title-dot { width:9px;height:9px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#06b6d4);box-shadow:0 0 0 5px rgba(6,182,212,0.12);display:inline-block; }
    #dashboardMap { height: 300px; z-index: 1; }
    .be-map-marker { position:relative; background:linear-gradient(135deg,#0ea5e9,#06b6d4); color:#fff; border:2px solid rgba(255,255,255,0.94); border-radius:999px; padding:7px 10px; font:900 12px 'DM Sans',sans-serif; box-shadow:0 14px 30px rgba(14,165,233,0.32); white-space:nowrap; transform:translate(-50%,-50%); }
    .be-map-marker::after { content:''; position:absolute; left:50%; bottom:-7px; width:10px; height:10px; background:#06b6d4; border-right:2px solid rgba(255,255,255,0.94); border-bottom:2px solid rgba(255,255,255,0.94); transform:translateX(-50%) rotate(45deg); border-radius:2px; }
    .be-map-marker.is-active { background:linear-gradient(135deg,#0369a1,#06b6d4); transform:translate(-50%,-50%) scale(1.08); }
    .dashboard-map-popup { min-width:200px; font-family:'DM Sans',sans-serif; }
    .dashboard-map-popup img { width:100%; height:92px; object-fit:cover; border-radius:10px; margin-bottom:8px; }
    .dashboard-map-popup .popup-title { font-weight:900; color:#0f2741; margin-bottom:3px; }
    .dashboard-map-popup .popup-address { font-size:0.76rem; color:#64748b; line-height:1.35; margin-bottom:7px; }
    .dashboard-map-popup .popup-price { color:#0284c7; font-weight:900; }
    .dashboard-map-popup .popup-link { color:#0284c7; font-weight:900; text-decoration:none; }

    .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .section-header h3 { font-size: 1rem; font-weight: 700; }
    .view-all { font-size: 0.82rem; color: var(--teal); font-weight: 600; text-decoration: none; }

    .bookings-section { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }
    .booking-card-mini { border-radius: 12px; overflow: hidden; border: 1px solid var(--border); transition: box-shadow 0.2s; }
    .booking-card-mini:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .booking-card-img { height: 110px; background: linear-gradient(135deg, #1a2340, #2d3a5e); position: relative; overflow: hidden; }
    .booking-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .booking-status-pill { position: absolute; top: 8px; left: 8px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
    .pill-confirmed { background: #22c55e; color: #fff; }
    .pill-pending   { background: var(--orange); color: #fff; }
    .booking-card-body  { padding: 12px; }
    .booking-card-title { font-size: 0.9rem; font-weight: 700; margin-bottom: 6px; }
    .booking-card-meta  { display:flex;align-items:center;gap:5px;font-size:0.78rem;color:var(--text-muted);margin-bottom:8px; }
    .booking-card-footer { display:flex;align-items:center;justify-content:space-between;margin-top:10px; }
    .booking-avatars img { width:24px;height:24px;border-radius:50%;border:2px solid #fff; }
    .bookings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .countdown-wrap  { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 8px 12px; margin-top: 8px; }
    .countdown-label { font-size: 0.72rem; color: var(--orange); font-weight: 600; margin-bottom: 3px; }
    .countdown-timer { font-size: 0.9rem; font-weight: 700; color: var(--text); font-variant-numeric: tabular-nums; }
    .wait-text { font-size: 0.75rem; color: var(--text-muted); display:flex;align-items:center;gap:4px; }
    .wait-dot { width:6px;height:6px;border-radius:50%;background:var(--orange);animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.3} }

    .recent-section { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }
    .listing-item { display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border); }
    .listing-item:last-child { border-bottom: none; padding-bottom: 0; }
    .listing-thumb { width:52px;height:52px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--bg); }
    .listing-info { flex:1; }
    .listing-name    { font-size:0.9rem;font-weight:600;margin-bottom:2px; }
    .listing-address { font-size:0.75rem;color:var(--text-muted); }
    .listing-rating  { font-size:0.75rem;color:var(--text-muted);display:flex;align-items:center;gap:3px; }
    .listing-rating i { color: var(--yellow); }
    .listing-price-block { text-align: right; }
    .listing-price { font-size:0.82rem;font-weight:700;color:var(--text); white-space:nowrap; }
    .price-mo { font-size: 0.68rem; color: var(--text-muted); }
    @media (max-width: 1100px) {
        .stats-grid,
        .quick-actions { grid-template-columns: repeat(2, 1fr); }
        .content-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid,
        .quick-actions { grid-template-columns: 1fr; }
        .map-header { align-items: flex-start; flex-direction: column; gap: 10px; }
        #dashboardMap { height: 340px; }
    }
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

{{-- Stats --}}
<div class="stats-grid">
    <a href="{{ route('search') }}" class="stat-card dashboard-link-card" aria-label="Browse active listings">
        <div class="stat-top"><div class="stat-icon blue"><i class="fas fa-list"></i></div><span class="stat-badge badge-up">Live</span></div>
        <div class="stat-label">Active Listings</div>
        <div class="stat-value">{{ $recentProperties->count() }}</div>
    </a>
    <a href="{{ route('bookings.index') }}" class="stat-card dashboard-link-card" aria-label="Open my bookings">
        <div class="stat-top"><div class="stat-icon green"><i class="fas fa-calendar-check"></i></div><span class="stat-badge badge-stable">Stable</span></div>
        <div class="stat-label">My Bookings</div>
        <div class="stat-value">{{ $bookings->count() }}</div>
    </a>
    <a href="{{ route('favorites') }}" class="stat-card dashboard-link-card" aria-label="Open saved boards">
        <div class="stat-top"><div class="stat-icon orange"><i class="fas fa-heart"></i></div><span class="stat-badge badge-high">Saved</span></div>
        <div class="stat-label">Saved Boards</div>
        <div class="stat-value">{{ $savedCount }}</div>
    </a>
    <a href="{{ route('bookings.index') }}" class="stat-card dashboard-link-card" aria-label="Open pending bookings">
        <div class="stat-top"><div class="stat-icon purple"><i class="fas fa-clock"></i></div><span class="stat-badge badge-positive">Pending</span></div>
        <div class="stat-label">Pending Bookings</div>
        <div class="stat-value">{{ $bookings->where('status', 'pending')->count() }}</div>
    </a>
</div>

<div class="quick-actions">
    <a href="{{ route('search') }}#searchMap" class="quick-action">
        <span class="quick-action-icon"><i class="fas fa-map-location-dot"></i></span>
        <span><strong>Focus Map View</strong><span>Explore nearby rooms</span></span>
    </a>
    <a href="{{ route('chat') }}" class="quick-action">
        <span class="quick-action-icon"><i class="fas fa-headset"></i></span>
        <span><strong>Need Help?</strong><span>Open chat support</span></span>
    </a>
    <a href="{{ route('bookings.index') }}" class="quick-action">
        <span class="quick-action-icon"><i class="fas fa-calendar-days"></i></span>
        <span><strong>My Bookings</strong><span>Track reservations</span></span>
    </a>
    <a href="{{ route('favorites') }}" class="quick-action">
        <span class="quick-action-icon"><i class="fas fa-heart"></i></span>
        <span><strong>Saved Boards</strong><span>Review favorites</span></span>
    </a>
</div>

{{-- Map: uses REAL DB coordinates --}}
<div class="map-section">
    <div class="map-header">
        <h3><span class="map-title-dot"></span> Nearby Boarding Houses</h3>
        <a href="{{ route('search') }}#searchMap" class="btn btn-sm btn-outline">
            <i class="fas fa-map-location-dot"></i> Open Full Map
        </a>
    </div>
    <div id="dashboardMap"></div>
</div>

{{-- Content Grid --}}
<div class="content-grid">

    {{-- Upcoming Bookings --}}
    <div class="bookings-section">
        <div class="section-header">
            <h3>Upcoming Bookings</h3>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:0.8rem;color:var(--text-muted)">Manage your active reservations</span>
                <a href="{{ route('bookings.index') }}" class="view-all">View History</a>
            </div>
        </div>
        <div class="bookings-grid">

            {{-- Confirmed booking --}}
            @if($confirmedBooking)
            <div class="booking-card-mini">
                <div class="booking-card-img">
                    <img src="{{ $confirmedBooking->property->image ? Storage::url($confirmedBooking->property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&q=80' }}"
                         alt="{{ $confirmedBooking->property->title }}">
                    <span class="booking-status-pill pill-confirmed">CONFIRMED</span>
                </div>
                <div class="booking-card-body">
                    {{-- FIX: use property->title and property->room_type (not booking->room_type) --}}
                    <div class="booking-card-title">
                        {{ $confirmedBooking->property->title }}
                        @if($confirmedBooking->property->room_type)
                            - {{ ucfirst($confirmedBooking->property->room_type) }}
                        @endif
                    </div>
                    {{-- FIX: use start_date not check_in --}}
                    <div class="booking-card-meta">
                        <i class="fas fa-calendar-alt"></i>
                        Check-in: {{ \Carbon\Carbon::parse($confirmedBooking->start_date)->format('M d, Y') }}
                    </div>
                    <div class="countdown-wrap">
                        <div class="countdown-label">⏱ Move-out countdown</div>
                        <div class="countdown-timer" id="countdown-confirmed">Calculating...</div>
                    </div>
                    <div class="booking-card-footer">
                        <div class="booking-avatars">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2ec4a5&color=fff" alt="">
                        </div>
                        <a href="{{ route('bookings.index') }}" class="btn btn-xs btn-outline">Details</a>
                    </div>
                </div>
            </div>
            @else
            <div class="booking-card-mini" style="display:flex;align-items:center;justify-content:center;min-height:140px;padding:20px;color:var(--text-muted);font-size:0.85rem;flex-direction:column;gap:8px;">
                <i class="fas fa-calendar-check" style="font-size:1.5rem;opacity:0.3;"></i>
                No confirmed bookings yet.
            </div>
            @endif

            {{-- Pending booking --}}
            @if($pendingBooking)
            <div class="booking-card-mini">
                <div class="booking-card-img">
                    <img src="{{ $pendingBooking->property->image ? Storage::url($pendingBooking->property->image) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&q=80' }}"
                         alt="{{ $pendingBooking->property->title }}">
                    <span class="booking-status-pill pill-pending">PENDING</span>
                </div>
                <div class="booking-card-body">
                    {{-- FIX: use property->title and property->room_type --}}
                    <div class="booking-card-title">
                        {{ $pendingBooking->property->title }}
                        @if($pendingBooking->property->room_type)
                            - {{ ucfirst($pendingBooking->property->room_type) }}
                        @endif
                    </div>
                    {{-- FIX: use start_date not check_in --}}
                    <div class="booking-card-meta">
                        <i class="fas fa-calendar-alt"></i>
                        Check-in: {{ \Carbon\Carbon::parse($pendingBooking->start_date)->format('M d, Y') }}
                    </div>
                    <div class="wait-text">
                        <span class="wait-dot"></span>
                        Waiting for owner approval...
                    </div>
                    <div class="booking-card-footer" style="margin-top:14px;">
                        <div class="booking-avatars">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=e8692a&color=fff" alt="">
                        </div>
                        <form method="POST" action="{{ route('bookings.cancel', $pendingBooking->id) }}" onsubmit="return confirm('Cancel this booking?')">
                            @csrf
                            <button type="submit" class="btn btn-xs" style="background:transparent;color:#ef4444;border:1px solid #ef4444;border-radius:6px;padding:4px 10px;font-size:0.75rem;cursor:pointer;">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="booking-card-mini" style="display:flex;align-items:center;justify-content:center;min-height:140px;padding:20px;color:var(--text-muted);font-size:0.85rem;flex-direction:column;gap:8px;">
                <i class="fas fa-clock" style="font-size:1.5rem;opacity:0.3;"></i>
                No pending bookings.
            </div>
            @endif

        </div>
    </div>

    {{-- Recent Listings --}}
    <div class="recent-section">
        <div class="section-header">
            <h3>Recent Listings</h3>
        </div>

        @forelse($recentProperties as $property)
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
        @empty
        <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:0.85rem;">
            No listings found.
        </div>
        @endforelse

        <a href="{{ route('search') }}" class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">
            Explore All Listings
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ─── Leaflet Map: REAL coordinates from DB ────────────────────────────────────
@php
    $mapData = $recentProperties->map(function($p) {
        return [
            'id'      => $p->id,
            'name'    => $p->title,
            'address' => $p->address,
            'price'   => number_format($p->price, 0),
            'lat'     => $p->latitude,
            'lng'     => $p->longitude,
            'image'   => $p->image ? Storage::url($p->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&q=80',
            'url'     => route('property.show', $p->id),
        ];
    })->filter(function($p) {
        return $p['lat'] && $p['lng'];
    })->values();
@endphp
const mapProperties = @json($mapData);

const map = L.map('dashboardMap', { zoomControl: true, scrollWheelZoom: true });

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

if (mapProperties.length > 0) {
    const bounds = [];
    mapProperties.forEach(p => {
        const propertyIcon = L.divIcon({
            html: `<div class="be-map-marker">&#8369;${p.price}</div>`,
            className: '',
            iconSize: [1, 1],
            iconAnchor: [0, 0]
        });

        const marker = L.marker([p.lat, p.lng], { icon: propertyIcon }).addTo(map);
        marker.bindPopup(`
            <div class="dashboard-map-popup">
                <img src="${p.image}" alt="">
                <div class="popup-title">${p.name}</div>
                <div class="popup-address">${p.address}</div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <span class="popup-price">&#8369;${p.price}/mo</span>
                    <a class="popup-link" href="${p.url}">View</a>
                </div>
            </div>
        `);
        bounds.push([p.lat, p.lng]);
    });
    map.fitBounds(bounds, { padding: [30, 30] });
} else {
    map.setView([7.4479, 125.8085], 13);
}

setTimeout(() => map.invalidateSize(), 120);
@if($confirmedBooking)
@php $moveOut = \Carbon\Carbon::parse($confirmedBooking->end_date)->toIso8601String(); @endphp
(function() {
    const el = document.getElementById('countdown-confirmed');
    if (!el) return;
    function tick() {
        const diff = new Date('{{ $moveOut }}') - new Date();
        if (diff <= 0) { el.textContent = '⚠️ Move-out date reached!'; el.style.color = '#ef4444'; return; }
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = `${d}d ${String(h).padStart(2,'0')}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    }
    tick();
    setInterval(tick, 1000);
})();
@endif
</script>
@endpush
