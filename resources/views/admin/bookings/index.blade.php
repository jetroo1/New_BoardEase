@extends('layouts.app')

@section('title', 'All Bookings')
@section('search-placeholder', 'Search bookings...')

@push('styles')
<style>
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px; }
    .page-header h1 { font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700; }

    .bookings-card { background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden; }
    .table-topbar  { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border); }
    .table-topbar h3 { font-size:1rem;font-weight:700; }

    .filter-tabs { display:flex;gap:4px;background:var(--bg);border-radius:8px;padding:3px; }
    .filter-tab { padding:5px 14px;border:none;background:transparent;border-radius:6px;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;color:var(--text-muted);transition:all 0.2s; }
    .filter-tab.active { background:#fff;color:var(--navy);box-shadow:0 1px 4px rgba(0,0,0,0.08); }

    .bookings-table { width:100%;border-collapse:collapse; }
    .bookings-table th { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:700;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:var(--bg); }
    .bookings-table td { padding:12px 16px;border-bottom:1px solid var(--border);font-size:0.875rem;vertical-align:middle; }
    .bookings-table tr:last-child td { border-bottom:none; }
    .bookings-table tbody tr:hover { background:var(--bg); }

    .badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;text-transform:uppercase; }
    .badge-pending   { background:#fff7ed;color:#ea580c; }
    .badge-confirmed { background:#dcfce7;color:#16a34a; }
    .badge-completed { background:#eff6ff;color:#3b82f6; }
    .badge-cancelled { background:#fee2e2;color:#dc2626; }

    .action-btn { padding:5px 12px;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;border:none;transition:all 0.2s; }
    .btn-approve  { background:var(--navy);color:#fff; }
    .btn-approve:hover { background:var(--navy-light); }
    .btn-reject   { background:transparent;color:#ef4444;border:1.5px solid #fca5a5; }
    .btn-reject:hover { background:#fef2f2; }
    .btn-complete { background:var(--blue-accent);color:#fff; }
    .btn-complete:hover { background:#2563eb; }

    .flash-success { background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px; }
    .pagination-wrap { padding:16px 20px;border-top:1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>All Bookings</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-top:4px;">Overview of all reservation activity</p>
    </div>
</div>

@if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="bookings-card">
    <div class="table-topbar">
        <h3>Bookings <span style="font-size:0.78rem;color:var(--text-muted);font-weight:400;">({{ $bookings->total() }} total)</span></h3>
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterBookings(this,'all')">All</button>
            <button class="filter-tab" onclick="filterBookings(this,'pending')">Pending</button>
            <button class="filter-tab" onclick="filterBookings(this,'confirmed')">Confirmed</button>
            <button class="filter-tab" onclick="filterBookings(this,'completed')">Completed</button>
            <button class="filter-tab" onclick="filterBookings(this,'cancelled')">Cancelled</button>
        </div>
    </div>
    <table class="bookings-table">
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Property</th>
                <th>Owner</th>
                <th>Move-in</th>
                <th>Move-out</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr data-status="{{ $booking->status }}">
                <td>
                    <div style="font-weight:600;">{{ $booking->user->name }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $booking->user->email }}</div>
                </td>
                <td>
                    <div style="font-weight:600;">{{ $booking->property->title }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $booking->property->address }}</div>
                </td>
                <td style="font-size:0.82rem;color:var(--text-muted);">{{ $booking->property->owner->name }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</td>
                <td style="font-weight:700;color:var(--blue-accent);">
                    {{ $booking->total_price ? '₱'.number_format($booking->total_price, 0) : '—' }}
                </td>
                <td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('admin.bookings.approve', $booking->id) }}" style="display:inline;">
                                @csrf <button class="action-btn btn-approve">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.bookings.reject', $booking->id) }}"
                                  style="display:inline;" onsubmit="return confirm('Reject this booking?')">
                                @csrf <button class="action-btn btn-reject">Reject</button>
                            </form>
                        @elseif($booking->status === 'confirmed')
                            <form method="POST" action="{{ route('admin.bookings.complete', $booking->id) }}" style="display:inline;">
                                @csrf <button class="action-btn btn-complete">Complete</button>
                            </form>
                        @else
                            <span style="font-size:0.78rem;color:var(--text-muted);">—</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $bookings->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function filterBookings(btn, status) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.bookings-table tbody tr').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
}
</script>
@endpush