@extends('layouts.app')

@section('title', 'My Bookings')
@section('search-placeholder', 'Search bookings...')

@push('styles')
<style>
    .page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px; }
    .page-header h1 { font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700; }
    .page-header p { font-size:0.875rem;color:var(--text-muted);margin-top:3px; }

    .bookings-layout { display:grid;grid-template-columns:1fr 300px;gap:20px; }

    .tab-filters { display:flex;gap:4px;margin-bottom:20px;background:var(--card);border-radius:10px;padding:4px;border:1px solid var(--border);width:fit-content; }
    .tab-filter { padding:8px 18px;border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;font-weight:600;cursor:pointer;color:var(--text-muted);background:transparent;transition:all 0.2s; }
    .tab-filter.active { background:var(--teal);color:#fff; }
    .tab-filter:hover:not(.active) { background:var(--bg);color:var(--text); }

    .booking-item { background:var(--card);border-radius:14px;border:1px solid var(--border);margin-bottom:14px;overflow:hidden;transition:box-shadow 0.2s; }
    .booking-item:hover { box-shadow:0 4px 20px rgba(0,0,0,0.08); }

    .booking-main { display:flex; }
    .booking-img { width:160px;flex-shrink:0;position:relative;overflow:hidden; }
    .booking-img img { width:100%;height:100%;object-fit:cover;transition:transform 0.3s; }
    .booking-item:hover .booking-img img { transform:scale(1.04); }

    .booking-content { flex:1;padding:18px 20px;display:flex;flex-direction:column;justify-content:space-between; }
    .booking-top { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px; }
    .booking-name { font-size:1.05rem;font-weight:700;margin-bottom:2px; }
    .booking-addr { font-size:0.8rem;color:var(--text-muted);display:flex;align-items:center;gap:4px; }
    .booking-addr i { color:var(--teal); }

    .booking-meta { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:12px 0; }
    .meta-item label { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;font-weight:700;color:var(--text-muted);display:block;margin-bottom:2px; }
    .meta-item span { font-size:0.875rem;font-weight:600; }
    .meta-price { color:var(--blue-accent); }

    .booking-bottom { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }

    .booking-countdown { background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 12px;margin-bottom:10px;display:flex;align-items:center;gap:8px; }
    .bc-label { font-size:0.75rem;color:var(--orange);font-weight:700; }
    .bc-time { font-size:0.875rem;font-weight:800;font-variant-numeric:tabular-nums;color:var(--text); }

    [data-theme="dark"] .booking-countdown { background:rgba(255,247,237,0.08);border-color:rgba(254,215,170,0.25); }
    [data-theme="dark"] .bc-time { color:#fdba74; }
    [data-theme="dark"] .bc-label { color:#fb923c; }

    /* ── STATUS TRACKER ── */
    .status-tracker { padding:16px 20px;border-top:1px solid var(--border);background:var(--bg); }
    .tracker-label { font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:12px; }

    .tracker-steps { display:flex;align-items:flex-start;position:relative; }
    .tracker-steps::before {
        content:'';
        position:absolute;
        top:15px;
        left:15px;
        right:15px;
        height:2px;
        background:var(--border);
        z-index:0;
    }

    .tracker-step { flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:1; }

    .step-circle {
        width:30px;height:30px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        font-size:0.75rem;font-weight:700;
        border:2px solid var(--border);
        background:var(--card);
        color:var(--text-muted);
        transition:all 0.3s;
        margin-bottom:6px;
    }
    .step-circle.done             { background:var(--teal);border-color:var(--teal);color:#fff; }
    .step-circle.inactive         { background:var(--card);border-color:var(--border);color:var(--text-muted); }
    .step-circle.pending-active   { background:var(--orange);border-color:var(--orange);color:#fff;box-shadow:0 0 0 4px rgba(232,105,42,0.15); }
    .step-circle.confirmed-active { background:var(--teal);border-color:var(--teal);color:#fff;box-shadow:0 0 0 4px rgba(32,178,140,0.15); }
    .step-circle.completed-active { background:var(--blue-accent);border-color:var(--blue-accent);color:#fff;box-shadow:0 0 0 4px rgba(59,130,246,0.15); }
    .step-circle.rejected         { background:#ef4444;border-color:#ef4444;color:#fff; }
    .step-circle.cancelled        { background:#94a3b8;border-color:#94a3b8;color:#fff; }

    .step-label { font-size:0.7rem;font-weight:600;color:var(--text-muted);text-align:center;line-height:1.3; }
    .step-label.done             { color:var(--teal); }
    .step-label.pending-active   { color:var(--orange);font-weight:700; }
    .step-label.confirmed-active { color:var(--teal);font-weight:700; }
    .step-label.completed-active { color:var(--blue-accent);font-weight:700; }
    .step-label.rejected         { color:#ef4444; }
    .step-label.cancelled        { color:#94a3b8; }

    .tracker-line-fill {
    position:absolute;
    top:15px;
    left:15px;
    height:2px;
    background:var(--teal);
    z-index:0;
    transition:width 0.4s ease;
    }
    .tracker-line-fill.pending-line   { background:var(--orange); }
    .tracker-line-fill.confirmed-line { background:var(--teal); }
    .tracker-line-fill.completed-line { background:var(--blue-accent); }
    .tracker-line-fill.rejected       { background:#ef4444; }
    .tracker-line-fill.cancelled      { background:#94a3b8; }

    /* status message below tracker */
    .tracker-message { margin-top:10px;font-size:0.8rem;padding:8px 12px;border-radius:8px;display:flex;align-items:center;gap:6px;background:var(--bg);border:1px solid var(--border);color:var(--text); }
    .tracker-message.pending   { border-left:3px solid var(--orange); }
    .tracker-message.confirmed { border-left:3px solid var(--teal); }
    .tracker-message.completed { border-left:3px solid var(--blue-accent); }
    .tracker-message.rejected  { border-left:3px solid #ef4444; }
    .tracker-message.cancelled { border-left:3px solid #94a3b8; }

    [data-theme="dark"] .tracker-message.pending   { background:rgba(255,247,237,0.08);color:#fdba74;border-color:rgba(254,215,170,0.25); }
[data-theme="dark"] .tracker-message.confirmed { background:rgba(240,253,244,0.08);color:#86efac;border-color:rgba(134,239,172,0.25); }
[data-theme="dark"] .tracker-message.completed { background:rgba(239,246,255,0.08);color:#93c5fd;border-color:rgba(191,219,254,0.25); }
[data-theme="dark"] .tracker-message.rejected  { background:rgba(254,242,242,0.08);color:#fca5a5;border-color:rgba(252,165,165,0.25); }
[data-theme="dark"] .tracker-message.cancelled { background:rgba(248,250,252,0.08);color:#94a3b8;border-color:rgba(226,232,240,0.25); }


    /* Sidebar */
    .overview-card { background:var(--card);border-radius:14px;border:1px solid var(--border);padding:20px;margin-bottom:16px; }
    .overview-title { font-size:0.95rem;font-weight:700;margin-bottom:14px; }
    .overview-row { display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border); }
    .overview-row:last-child { border-bottom:none; }
    .ov-left { display:flex;align-items:center;gap:10px; }
    .ov-icon { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:0.85rem; }
    .ov-icon.blue   { background:#eff6ff;color:var(--blue-accent); }
    .ov-icon.green  { background:#f0fdf4;color:var(--green); }
    .ov-icon.purple { background:#f5f3ff;color:var(--purple); }
    .ov-label { font-size:0.875rem;font-weight:600; }
    .ov-val { font-size:0.95rem;font-weight:800; }

    .help-card { background:var(--blue-accent);border-radius:14px;padding:20px;margin-bottom:16px; }
    .help-title { font-size:1rem;font-weight:700;color:#fff;margin-bottom:8px; }
    .help-text { font-size:0.82rem;color:rgba(255,255,255,0.8);line-height:1.5;margin-bottom:14px; }
    .help-btn { width:100%;padding:10px;background:#fff;color:var(--blue-accent);border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;font-weight:700;cursor:pointer;transition:opacity 0.2s; }
    .help-btn:hover { opacity:0.9; }

    .minimap-card { background:var(--card);border-radius:14px;border:1px solid var(--border);overflow:hidden; }
    .minimap-card .map-footer { padding:10px 14px;display:flex;align-items:center;justify-content:space-between; }
    .view-map-btn { font-size:0.78rem;color:var(--teal);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px; }
    #bookingsMap { height:140px; }

    .empty-state { text-align:center;padding:60px 20px;color:var(--text-muted); }
    .empty-state i { font-size:3rem;margin-bottom:12px;opacity:0.3; }
    /* Avatar image support */
.conv-avatar img,
.chat-peer-avatar img,
.msg-avatar img,
.user-item .conv-avatar img {
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>My Bookings</h1>
        <p>Manage your current and upcoming stays</p>
    </div>
    <a href="#" class="btn btn-outline"><i class="fas fa-download"></i> Export History</a>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-weight:600;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="bookings-layout">
    <div>
        <div class="tab-filters">
            <button class="tab-filter active" onclick="filterBookings('all',this)">All</button>
            <button class="tab-filter" onclick="filterBookings('pending',this)">Pending</button>
            <button class="tab-filter" onclick="filterBookings('confirmed',this)">Confirmed</button>
            <button class="tab-filter" onclick="filterBookings('completed',this)">Completed</button>
            <button class="tab-filter" onclick="filterBookings('cancelled',this)">Cancelled</button>
        </div>

        <div id="bookingsList">
            @forelse($bookings as $booking)
            @php
                $prop  = $booking->property;
                $image = $prop->image
                    ? Storage::url($prop->image)
                    : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=300&q=80';

                $statusClass = match($booking->status) {
                    'confirmed' => 'badge-confirmed',
                    'pending'   => 'badge-pending',
                    'completed' => 'badge-completed',
                    'cancelled' => 'badge-cancelled',
                    'rejected'  => 'badge-cancelled',
                    default     => 'badge-pending',
                };

                // Steps: submitted → under review → confirmed → completed
                // Step states: done, active, inactive, rejected, cancelled
                $steps = match($booking->status) {
                'pending'   => ['done','pending-active','inactive','inactive'],
                'confirmed' => ['done','done','confirmed-active','inactive'],
                'completed' => ['done','done','done','completed-active'],
                'rejected'  => ['done','rejected','inactive','inactive'],
                'cancelled' => ['cancelled','inactive','inactive','inactive'],
                default     => ['done','pending-active','inactive','inactive'],
            };

                // Line fill width between steps (0%, 33%, 66%, 100%)
                $lineWidth = match($booking->status) {
                    'pending'   => '33%',
                    'confirmed' => '66%',
                    'completed' => '100%',
                    'rejected'  => '33%',
                    'cancelled' => '0%',
                    default     => '33%',
                };

                $lineClass = match($booking->status) {
    'pending'   => 'pending-line',
    'confirmed' => 'confirmed-line',
    'completed' => 'completed-line',
    'rejected'  => 'rejected',
    'cancelled' => 'cancelled',
    default     => 'pending-line',
};

                $stepLabels = ['Submitted', 'Under Review', 'Confirmed', 'Completed'];
                $stepIcons  = ['fas fa-paper-plane', 'fas fa-search', 'fas fa-check', 'fas fa-home'];

                $trackerMessages = [
                    'pending'   => ['icon' => 'fas fa-clock',        'text' => 'Your booking is under review. Admin will contact you within 24 hours.'],
                    'confirmed' => ['icon' => 'fas fa-check-circle',  'text' => 'Your booking is confirmed! Prepare for your move-in date.'],
                    'completed' => ['icon' => 'fas fa-star',          'text' => 'Stay completed. Don\'t forget to leave a review!'],
                    'rejected'  => ['icon' => 'fas fa-times-circle',  'text' => 'Your booking was not approved. Try another property.'],
                    'cancelled' => ['icon' => 'fas fa-ban',           'text' => 'You cancelled this booking request.'],
                ];
                $msg = $trackerMessages[$booking->status] ?? $trackerMessages['pending'];
            @endphp

            <div class="booking-item" data-status="{{ $booking->status }}">
                <div class="booking-main">
                    <div class="booking-img">
                        <img src="{{ $image }}" alt="{{ $prop->title }}">
                    </div>
                    <div class="booking-content">
                        <div>
                            <div class="booking-top">
                                <div>
                                    <div class="booking-name">{{ $prop->title }}</div>
                                    <div class="booking-addr">
                                        <i class="fas fa-map-marker-alt"></i> {{ $prop->address }}
                                    </div>
                                </div>
                                <span class="badge {{ $statusClass }}">{{ strtoupper($booking->status) }}</span>
                            </div>

                            <div class="booking-meta">
                                @if($booking->status === 'completed')
                                <div class="meta-item">
                                    <label>Stay Period</label>
                                    <span>{{ \Carbon\Carbon::parse($booking->start_date)->format('M d') }} – {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</span>
                                </div>
                                @else
                                <div class="meta-item">
                                    <label>Check-in</label>
                                    <span>{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</span>
                                </div>
                                @endif
                                <div class="meta-item">
                                    <label>Monthly Rate</label>
                                    <span class="meta-price">₱{{ number_format($prop->price, 0) }}/mo</span>
                                </div>
                            </div>

                            @if($booking->status === 'confirmed')
                            <div class="booking-countdown">
                                <span class="bc-label">⏱ Move-out countdown:</span>
                               <span class="bc-time" data-moveout="{{ \Carbon\Carbon::parse($booking->end_date)->toDateTimeString() }}">Loading...</span>
                            </div>
                            @endif
                        </div>

                        <div class="booking-bottom">
                            <a href="{{ route('property.show', $prop->id) }}" class="btn btn-sm btn-primary">View Property</a>

                            @if($booking->status === 'confirmed')
                            <a href="{{ route('chat') }}" class="btn btn-sm btn-outline">
                                <i class="fas fa-comment"></i> Message Host
                            </a>
                            @endif

                            @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('bookings.cancel', $booking->id) }}" onsubmit="return confirm('Cancel this booking?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline" style="color:var(--red);border-color:var(--red)">
                                    <i class="fas fa-times"></i> Cancel Request
                                </button>
                            </form>
                            @endif

                            @if($booking->status === 'completed')
                            <a href="{{ route('reviews') }}" class="btn btn-sm btn-green">
                                <i class="fas fa-star"></i> Leave Review
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── STATUS TRACKER ── --}}
                <div class="status-tracker">
                    <div class="tracker-label">Booking Status</div>
                    <div class="tracker-steps" style="position:relative;">
                        {{-- Filled line --}}
                        <div class="tracker-line-fill {{ $lineClass }}" style="width:{{ $lineWidth }}"></div>

                        @foreach($stepLabels as $i => $label)
                        @php
                            $state = $steps[$i];
                        @endphp
                        <div class="tracker-step">
                            <div class="step-circle {{ $state }}">
                                @if($state === 'done')
                                    <i class="fas fa-check"></i>
                                @elseif($state === 'rejected')
                                    <i class="fas fa-times"></i>
                                @elseif($state === 'cancelled')
                                    <i class="fas fa-ban"></i>
                                @else
                                    <i class="{{ $stepIcons[$i] }}"></i>
                                @endif
                            </div>
                            <div class="step-label {{ $state }}">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Status message --}}
                    <div class="tracker-message {{ $booking->status }}">
                        <i class="{{ $msg['icon'] }}"></i>
                        <span>{{ $msg['text'] }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p style="font-size:1rem;font-weight:600;margin-bottom:6px;">No bookings yet</p>
                <p style="font-size:0.875rem;">Find a boarding house and make your first reservation.</p>
                <a href="{{ route('search') }}" class="btn btn-primary" style="margin-top:16px;display:inline-flex;">Browse Properties</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar --}}
    <div>
        <div class="overview-card">
            <div class="overview-title">Booking Overview</div>
            <div class="overview-row">
                <div class="ov-left">
                    <div class="ov-icon blue"><i class="fas fa-calendar-check"></i></div>
                    <span class="ov-label">Active Stays</span>
                </div>
                <span class="ov-val">{{ str_pad($activeCount, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="overview-row">
                <div class="ov-left">
                    <div class="ov-icon green"><i class="fas fa-check-circle"></i></div>
                    <span class="ov-label">Past Stays</span>
                </div>
                <span class="ov-val">{{ str_pad($pastCount, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="overview-row">
                <div class="ov-left">
                    <div class="ov-icon purple"><i class="fas fa-coins"></i></div>
                    <span class="ov-label">Total Bookings</span>
                </div>
                <span class="ov-val">{{ str_pad($bookings->count(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        {{-- Legend card --}}
        <div class="overview-card">
            <div class="overview-title">Status Guide</div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:0.82rem;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--orange);flex-shrink:0;"></div>
                    <span><strong>Pending</strong> — Waiting for admin review</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--teal);flex-shrink:0;"></div>
                    <span><strong>Confirmed</strong> — Approved, ready to move in</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--blue-accent);flex-shrink:0;"></div>
                    <span><strong>Completed</strong> — Stay finished</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:#ef4444;flex-shrink:0;"></div>
                    <span><strong>Rejected</strong> — Not approved</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:#94a3b8;flex-shrink:0;"></div>
                    <span><strong>Cancelled</strong> — You cancelled</span>
                </div>
            </div>
        </div>

        <div class="help-card">
            <div class="help-title">Need Help?</div>
            <div class="help-text">Having issues with your booking or payment? Our concierge team is here 24/7.</div>
            <button class="help-btn" onclick="window.location.href='{{ route('chat') }}'">
                <i class="fas fa-comments"></i> Chat with Support
            </button>
        </div>

        <div class="minimap-card">
            <div id="bookingsMap"></div>
            <div class="map-footer">
                <a href="{{ route('search') }}" class="view-map-btn">
                    <i class="fas fa-map-marked-alt"></i> VIEW MAP LOCATION
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Countdown timers
document.querySelectorAll('.bc-time[data-moveout]').forEach(el => {
    const moveOut = new Date(el.dataset.moveout);
    function tick() {
        const diff = moveOut - new Date();
        if (diff <= 0) { el.textContent = 'Move-out reached!'; el.style.color='#ef4444'; return; }
        const d = Math.floor(diff/86400000);
        const h = Math.floor((diff%86400000)/3600000);
        const m = Math.floor((diff%3600000)/60000);
        const s = Math.floor((diff%60000)/1000);
        el.textContent = `${d}d ${String(h).padStart(2,'0')}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    }
    tick(); setInterval(tick, 1000);
});

// Tab filter
function filterBookings(status, btn) {
    document.querySelectorAll('.tab-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.booking-item').forEach(item => {
        item.style.display = (status === 'all' || item.dataset.status === status) ? 'block' : 'none';
    });
}

// Mini map
const bmap = L.map('bookingsMap', { zoomControl:false, dragging:false, scrollWheelZoom:false })
    .setView([7.4479, 125.8085], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(bmap);
const mini = L.divIcon({
    html:`<div style="background:#e8692a;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,0.3)">🏠</div>`,
    className:'', iconAnchor:[12,12]
});

@foreach($bookings as $booking)
@if($booking->property->latitude && $booking->property->longitude)
L.marker([{{ $booking->property->latitude }}, {{ $booking->property->longitude }}], { icon: mini })
    .addTo(bmap)
    .bindPopup('{{ addslashes($booking->property->title) }}');
@endif
@endforeach
</script>
@endpush