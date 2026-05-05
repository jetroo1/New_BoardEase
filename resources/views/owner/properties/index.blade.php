@extends('layouts.app')

@section('title', 'My Properties')
@section('search-placeholder', 'Search your properties...')

@push('styles')
<style>
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px; }
    .page-header h1 { font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700; }
    .page-header p  { font-size:0.875rem;color:var(--text-muted);margin-top:4px; }

    .props-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px; }

    .prop-card { background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:box-shadow 0.2s,transform 0.2s; }
    .prop-card:hover { box-shadow:0 8px 24px rgba(0,0,0,0.1);transform:translateY(-2px); }
    .prop-img { height:160px;object-fit:cover;width:100%;background:var(--bg); }
    .prop-body { padding:14px 16px; }
    .prop-title { font-size:1rem;font-weight:700;margin-bottom:4px; }
    .prop-address { font-size:0.78rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;margin-bottom:10px; }
    .prop-meta { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px; }
    .prop-price { font-size:1rem;font-weight:700;color:var(--blue-accent); }
    .prop-status { font-size:0.72rem;font-weight:700;padding:3px 9px;border-radius:20px; }
    .status-active   { background:#dcfce7;color:#16a34a; }
    .status-inactive { background:#fee2e2;color:#dc2626; }
    .approved-badge  { font-size:0.72rem;font-weight:700;padding:3px 9px;border-radius:20px; }
    .approved-yes    { background:#dcfce7;color:#16a34a; }
    .approved-no     { background:#fff7ed;color:#ea580c; }
    .prop-actions { display:flex;gap:8px;border-top:1px solid var(--border);padding-top:12px; }
    .btn-edit   { flex:1;padding:7px;background:var(--navy);color:#fff;border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;transition:background 0.2s; }
    .btn-edit:hover { background:var(--navy-light); }
    .btn-delete { flex:1;padding:7px;background:transparent;color:#ef4444;border:1.5px solid #fca5a5;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.2s; }
    .btn-delete:hover { background:#fef2f2;border-color:#ef4444; }
    .empty-state { text-align:center;padding:60px 20px;color:var(--text-muted); }
    .empty-state i { font-size:3rem;opacity:0.2;margin-bottom:12px;display:block; }
    .flash-success { background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px; }
    .flash-error   { background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;display:flex;align-items:center;gap:8px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>My Properties</h1>
        <p>Manage your boarding house listings</p>
    </div>
    <a href="{{ route('owner.properties.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Property
    </a>
</div>

@if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

@if($properties->isEmpty())
    <div class="empty-state">
        <i class="fas fa-home"></i>
        <p style="font-size:1rem;font-weight:600;">No properties yet</p>
        <p style="font-size:0.875rem;margin-top:4px;">Start by adding your first boarding house listing.</p>
        <a href="{{ route('owner.properties.create') }}" class="btn btn-primary" style="margin-top:16px;display:inline-flex;">
            <i class="fas fa-plus"></i> Add Property
        </a>
    </div>
@else
<div class="props-grid">
    @foreach($properties as $property)
    <div class="prop-card">
        <img class="prop-img"
             src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&q=80' }}"
             alt="{{ $property->title }}">
        <div class="prop-body">
            <div class="prop-title">{{ $property->title }}</div>
            <div class="prop-address">
                <i class="fas fa-map-marker-alt" style="color:var(--teal)"></i>
                {{ $property->address }}
            </div>
            <div class="prop-meta">
                <div class="prop-price">₱{{ number_format($property->price, 0) }}<span style="font-size:0.72rem;font-weight:400;color:var(--text-muted)">/mo</span></div>
                <span class="prop-status {{ $property->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    {{ strtoupper($property->status) }}
                </span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span class="approved-badge {{ $property->is_approved ? 'approved-yes' : 'approved-no' }}">
                    {{ $property->is_approved ? '✓ Approved' : '⏳ Pending Approval' }}
                </span>
                <span style="font-size:0.78rem;color:var(--text-muted);">
                    <i class="fas fa-calendar-check"></i> {{ $property->bookings_count }} booking(s)
                </span>
            </div>
            <div class="prop-actions">
                <a href="{{ route('owner.properties.edit', $property->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="{{ route('owner.properties.destroy', $property->id) }}"
                      onsubmit="return confirm('Delete {{ addslashes($property->title) }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-delete" style="width:100%;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection