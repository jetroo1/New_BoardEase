@extends('layouts.app')

@section('title', 'Notifications')
@section('search-placeholder', 'Search notifications...')

@push('styles')
<style>
    .notifications-page {
        max-width: 980px;
        margin: 0 auto;
    }
    .notifications-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
    }
    .notifications-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: 0;
    }
    .notifications-header p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 4px;
    }
    .notifications-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }
    .notifications-tab {
        border: 1px solid var(--border);
        background: rgba(255,255,255,0.72);
        color: var(--text-muted);
        border-radius: 999px;
        padding: 8px 14px;
        font: 800 0.82rem 'DM Sans', sans-serif;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .notifications-tab.active,
    .notifications-tab:hover {
        border-color: rgba(6, 182, 212, 0.38);
        background: rgba(6, 182, 212, 0.14);
        color: #0284c7;
    }
    .notifications-card {
        background: var(--glass-card);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        backdrop-filter: blur(18px);
        overflow: hidden;
    }
    .notifications-row {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
        text-decoration: none;
        color: var(--text);
        transition: background 0.18s ease, transform 0.18s ease;
        cursor: pointer;
    }
    .notifications-row:last-child { border-bottom: none; }
    .notifications-row:hover {
        background: rgba(14, 165, 233, 0.08);
    }
    .notifications-row.unread {
        background: rgba(6, 182, 212, 0.10);
    }
    .notifications-row-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: #0284c7;
        background: rgba(14, 165, 233, 0.13);
    }
    .notifications-row-icon.booking { color: #0ea5e9; background: rgba(14, 165, 233, 0.14); }
    .notifications-row-icon.message { color: #06b6d4; background: rgba(6, 182, 212, 0.14); }
    .notifications-row-icon.property { color: #0891b2; background: rgba(8, 145, 178, 0.14); }
    .notifications-row-icon.review { color: #f59e0b; background: rgba(245, 158, 11, 0.14); }
    .notifications-row-icon.payment { color: #16a34a; background: rgba(34, 197, 94, 0.14); }
    .notifications-row-icon.system { color: #64748b; background: rgba(100, 116, 139, 0.13); }
    .notifications-row-main { min-width: 0; flex: 1; }
    .notifications-row-top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
    }
    .notifications-row-title {
        font-size: 0.95rem;
        font-weight: 800;
    }
    .notifications-row-message {
        color: var(--text-muted);
        font-size: 0.86rem;
        line-height: 1.5;
        margin-top: 4px;
    }
    .notifications-row-time {
        color: #0284c7;
        font-size: 0.76rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .notifications-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--teal);
        margin-top: 5px;
        flex-shrink: 0;
    }
    .notifications-empty {
        padding: 54px 20px;
        text-align: center;
        color: var(--text-muted);
    }
    .notifications-empty i {
        font-size: 2rem;
        color: #38bdf8;
        margin-bottom: 12px;
    }
    @media (max-width: 700px) {
        .notifications-header { flex-direction: column; }
        .notifications-row-top { flex-direction: column; gap: 4px; }
        .notifications-row-time { white-space: normal; }
    }
</style>
@endpush

@section('content')
@php
    $categoryCounts = collect($notifications)->groupBy('category')->map->count();
@endphp

<div class="notifications-page">
    <div class="notifications-header">
        <div>
            <h1>Notifications</h1>
            <p>Messages, bookings, reviews, listing updates, and system announcements in one place.</p>
        </div>
        <button class="btn btn-primary" id="pageMarkAllRead">
            <i class="fas fa-check-double"></i> Mark all as read
        </button>
    </div>

    <div class="notifications-tabs">
        <button class="notifications-tab active" data-filter="all">All <span>({{ count($notifications) }})</span></button>
        <button class="notifications-tab" data-filter="messages">Messages <span>({{ $categoryCounts['messages'] ?? 0 }})</span></button>
        <button class="notifications-tab" data-filter="bookings">Bookings <span>({{ $categoryCounts['bookings'] ?? 0 }})</span></button>
        <button class="notifications-tab" data-filter="properties">Properties <span>({{ $categoryCounts['properties'] ?? 0 }})</span></button>
        <button class="notifications-tab" data-filter="system">System <span>({{ $categoryCounts['system'] ?? 0 }})</span></button>
    </div>

    <div class="notifications-card" id="pageNotificationList">
        @forelse($notifications as $notification)
            <div
                class="notifications-row {{ $notification['unread'] ? 'unread' : '' }}"
                data-id="{{ $notification['id'] }}"
                data-category="{{ $notification['category'] }}"
                data-url="{{ $notification['action_url'] }}"
            >
                <div class="notifications-row-icon {{ $notification['tone'] }}">
                    <i class="{{ $notification['icon'] }}"></i>
                </div>
                <div class="notifications-row-main">
                    <div class="notifications-row-top">
                        <div style="display:flex;gap:10px;align-items:flex-start;">
                            <div>
                                <div class="notifications-row-title">{{ $notification['title'] }}</div>
                                <div class="notifications-row-message">{{ $notification['message'] }}</div>
                            </div>
                            @if($notification['unread'])
                                <span class="notifications-dot"></span>
                            @endif
                        </div>
                        <div class="notifications-row-time">
                            <i class="fas fa-clock" style="font-size:0.68rem;margin-right:4px;"></i>{{ $notification['time_ago'] }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="notifications-empty">
                <i class="fas fa-bell-slash"></i>
                <div style="font-weight:800;color:var(--text);margin-bottom:4px;">No notifications yet</div>
                <div>You will see messages, booking updates, and property alerts here.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.notifications-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.notifications-tab').forEach(item => item.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('.notifications-row').forEach(row => {
            row.style.display = filter === 'all' || row.dataset.category === filter ? 'flex' : 'none';
        });
    });
});

document.querySelectorAll('.notifications-row').forEach(row => {
    row.addEventListener('click', function() {
        if (typeof markNotificationRead === 'function') {
            markNotificationRead(this.dataset.id, this.dataset.url);
        } else {
            window.location.href = this.dataset.url;
        }
    });
});

document.getElementById('pageMarkAllRead')?.addEventListener('click', function() {
    if (typeof markAllNotificationsRead === 'function') {
        markAllNotificationsRead();
    }
    document.querySelectorAll('.notifications-row.unread').forEach(row => row.classList.remove('unread'));
    document.querySelectorAll('.notifications-dot').forEach(dot => dot.remove());
});
</script>
@endpush
