@extends('layouts.app')

@section('title', 'Notifications')
@section('search-placeholder', 'Search notifications...')

@push('styles')
<style>
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-header h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 700; }
    .page-header p { font-size: 0.875rem; color: var(--text-muted); margin-top: 4px; }

    .notif-tabs { display: flex; gap: 4px; background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 4px; margin-bottom: 20px; width: fit-content; }
    .notif-tab { padding: 8px 18px; border: none; background: transparent; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 600; cursor: pointer; color: var(--text-muted); transition: all 0.2s; }
    .notif-tab.active { background: var(--navy); color: #fff; }

    .notif-group-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin: 20px 0 10px; }

    .notif-item { background: var(--card); border-radius: 12px; border: 1px solid var(--border); padding: 14px 16px; display: flex; gap: 12px; align-items: flex-start; margin-bottom: 8px; cursor: pointer; transition: box-shadow 0.2s; position: relative; }
    .notif-item:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .notif-item.unread { border-left: 3px solid var(--teal); background: #f0fdfb; }
    .notif-item.unread::after { content: ''; position: absolute; top: 14px; right: 14px; width: 8px; height: 8px; background: var(--teal); border-radius: 50%; }

    .notif-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .notif-icon.booking { background: #eff6ff; color: var(--blue-accent); }
    .notif-icon.payment { background: #f0fdf4; color: var(--green); }
    .notif-icon.message { background: #f5f3ff; color: var(--purple); }
    .notif-icon.reminder { background: #fff7ed; color: var(--orange); }
    .notif-icon.system { background: #f1f5f9; color: var(--text-muted); }

    .notif-body { flex: 1; }
    .notif-title { font-size: 0.875rem; font-weight: 700; margin-bottom: 3px; }
    .notif-text { font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; }
    .notif-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; }

    .mark-all { font-size: 0.82rem; color: var(--teal); font-weight: 600; cursor: pointer; border: none; background: none; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Notifications</h1>
        <p>Stay updated on your bookings and messages</p>
    </div>
    <button class="mark-all" onclick="markAllRead()"><i class="fas fa-check-double"></i> Mark all as read</button>
</div>

<div class="notif-tabs">
    <button class="notif-tab active" onclick="switchTab(this,'all')">All</button>
    <button class="notif-tab" onclick="switchTab(this,'bookings')">Bookings</button>
    <button class="notif-tab" onclick="switchTab(this,'messages')">Messages</button>
    <button class="notif-tab" onclick="switchTab(this,'system')">System</button>
</div>

<div id="notifList">
    <div class="notif-group-label">Today</div>

    @php
    $notifs = [
        ['type'=>'booking','icon'=>'fas fa-calendar-check','class'=>'booking','title'=>'Booking Confirmed!','text'=>'Your booking for Lola Doth\'s BH – Solo Room has been confirmed. Check-in: March 14, 2026.','time'=>'2 minutes ago','unread'=>true,'cat'=>'bookings'],
        ['type'=>'message','icon'=>'fas fa-comment-dots','class'=>'message','title'=>'New Message from Admin Support','text'=>'Your booking has been confirmed! ✓ Feel free to contact us if you have questions.','time'=>'5 minutes ago','unread'=>true,'cat'=>'messages'],
        ['type'=>'reminder','icon'=>'fas fa-clock','class'=>'reminder','title'=>'Move-out Reminder','text'=>'Your contract at M. Dormitory (Double Deck) ends in 30 days on June 2, 2025. Please plan your move accordingly.','time'=>'1 hour ago','unread'=>false,'cat'=>'bookings'],
        ['type'=>'payment','icon'=>'fas fa-receipt','class'=>'payment','title'=>'Payment Received','text'=>'₱1,900 monthly rent for Lola Doth\'s BH has been processed successfully for April 2026.','time'=>'3 hours ago','unread'=>false,'cat'=>'system'],
    ];
    @endphp

    @foreach($notifs as $n)
    <div class="notif-item {{ $n['unread'] ? 'unread' : '' }}" data-cat="{{ $n['cat'] }}" onclick="this.classList.remove('unread')">
        <div class="notif-icon {{ $n['class'] }}"><i class="{{ $n['icon'] }}"></i></div>
        <div class="notif-body">
            <div class="notif-title">{{ $n['title'] }}</div>
            <div class="notif-text">{{ $n['text'] }}</div>
            <div class="notif-time"><i class="fas fa-clock" style="font-size:0.7rem"></i> {{ $n['time'] }}</div>
        </div>
    </div>
    @endforeach

    <div class="notif-group-label">Yesterday</div>

    @php
    $older = [
        ['icon'=>'fas fa-star','class'=>'system','title'=>'Review Reminder','text'=>'You stayed at Lawas Boarding House. Share your experience to help other renters!','time'=>'Yesterday, 8:00 PM','cat'=>'system'],
        ['icon'=>'fas fa-home','class'=>'booking','title'=>'New Listing Near You','text'=>'A new boarding house "Green Ridge BH" opened 300m from UM Tagum. Starting at ₱2,200/mo.','time'=>'Yesterday, 2:15 PM','cat'=>'bookings'],
        ['icon'=>'fas fa-shield-alt','class'=>'system','title'=>'Account Verified','text'=>'Your BoardEase account has been successfully verified. You can now make reservations.','time'=>'Yesterday, 10:00 AM','cat'=>'system'],
    ];
    @endphp

    @foreach($older as $n)
    <div class="notif-item" data-cat="{{ $n['cat'] }}" onclick="">
        <div class="notif-icon {{ $n['class'] }}"><i class="{{ $n['icon'] }}"></i></div>
        <div class="notif-body">
            <div class="notif-title">{{ $n['title'] }}</div>
            <div class="notif-text">{{ $n['text'] }}</div>
            <div class="notif-time"><i class="fas fa-clock" style="font-size:0.7rem"></i> {{ $n['time'] }}</div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function switchTab(btn, cat) {
    document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.notif-item').forEach(item => {
        if (cat === 'all' || item.dataset.cat === cat) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function markAllRead() {
    document.querySelectorAll('.notif-item.unread').forEach(i => i.classList.remove('unread'));
    // Update the topbar badge
    const badge = document.querySelector('.notif-badge');
    if (badge) badge.style.display = 'none';
}
</script>
@endpush
