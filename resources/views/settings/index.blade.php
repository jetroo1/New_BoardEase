@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
<style>
    .toggle-checkbox:checked { right: 0; border-color: #2ec4a5; }
    .toggle-checkbox:checked + .toggle-label { background-color: #2ec4a5; }
    .toggle-checkbox { right: 4px; transition: all 0.2s; }
</style>
@endpush

@section('content')
<div class="min-h-screen" id="settingsPage">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color:var(--text)">Settings</h1>
        <p style="color:var(--text-muted)" class="text-sm mt-1">Manage your account preferences</p>
    </div>

    <div class="flex gap-6 flex-wrap lg:flex-nowrap">

        {{-- Sidebar Tabs --}}
        <div class="w-full lg:w-56 flex-shrink-0">
            <div class="rounded-xl border p-2 flex flex-col gap-1" style="background:var(--card);border-color:var(--border)">
                @php
                    $tab = request('tab', session('tab', 'profile'));
                    // Map success keys to tab
                    if(session('success_notifications')) $tab = 'notifications';
                    if(session('success_security')) $tab = 'security';
                    if(session('success_preferences')) $tab = 'preferences';
                    if(session('success_profile')) $tab = 'profile';
                @endphp

                @foreach([
                    ['tab'=>'profile',       'icon'=>'fa-user',        'label'=>'Profile'],
                    ['tab'=>'notifications', 'icon'=>'fa-bell',        'label'=>'Notifications'],
                    ['tab'=>'security',      'icon'=>'fa-lock',        'label'=>'Security'],
                    ['tab'=>'preferences',   'icon'=>'fa-sliders-h',   'label'=>'Preferences'],
                ] as $item)
                <a href="?tab={{ $item['tab'] }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all"
                   style="{{ $tab === $item['tab'] ? 'background:rgba(46,196,165,0.12);color:#2ec4a5;' : 'color:var(--text-muted);' }}">
                    <i class="fas {{ $item['icon'] }} w-4 text-center"></i>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Main Panel --}}
        <div class="flex-1">

            {{-- ── PROFILE TAB ── --}}
            @if($tab === 'profile')
            <div class="rounded-xl border p-6" style="background:var(--card);border-color:var(--border)">
                <h2 class="text-base font-semibold mb-1" style="color:var(--text)">Profile Information</h2>
                <p class="text-sm mb-6" style="color:var(--text-muted)">Update your personal information</p>

                @if(session('success_profile'))
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#16a34a">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success_profile') }}
                </div>
                @endif

                {{-- Avatar --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="relative">
                        <img id="avatarPreview"
                             src="{{ $user->photo }}"
                             alt="avatar"
                             class="w-20 h-20 rounded-full object-cover border-2"
                             style="border-color:var(--teal)">
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <form action="{{ route('settings.photo.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="btn btn-outline cursor-pointer text-sm px-4 py-2 rounded-lg font-semibold flex items-center gap-2" style="border:1.5px solid var(--border);color:var(--text)">
                                <i class="fas fa-upload text-xs"></i> Upload Photo
                                <input type="file" name="photo" class="hidden" accept="image/*" onchange="this.closest('form').submit()">
                            </label>
                        </form>
                        <form action="{{ route('settings.photo.remove') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm px-4 py-2 rounded-lg font-semibold border" style="border-color:#fca5a5;color:#ef4444;background:transparent">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>

                <form action="{{ route('settings.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                   style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                   onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                   style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                   onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                   style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                   onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Role</label>
                            <input type="text" value="{{ ucfirst($user->role ?? 'Tenant') }}" disabled
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm"
                                   style="background:var(--bg);border-color:var(--border);color:var(--text-muted);cursor:not-allowed">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn px-5 py-2.5 rounded-lg text-sm font-semibold text-white flex items-center gap-2" style="background:var(--navy)">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- ── NOTIFICATIONS TAB ── --}}
            @if($tab === 'notifications')
            <div class="rounded-xl border p-6" style="background:var(--card);border-color:var(--border)">
                <h2 class="text-base font-semibold mb-1" style="color:var(--text)">Notification Preferences</h2>
                <p class="text-sm mb-6" style="color:var(--text-muted)">Choose what you want to be notified about</p>

                @if(session('success_notifications'))
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#16a34a">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success_notifications') }}
                </div>
                @endif

                @php
                    $notifPrefs = $user->notification_preferences ?? [];
                    $notifItems = [
                        ['key'=>'booking_confirmations', 'label'=>'Booking Confirmations',  'desc'=>'Get notified when your booking is approved or rejected', 'default'=>true],
                        ['key'=>'move_out_reminders',    'label'=>'Move-out Reminders',      'desc'=>'Receive reminders 30 and 7 days before your contract ends', 'default'=>true],
                        ['key'=>'new_messages',          'label'=>'New Messages',            'desc'=>'Notify when hosts or admin sends you a message', 'default'=>true],
                        ['key'=>'new_listings_nearby',   'label'=>'New Listings Nearby',     'desc'=>'Be alerted when new boarding houses open near your saved location', 'default'=>false],
                        ['key'=>'payment_receipts',      'label'=>'Payment Receipts',        'desc'=>'Get receipts for monthly rent and deposit payments', 'default'=>true],
                        ['key'=>'review_reminders',      'label'=>'Review Reminders',        'desc'=>'Remind me to leave a review after my stay ends', 'default'=>false],
                    ];
                @endphp

                <form action="{{ route('settings.notifications.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col divide-y" style="border-color:var(--border)">
                        @foreach($notifItems as $item)
                        @php $checked = isset($notifPrefs[$item['key']]) ? $notifPrefs[$item['key']] : $item['default']; @endphp
                        <div class="flex items-center justify-between py-4">
                            <div>
                                <p class="text-sm font-medium" style="color:var(--text)">{{ $item['label'] }}</p>
                                <p class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $item['desc'] }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                                <input type="checkbox" name="{{ $item['key'] }}" value="1" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                <div class="w-11 h-6 rounded-full peer transition-all duration-200"
                                     style="background:var(--border)"
                                     x-data
                                     :style="$el.previousElementSibling.checked ? 'background:#2ec4a5' : 'background:var(--border)'">
                                </div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-200 peer-checked:translate-x-5"></div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn px-5 py-2.5 rounded-lg text-sm font-semibold text-white flex items-center gap-2" style="background:var(--navy)">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- ── SECURITY TAB ── --}}
            @if($tab === 'security')
            <div class="flex flex-col gap-5">
                {{-- Change Password --}}
                <div class="rounded-xl border p-6" style="background:var(--card);border-color:var(--border)">
                    <h2 class="text-base font-semibold mb-1" style="color:var(--text)">Change Password</h2>
                    <p class="text-sm mb-6" style="color:var(--text-muted)">Make sure your account stays secure</p>

                    @if(session('success_security'))
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#16a34a">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success_security') }}
                    </div>
                    @endif

                    @if($errors->has('current_password'))
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#fee2e2;color:#dc2626">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first('current_password') }}
                    </div>
                    @endif

                    <form action="{{ route('settings.password.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Current Password</label>
                                <input type="password" name="current_password" placeholder="Enter current password"
                                       class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                       style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                       onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">New Password</label>
                                <input type="password" name="password" placeholder="Enter new password"
                                       class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                       style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                       onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Confirm New Password</label>
                                <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                       class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                       style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                       onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="btn px-5 py-2.5 rounded-lg text-sm font-semibold text-white flex items-center gap-2" style="background:var(--navy)">
                                <i class="fas fa-lock"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="rounded-xl border p-6" style="background:var(--card);border-color:#fca5a5">
                    <h2 class="text-base font-semibold mb-1" style="color:#ef4444">Danger Zone</h2>
                    <p class="text-sm mb-4" style="color:var(--text-muted)">Irreversible account actions</p>
                    <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                            class="px-4 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2"
                            style="background:#fee2e2;color:#ef4444;border:1.5px solid #fca5a5">
                        <i class="fas fa-trash"></i> Delete My Account
                    </button>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5)">
                <div class="rounded-xl border p-6 w-full max-w-md mx-4" style="background:var(--card);border-color:var(--border)">
                    <h3 class="text-base font-semibold mb-2" style="color:#ef4444"><i class="fas fa-exclamation-triangle mr-2"></i>Delete Account</h3>
                    <p class="text-sm mb-4" style="color:var(--text-muted)">This action is <strong>irreversible</strong>. Type <strong>DELETE</strong> to confirm.</p>
                    <form action="{{ route('settings.account.delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="text" name="confirm_delete" placeholder="Type DELETE to confirm"
                               class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none mb-4"
                               style="background:var(--bg);border-color:#fca5a5;color:var(--text)">
                        <div class="flex gap-3 justify-end">
                            <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold" style="background:var(--bg);color:var(--text)">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#ef4444">
                                Delete Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- ── PREFERENCES TAB ── --}}
            @if($tab === 'preferences')
            <div class="rounded-xl border p-6" style="background:var(--card);border-color:var(--border)">
                <h2 class="text-base font-semibold mb-1" style="color:var(--text)">App Preferences</h2>
                <p class="text-sm mb-6" style="color:var(--text-muted)">Customize your BoardEase experience</p>

                @if(session('success_preferences'))
                <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#16a34a">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success_preferences') }}
                </div>
                @endif

                @php $appPrefs = $user->app_preferences ?? []; @endphp

                <form action="{{ route('settings.preferences.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Default Search Location</label>
                            <input type="text" name="default_location" value="{{ old('default_location', $appPrefs['default_location'] ?? '') }}"
                                   placeholder="e.g. Tagum City"
                                   class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                   style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                   onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Preferred Price Range (₱/mo)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" name="price_min" value="{{ old('price_min', $appPrefs['price_min'] ?? 1000) }}"
                                       placeholder="Min"
                                       class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                       style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                       onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                                <input type="number" name="price_max" value="{{ old('price_max', $appPrefs['price_max'] ?? 5000) }}"
                                       placeholder="Max"
                                       class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all"
                                       style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                       onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text)">Preferred Room Type</label>
                            <select name="room_type"
                                    class="w-full px-3 py-2.5 rounded-lg border text-sm outline-none transition-all appearance-none"
                                    style="background:var(--bg);border-color:var(--border);color:var(--text)"
                                    onfocus="this.style.borderColor='#2ec4a5'" onblur="this.style.borderColor='var(--border)'">
                                @foreach(['any'=>'Any', 'single'=>'Single Room', 'shared'=>'Shared Room', 'studio'=>'Studio', 'entire'=>'Entire Unit'] as $val=>$label)
                                <option value="{{ $val }}" {{ ($appPrefs['room_type'] ?? 'any') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Theme Toggle --}}
                        <div class="pt-2 border-t" style="border-color:var(--border)">
                            <label class="block text-sm font-medium mb-3" style="color:var(--text)">Appearance</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="setTheme('light')" id="btnLight"
                                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-lg border text-sm font-medium transition-all"
                                        style="border-color:var(--border);color:var(--text)">
                                    <i class="fas fa-sun"></i> Light
                                </button>
                                <button type="button" onclick="setTheme('dark')" id="btnDark"
                                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-lg border text-sm font-medium transition-all"
                                        style="border-color:var(--border);color:var(--text)">
                                    <i class="fas fa-moon"></i> Dark
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn px-5 py-2.5 rounded-lg text-sm font-semibold text-white flex items-center gap-2" style="background:var(--navy)">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Toggle switches (pure CSS-compatible fallback) ─────────────
document.querySelectorAll('input[type="checkbox"].sr-only').forEach(function(cb) {
    var track = cb.nextElementSibling;
    function update() {
        track.style.background = cb.checked ? '#2ec4a5' : 'var(--border)';
    }
    update();
    cb.addEventListener('change', update);
});

// ── Theme ──────────────────────────────────────────────────────
function setTheme(theme) {
    fetch('{{ route("settings.theme.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ theme })
    }).then(function(r) { return r.json(); }).then(function(data) {
        applyTheme(data.theme);
        localStorage.setItem('boardease_theme', data.theme);
        highlightThemeBtn(data.theme);
    });
}

function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.style.setProperty('--bg',         '#0f1624');
        document.documentElement.style.setProperty('--card',       '#1a2340');
        document.documentElement.style.setProperty('--text',       '#e2e8f0');
        document.documentElement.style.setProperty('--text-muted', '#94a3b8');
        document.documentElement.style.setProperty('--border',     '#2d3a5e');
        document.documentElement.style.setProperty('--navy',       '#243050');
        document.documentElement.style.setProperty('--navy-light', '#2d3a5e');
    } else {
        document.documentElement.style.setProperty('--bg',         '#f0f4f8');
        document.documentElement.style.setProperty('--card',       '#ffffff');
        document.documentElement.style.setProperty('--text',       '#1e293b');
        document.documentElement.style.setProperty('--text-muted', '#64748b');
        document.documentElement.style.setProperty('--border',     '#e2e8f0');
        document.documentElement.style.setProperty('--navy',       '#1a2340');
        document.documentElement.style.setProperty('--navy-light', '#243050');
    }
}

function highlightThemeBtn(theme) {
    var btnLight = document.getElementById('btnLight');
    var btnDark  = document.getElementById('btnDark');
    if (!btnLight || !btnDark) return;
    if (theme === 'dark') {
        btnDark.style.borderColor  = '#2ec4a5';
        btnDark.style.color        = '#2ec4a5';
        btnDark.style.background   = 'rgba(46,196,165,0.1)';
        btnLight.style.borderColor = 'var(--border)';
        btnLight.style.color       = 'var(--text)';
        btnLight.style.background  = 'transparent';
    } else {
        btnLight.style.borderColor = '#2ec4a5';
        btnLight.style.color       = '#2ec4a5';
        btnLight.style.background  = 'rgba(46,196,165,0.1)';
        btnDark.style.borderColor  = 'var(--border)';
        btnDark.style.color        = 'var(--text)';
        btnDark.style.background   = 'transparent';
    }
}

// Apply saved theme on load
(function() {
    var saved = '{{ auth()->user()->theme ?? "light" }}';
    applyTheme(saved);
    highlightThemeBtn(saved);
})();
</script>
@endpush