@extends('layouts.app')

@section('title', 'All Properties')
@section('search-placeholder', 'Search properties...')

@push('styles')
<style>
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px; }
    .page-header h1 { font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700; }

    .props-card { background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden; }
    .table-topbar { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border); }
    .table-topbar h3 { font-size:1rem;font-weight:700; }

    .filter-tabs { display:flex;gap:4px;background:var(--bg);border-radius:8px;padding:3px; }
    .filter-tab { padding:5px 14px;border:none;background:transparent;border-radius:6px;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;color:var(--text-muted);transition:all 0.2s; }
    .filter-tab.active { background:#fff;color:var(--navy);box-shadow:0 1px 4px rgba(0,0,0,0.08); }

    .props-table { width:100%;border-collapse:collapse; }
    .props-table th { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:700;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:var(--bg); }
    .props-table td { padding:12px 16px;border-bottom:1px solid var(--border);font-size:0.875rem;vertical-align:middle; }
    .props-table tr:last-child td { border-bottom:none; }
    .props-table tbody tr:hover { background:var(--bg); }

    .prop-cell { display:flex;align-items:center;gap:10px; }
    .prop-thumb { width:44px;height:44px;border-radius:8px;object-fit:cover;background:var(--bg);flex-shrink:0; }
    .prop-name    { font-weight:600; }
    .prop-address { font-size:0.75rem;color:var(--text-muted); }

    .badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700; }
    .badge-approved  { background:#dcfce7;color:#16a34a; }
    .badge-pending   { background:#fff7ed;color:#ea580c; }
    .badge-active    { background:#eff6ff;color:#3b82f6; }

    .btn-approve { padding:5px 12px;background:#16a34a;color:#fff;border:none;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:background 0.2s; }
    .btn-approve:hover { background:#15803d; }
    .btn-reject  { padding:5px 12px;background:transparent;color:#ea580c;border:1.5px solid #fed7aa;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s; }
    .btn-reject:hover { background:#fff7ed; }
    .btn-del     { padding:5px 10px;background:transparent;color:#ef4444;border:1.5px solid #fca5a5;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;cursor:pointer;transition:all 0.2s; }
    .btn-del:hover { background:#fef2f2; }

    .flash-success { background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px; }
    .pagination-wrap { padding:16px 20px;border-top:1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>All Properties</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-top:4px;">Review and approve property listings</p>
    </div>
</div>

@if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="props-card">
    <div class="table-topbar">
        <h3>Properties <span style="font-size:0.78rem;color:var(--text-muted);font-weight:400;">({{ $properties->total() }} total)</span></h3>
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterProps(this,'all')">All</button>
            <button class="filter-tab" onclick="filterProps(this,'pending')">Pending</button>
            <button class="filter-tab" onclick="filterProps(this,'approved')">Approved</button>
        </div>
    </div>
    <table class="props-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Owner</th>
                <th>Price</th>
                <th>Bookings</th>
                <th>Coordinates</th>
                <th>Approval</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr data-approved="{{ $property->is_approved ? 'approved' : 'pending' }}">
                <td>
                    <div class="prop-cell">
                        <img class="prop-thumb"
                             src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=80&q=80' }}"
                             alt="{{ $property->title }}">
                        <div>
                            <div class="prop-name">{{ $property->title }}</div>
                            <div class="prop-address">{{ $property->address }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight:600;">{{ $property->owner->name }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $property->owner->email }}</div>
                </td>
                <td style="font-weight:700;color:var(--blue-accent);">₱{{ number_format($property->price, 0) }}/mo</td>
                <td>{{ $property->bookings_count }}</td>
                <td style="font-size:0.78rem;color:var(--text-muted);">
                    @if($property->latitude && $property->longitude)
                        <span style="color:var(--green);">✓ Set</span><br>
                        <span style="font-size:0.7rem;">{{ $property->latitude }}, {{ $property->longitude }}</span>
                    @else
                        <span style="color:#ef4444;">✗ Missing</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $property->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $property->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="{{ route('property.show', $property->id) }}" class="btn-approve" style="background:var(--navy);text-decoration:none;">View</a>

                        @if(!$property->is_approved)
                        <form method="POST" action="{{ route('admin.properties.approve', $property->id) }}" style="display:inline;">
                            @csrf
                            <button class="btn-approve">Approve</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.properties.reject', $property->id) }}" style="display:inline;">
                            @csrf
                            <button class="btn-reject">Reject</button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('admin.properties.destroy', $property->id) }}"
                              style="display:inline;"
                              onsubmit="return confirm('Delete {{ addslashes($property->title) }}?')">
                            @csrf @method('DELETE')
                            <button class="btn-del"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $properties->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function filterProps(btn, type) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.props-table tbody tr').forEach(row => {
        row.style.display = (type === 'all' || row.dataset.approved === type) ? '' : 'none';
    });
}
</script>
@endpush