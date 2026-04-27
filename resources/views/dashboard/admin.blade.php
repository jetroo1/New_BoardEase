@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('search-placeholder', 'Search properties, tenants, or bookings...')

@push('styles')
<style>
    .welcome-header { margin-bottom: 24px; }
    .welcome-header h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 700; }
    .welcome-header .date { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; display:flex;align-items:center;gap:6px; }

    .stats-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
    .stat-card { background:var(--card);border-radius:14px;padding:18px 20px;border:1px solid var(--border);position:relative;overflow:hidden; }
    .stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px; }
    .stat-card:nth-child(1)::before { background:var(--blue-accent); }
    .stat-card:nth-child(2)::before { background:var(--green); }
    .stat-card:nth-child(3)::before { background:var(--orange); }
    .stat-card:nth-child(4)::before { background:var(--purple); }
    .stat-top { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px; }
    .stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem; }
    .stat-icon.blue   { background:#eff6ff;color:var(--blue-accent); }
    .stat-icon.green  { background:#f0fdf4;color:var(--green); }
    .stat-icon.orange { background:#fff7ed;color:var(--orange); }
    .stat-icon.purple { background:#f5f3ff;color:var(--purple); }
    .stat-badge { font-size:0.72rem;font-weight:700;padding:3px 8px;border-radius:20px; }
    .badge-up     { background:#dcfce7;color:#16a34a; }
    .badge-stable { background:#f0fdf4;color:#22c55e; }
    .badge-high   { background:#fff7ed;color:var(--orange); }
    .badge-rev    { background:#f5f3ff;color:var(--purple); }
    .stat-value { font-family:'Syne',sans-serif;font-size:1.7rem;font-weight:700; }
    .stat-label { font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:4px; }

    .content-grid { display:grid;grid-template-columns:1fr 300px;gap:20px; }

    /* Listings Table */
    .listings-card { background:var(--card);border-radius:14px;border:1px solid var(--border);padding:20px; }
    .table-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
    .table-header h3 { font-size:1rem;font-weight:700; }
    .listings-table { width:100%;border-collapse:collapse; }
    .listings-table th { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:700;padding:8px 12px;text-align:left;border-bottom:1px solid var(--border); }
    .listings-table td { padding:12px;border-bottom:1px solid var(--border);font-size:0.875rem; }
    .listings-table tr:last-child td { border-bottom:none; }
    .listings-table tbody tr:hover { background:var(--bg); }
    .prop-cell { display:flex;align-items:center;gap:10px; }
    .prop-thumb { width:40px;height:40px;border-radius:8px;object-fit:cover;background:var(--bg); }
    .prop-name { font-weight:600;font-size:0.875rem; }
    .price-cell { color:var(--blue-accent);font-weight:700; }
    .action-link { color:var(--teal);font-size:0.82rem;font-weight:600;text-decoration:none;margin-right:8px; }
    .action-link:hover { text-decoration:underline; }
    .add-listing-row { text-align:center;padding:20px; }
    .add-listing-btn { color:var(--blue-accent);font-size:0.875rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:2px dashed var(--border);border-radius:10px;padding:10px 24px;transition:all 0.2s; }
    .add-listing-btn:hover { border-color:var(--blue-accent);background:#eff6ff; }

    /* Requests */
    .requests-card { background:var(--card);border-radius:14px;border:1px solid var(--border);padding:20px; }
    .requests-card h3 { font-size:1rem;font-weight:700;margin-bottom:16px; }
    .request-item { border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px;border-left:3px solid var(--border);transition:all 0.2s; }
    .request-item.pending-l   { border-left-color:var(--orange); }
    .request-item.confirmed-l { border-left-color:var(--green); }
    .request-item.rejected-l  { border-left-color:#ef4444;opacity:0.6; }
    .req-top { display:flex;align-items:center;gap:10px;margin-bottom:10px; }
    .req-avatar { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;color:#fff;flex-shrink:0;background:var(--teal); }
    .req-info { flex:1; }
    .req-name { font-size:0.875rem;font-weight:700; }
    .req-prop { font-size:0.75rem;color:var(--text-muted); }
    .req-date { font-size:0.72rem;color:var(--text-muted);margin-top:2px; }
    .req-actions { display:flex;gap:6px; }
    .btn-approve { background:#1a2340;color:#fff;border:none;border-radius:7px;padding:6px 13px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:background 0.2s; }
    .btn-approve:hover { background:#243050; }
    .btn-approve.approved { background:var(--green); cursor:default; }
    .btn-reject { background:transparent;color:var(--text-muted);border:1.5px solid var(--border);border-radius:7px;padding:6px 13px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s; }
    .btn-reject:hover { border-color:var(--red);color:var(--red); }
    .view-all-btn { width:100%;padding:10px;background:transparent;border:1.5px solid var(--border);border-radius:9px;font-family:'DM Sans',sans-serif;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;margin-top:4px; }
    .view-all-btn:hover { border-color:var(--navy);background:var(--bg); }

    /* Flash */
    .flash-success { background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px; }
    .flash-error   { background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;display:flex;align-items:center;gap:8px; }

    .empty-requests { text-align:center;padding:30px 0;color:var(--text-muted);font-size:0.875rem; }
    .empty-requests i { font-size:2rem;margin-bottom:8px;opacity:0.3;display:block; }
</style>
@endpush

@section('content')
<div class="welcome-header">
    <h1>Welcome, {{ $user->name }}!</h1>
    <div class="date">
        <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
        <span id="liveDate">Loading date...</span>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue"><i class="fas fa-list-alt"></i></div>
            <span class="stat-badge badge-up">Live</span>
        </div>
        <div class="stat-label">Total Listings</div>
        <div class="stat-value">{{ $totalListings }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <span class="stat-badge badge-stable">Stable</span>
        </div>
        <div class="stat-label">Active Bookings</div>
        <div class="stat-value">{{ $activeBookings }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            <span class="stat-badge badge-high">{{ $pendingRequests > 0 ? 'High' : 'Clear' }}</span>
        </div>
        <div class="stat-label">Pending Requests</div>
        <div class="stat-value">{{ $pendingRequests }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon purple"><i class="fas fa-peso-sign"></i></div>
            <span class="stat-badge badge-rev">Monthly</span>
        </div>
        <div class="stat-label">Monthly Revenue</div>
        <div class="stat-value">₱{{ number_format($monthlyRevenue, 0) }}</div>
    </div>
</div>

{{-- Content Grid --}}
<div class="content-grid">

    {{-- Listings Table --}}
    <div class="listings-card">
        <div class="table-header">
            <h3>My Listings</h3>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('properties.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>

        <table class="listings-table">
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Bookings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                @php
                    $thumb = $listing->image
                        ? Storage::url($listing->image)
                        : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=80&q=80';
                    $activeCount = $listing->bookings->where('status', 'confirmed')->count();
                @endphp
                <tr>
                    <td>
                        <div class="prop-cell">
                            <img class="prop-thumb" src="{{ $thumb }}" alt="{{ $listing->title }}">
                            <div>
                                <div class="prop-name">{{ $listing->title }}</div>
                                <div style="font-size:0.72rem;color:var(--text-muted);">{{ $listing->address }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="price-cell">₱{{ number_format($listing->price, 0) }}/mo</td>
                    <td>
                        <span class="badge {{ $listing->status === 'available' ? 'badge-active' : 'badge-cancelled' }}">
                            {{ strtoupper($listing->status) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:600;">{{ $activeCount }}</span>
                        <span style="color:var(--text-muted);font-size:0.78rem;"> active</span>
                    </td>
                    <td>
                        <a href="{{ route('property.show', $listing->id) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px;">
                        No listings yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="add-listing-row">
            <a href="{{ route('properties.create') }}" class="add-listing-btn">
                <i class="fas fa-plus-circle"></i> Add New Listing
            </a>
        </div>
    </div>

    {{-- Incoming Requests --}}
    <div class="requests-card">
        <h3>Incoming Requests</h3>

        @forelse($pendingBookings as $booking)
        <div class="request-item pending-l" id="req-{{ $booking->id }}">
            <div class="req-top">
                <div class="req-avatar">
                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                </div>
                <div class="req-info">
                    <div class="req-name">{{ $booking->user->name }}</div>
                    <div class="req-prop">{{ $booking->property->title }}</div>
                    <div class="req-date">
                        Move-in: {{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}
                    </div>
                </div>
                <span class="badge badge-pending">PENDING</span>
            </div>
            <div class="req-actions">
                {{-- Approve form --}}
                <form method="POST" action="{{ route('bookings.approve', $booking->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-approve">Approve</button>
                </form>
                {{-- Reject form --}}
                <form method="POST" action="{{ route('bookings.reject', $booking->id) }}"
                      style="display:inline;"
                      onsubmit="return confirm('Reject this booking from {{ $booking->user->name }}?')">
                    @csrf
                    <button type="submit" class="btn-reject">Reject</button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-requests">
            <i class="fas fa-inbox"></i>
            No pending requests right now.
        </div>
        @endforelse

        {{-- Confirmed bookings preview --}}
        @foreach($confirmedBookings as $booking)
        <div class="request-item confirmed-l">
            <div class="req-top">
                <div class="req-avatar" style="background:var(--green);">
                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                </div>
                <div class="req-info">
                    <div class="req-name">{{ $booking->user->name }}</div>
                    <div class="req-prop">{{ $booking->property->title }}</div>
                </div>
                <span class="badge badge-confirmed">CONFIRMED</span>
            </div>
        </div>
        @endforeach

        <button class="view-all-btn" onclick="window.location='{{ route('bookings') }}'">
            View All Requests
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function updateDate() {
    const now = new Date();
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    document.getElementById('liveDate').textContent = now.toLocaleDateString('en-US', opts);
}
updateDate();
</script>
@endpush