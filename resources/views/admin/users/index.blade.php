@extends('layouts.app')

@section('title', 'All Users')
@section('search-placeholder', 'Search users...')

@push('styles')
<style>
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px; }
    .page-header h1 { font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700; }

    .users-card { background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden; }
    .table-topbar { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border); }
    .table-topbar h3 { font-size:1rem;font-weight:700; }

    .users-table { width:100%;border-collapse:collapse; }
    .users-table th { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:700;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:var(--bg); }
    .users-table td { padding:12px 16px;border-bottom:1px solid var(--border);font-size:0.875rem;vertical-align:middle; }
    .users-table tr:last-child td { border-bottom:none; }
    .users-table tbody tr:hover { background:var(--bg); }

    .user-cell { display:flex;align-items:center;gap:10px; }
    .user-avatar { width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0; }
    .user-name  { font-weight:600; }
    .user-email { font-size:0.75rem;color:var(--text-muted); }

    .role-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;text-transform:uppercase; }
    .role-tenant { background:#eff6ff;color:#3b82f6; }
    .role-owner  { background:#fff7ed;color:#ea580c; }
    .role-admin  { background:#f5f3ff;color:#8b5cf6; }

    .role-select { padding:4px 8px;border:1.5px solid var(--border);border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;background:var(--bg);color:var(--text);cursor:pointer; }
    .btn-update { padding:5px 12px;background:var(--navy);color:#fff;border:none;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:background 0.2s; }
    .btn-update:hover { background:var(--navy-light); }
    .btn-del { padding:5px 10px;background:transparent;color:#ef4444;border:1.5px solid #fca5a5;border-radius:7px;font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s; }
    .btn-del:hover { background:#fef2f2; }

    .flash-success { background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:8px; }
    .flash-error   { background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;display:flex;align-items:center;gap:8px; }

    .pagination-wrap { padding:16px 20px;border-top:1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>All Users</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-top:4px;">Manage user accounts and roles</p>
    </div>
</div>

@if(session('success'))
    <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="users-card">
    <div class="table-topbar">
        <h3>Users <span style="font-size:0.78rem;color:var(--text-muted);font-weight:400;">({{ $users->total() }} total)</span></h3>
    </div>
    <table class="users-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Properties</th>
                <th>Bookings</th>
                <th>Joined</th>
                <th>Change Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>
                    <img class="user-avatar"
     src="{{ $u->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=2ec4a5&color=fff' }}"
     alt="{{ $u->name }}"
     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=2ec4a5&color=fff'">
                        <div>
                            <div class="user-name">{{ $u->name }}</div>
                            <div class="user-email">{{ $u->email }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="role-badge role-{{ $u->role }}">{{ $u->role }}</span></td>
                <td>{{ $u->properties_count }}</td>
                <td>{{ $u->bookings_count }}</td>
                <td style="color:var(--text-muted);font-size:0.8rem;">{{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}</td>
                <td>
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.role', $u) }}" style="display:flex;gap:6px;align-items:center;">
                        @csrf @method('PATCH')
                        <select name="role" class="role-select">
                            @foreach(['tenant','owner','admin'] as $r)
                            <option value="{{ $r }}" {{ $u->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-update">Save</button>
                    </form>
                    @else
                    <span style="font-size:0.78rem;color:var(--text-muted);">You</span>
                    @endif
                </td>
                <td>
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                          onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination-wrap">
        {{ $users->links() }}
    </div>
</div>
@endsection