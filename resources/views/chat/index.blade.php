@extends('layouts.app')

@section('title', 'Messages & Calls')
@section('search-placeholder', 'Search conversations...')

@push('styles')
<style>
    .page-content { padding: 0 !important; height: calc(100vh - 64px); display: flex; overflow: hidden; }
    .chat-layout { display: flex; width: 100%; height: 100%; overflow: hidden; }

    /* ── Conversations Panel ── */
    .conversations-panel { width: 300px; flex-shrink: 0; border-right: 1px solid var(--border); display: flex; flex-direction: column; background: var(--card); }
    .conv-header { padding: 18px 16px 14px; border-bottom: 1px solid var(--border); }
    .conv-header h3 { font-size: 1rem; font-weight: 700; margin-bottom: 10px; }
    .conv-search { position: relative; }
    .conv-search input { width: 100%; padding: 8px 12px 8px 34px; border: 1.5px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.82rem; outline: none; background: var(--bg); color: var(--text); transition: border-color 0.2s; }
    .conv-search input:focus { border-color: var(--teal); }
    .conv-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.78rem; }
    .conv-list { flex: 1; overflow-y: auto; padding: 8px; }
    .conv-item { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 10px; cursor: pointer; transition: background 0.15s; position: relative; }
    .conv-item:hover { background: var(--bg); }
    .conv-item.active { background: color-mix(in srgb, var(--blue-accent) 10%, var(--card)); }
    .conv-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: #fff; }
    .online-dot { position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: var(--green); border-radius: 50%; border: 2px solid var(--card); }
    .conv-info { flex: 1; overflow: hidden; }
    .conv-name { font-size: 0.875rem; font-weight: 700; margin-bottom: 2px; }
    .conv-preview { font-size: 0.78rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .conv-meta { text-align: right; flex-shrink: 0; }
    .conv-time { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 4px; }
    .unread-badge { background: var(--teal); color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.68rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-left: auto; }

    /* ── Chat Window ── */
    .chat-window { flex: 1; display: flex; flex-direction: column; background: var(--bg); overflow: hidden; }
    .chat-topbar { background: var(--card); border-bottom: 1px solid var(--border); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }
    .chat-peer { display: flex; align-items: center; gap: 12px; }
    .chat-peer-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; }
    .chat-peer-name { font-size: 0.95rem; font-weight: 700; }
    .chat-peer-status { font-size: 0.75rem; color: var(--green); display: flex; align-items: center; gap: 4px; }
    .chat-actions { display: flex; gap: 8px; }
    .call-btn { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .call-btn.voice { background: color-mix(in srgb, var(--green) 12%, var(--card)); color: var(--green); }
    .call-btn.voice:hover { background: var(--green); color: #fff; }
    .call-btn.video { background: color-mix(in srgb, var(--blue-accent) 12%, var(--card)); color: var(--blue-accent); }
    .call-btn.video:hover { background: var(--blue-accent); color: #fff; }
    .call-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    /* ── Messages ── */
    .messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px; }
    .msg-day-divider { display: flex; align-items: center; gap: 12px; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
    .msg-day-divider::before, .msg-day-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .msg-group { display: flex; gap: 10px; max-width: 70%; }
    .msg-group.sent { margin-left: auto; flex-direction: row-reverse; }
    .msg-avatar { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; align-self: flex-end; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #fff; }
    .msg-bubbles { display: flex; flex-direction: column; gap: 3px; }
    .msg-bubble { padding: 10px 14px; border-radius: 14px; font-size: 0.875rem; line-height: 1.5; max-width: 100%; word-wrap: break-word; }
    .msg-group:not(.sent) .msg-bubble { background: var(--card); color: var(--text); border-bottom-left-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .msg-group.sent .msg-bubble { background: var(--navy); color: #fff; border-bottom-right-radius: 4px; }
    .msg-time { font-size: 0.68rem; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 4px; }
    .msg-group.sent .msg-time { justify-content: flex-end; }
    .msg-read { color: var(--teal); }

    /* ── Typing ── */
    .typing-indicator { display: flex; gap: 10px; max-width: 70%; align-items: flex-end; }
    .typing-dots { background: var(--card); padding: 12px 16px; border-radius: 14px; border-bottom-left-radius: 4px; display: flex; gap: 4px; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .typing-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--text-muted); animation: typingBounce 1.2s infinite ease-in-out; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

    /* ── Input ── */
    .chat-input-area { background: var(--card); border-top: 1px solid var(--border); padding: 14px 20px; }
    .input-toolbar { display: flex; gap: 6px; margin-bottom: 10px; }
    .toolbar-btn { width: 32px; height: 32px; border: none; border-radius: 7px; background: var(--bg); color: var(--text-muted); cursor: pointer; font-size: 0.85rem; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .toolbar-btn:hover { background: var(--border); color: var(--text); }
    .input-row { display: flex; gap: 10px; align-items: flex-end; }
    .msg-input-wrap { flex: 1; position: relative; }
    .msg-input { width: 100%; min-height: 44px; max-height: 120px; padding: 11px 44px 11px 14px; border: 1.5px solid var(--border); border-radius: 12px; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; outline: none; resize: none; background: var(--bg); color: var(--text); transition: border-color 0.2s; line-height: 1.4; }
    .msg-input:focus { border-color: var(--teal); background: var(--card); }
    .emoji-btn { position: absolute; right: 12px; bottom: 10px; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--text-muted); transition: transform 0.2s; }
    .emoji-btn:hover { transform: scale(1.2); }
    .send-btn { width: 44px; height: 44px; background: var(--navy); color: #fff; border: none; border-radius: 12px; cursor: pointer; font-size: 0.95rem; transition: all 0.2s; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .send-btn:hover { background: var(--teal); }
    .send-btn:active { transform: scale(0.95); }

    /* ── Empty state ── */
    .chat-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); gap: 12px; }
    .chat-empty i { font-size: 3rem; opacity: 0.3; }

    /* ── New Conversation Modal ── */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
    .modal-overlay.active { display: flex; }
    .modal-card { background: var(--card); border-radius: 16px; padding: 24px; width: 380px; max-height: 80vh; display: flex; flex-direction: column; gap: 16px; }
    .modal-card h3 { font-size: 1rem; font-weight: 700; }
    .user-list { overflow-y: auto; display: flex; flex-direction: column; gap: 4px; }
    .user-item { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 10px; cursor: pointer; transition: background 0.15s; }
    .user-item:hover { background: var(--bg); }

    /* ── Call Modal ── */
    .call-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .call-modal.active { display: flex; }

    /* Video streams */
    .call-video-wrap { position: relative; width: 100%; max-width: 780px; }
    .video-remote { width: 100%; max-height: 460px; background: #0a0f1e; border-radius: 20px; object-fit: cover; display: block; }
    .video-local  { position: absolute; bottom: 16px; right: 16px; width: 140px; height: 100px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3); background: #111827; }
    .video-local.hidden  { display: none; }

    /* Voice-only card (shown when no video) */
    .call-card { background: var(--navy); border-radius: 24px; padding: 40px; text-align: center; width: 320px; animation: callSlideIn 0.3s ease; }
    @keyframes callSlideIn { from{transform:translateY(20px) scale(0.95);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }
    .call-avatar { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #fff; position: relative; }
    .call-avatar::before { content:''; position:absolute; inset:-8px; border:2px solid rgba(46,196,165,0.4); border-radius:50%; animation:callRing 1.5s infinite; }
    .call-avatar::after  { content:''; position:absolute; inset:-16px; border:2px solid rgba(46,196,165,0.2); border-radius:50%; animation:callRing 1.5s infinite 0.5s; }
    @keyframes callRing { 0%{transform:scale(1);opacity:1} 100%{transform:scale(1.3);opacity:0} }
    .call-type   { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    .call-name   { font-family: 'Syne', sans-serif; font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 6px; }
    .call-status { font-size: 0.82rem; color: rgba(255,255,255,0.5); margin-bottom: 32px; }
    .call-timer  { font-size: 0.9rem; color: var(--teal); font-variant-numeric: tabular-nums; margin-top: 20px; font-weight: 700; }

    /* Shared controls bar */
    .call-controls { display: flex; justify-content: center; gap: 20px; margin-top: 24px; }
    .call-ctrl { width: 54px; height: 54px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; cursor: pointer; transition: all 0.2s; }
    .ctrl-mute    { background: rgba(255,255,255,0.1); color: #fff; }
    .ctrl-mute:hover { background: rgba(255,255,255,0.2); }
    .ctrl-cam     { background: rgba(255,255,255,0.1); color: #fff; }
    .ctrl-cam:hover { background: rgba(255,255,255,0.2); }
    .ctrl-end     { background: var(--red); color: #fff; }
    .ctrl-end:hover { background: #dc2626; }
    .ctrl-speaker { background: rgba(255,255,255,0.1); color: #fff; }

    /* Incoming call overlay */
    .incoming-modal { display: none; position: fixed; bottom: 24px; right: 24px; background: var(--navy); border-radius: 18px; padding: 20px 24px; z-index: 1100; box-shadow: 0 8px 32px rgba(0,0,0,0.4); animation: slideUp 0.3s ease; min-width: 280px; }
    .incoming-modal.active { display: block; }
    @keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
    .incoming-title { font-size: 0.72rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .incoming-name  { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 16px; }
    .incoming-btns  { display: flex; gap: 10px; }
    .btn-accept { flex: 1; padding: 10px; border: none; border-radius: 10px; background: var(--green); color: #fff; font-family: 'DM Sans',sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.2s; }
    .btn-accept:hover { background: #16a34a; }
    .btn-decline { flex: 1; padding: 10px; border: none; border-radius: 10px; background: var(--red); color: #fff; font-family: 'DM Sans',sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.2s; }
    .btn-decline:hover { background: #dc2626; }

    .realtime-badge { display: inline-flex; align-items: center; gap: 5px; background: color-mix(in srgb, var(--green) 12%, var(--card)); color: var(--green); font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid color-mix(in srgb, var(--green) 30%, var(--card)); }
    .rt-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: rtPulse 1.5s infinite; }
    @keyframes rtPulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

    /* ── Modal input dark mode fix ── */
    .modal-card input { background: var(--bg); color: var(--text); border: 1.5px solid var(--border); }
    .modal-card input:focus { border-color: var(--teal); outline: none; }
</style>
@endpush

@section('content')
<div class="chat-layout">

    {{-- ── Conversations List ── --}}
    <div class="conversations-panel">
        <div class="conv-header">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <h3>Messages</h3>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="realtime-badge"><span class="rt-dot"></span> Live</span>
                    <button onclick="openNewConvModal()" title="New conversation" style="background:var(--teal);color:#fff;border:none;border-radius:7px;width:28px;height:28px;cursor:pointer;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="conv-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search conversations..." id="convSearch" oninput="searchConvs(this.value)">
            </div>
        </div>

        <div class="conv-list" id="convList">
            @forelse($conversations as $conv)
            <div class="conv-item"
                 id="conv-item-{{ $conv['id'] }}"
                 onclick="openConversation({{ $conv['id'] }}, '{{ addslashes($conv['other']->name) }}', '{{ $conv['initials'] }}', '{{ $conv['color'] }}')"
                 data-name="{{ strtolower($conv['other']->name) }}">
                <div class="conv-avatar" style="background:{{ $conv['color'] }}">
                    {{ $conv['initials'] }}
                </div>
                <div class="conv-info">
                    <div class="conv-name">{{ $conv['other']->name }}</div>
                    <div class="conv-preview" id="conv-preview-{{ $conv['id'] }}">{{ $conv['preview'] }}</div>
                </div>
                <div class="conv-meta">
                    <div class="conv-time" id="conv-time-{{ $conv['id'] }}">{{ $conv['time'] }}</div>
                    @if($conv['unread'] > 0)
                    <div class="unread-badge" id="conv-unread-{{ $conv['id'] }}">{{ $conv['unread'] }}</div>
                    @else
                    <div class="unread-badge" id="conv-unread-{{ $conv['id'] }}" style="display:none">0</div>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:24px 16px;text-align:center;color:var(--text-muted);font-size:0.85rem;">
                No conversations yet.<br>Click <strong>+</strong> to start one.
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Chat Window ── --}}
    <div class="chat-window" id="chatWindow">
        <div class="chat-empty" id="chatEmpty">
            <i class="fas fa-comments"></i>
            <div style="font-size:0.9rem;font-weight:600;">Select a conversation</div>
            <div style="font-size:0.8rem;">or start a new one with the + button</div>
        </div>

        <div id="chatActive" style="display:none;flex-direction:column;flex:1;overflow:hidden;width:100%;">
            {{-- Topbar --}}
            <div class="chat-topbar">
                <div class="chat-peer">
                    <div class="chat-peer-avatar" id="chatPeerAvatar" style="background:#2ec4a5">A</div>
                    <div>
                        <div class="chat-peer-name" id="chatPeerName">—</div>
                        <div class="chat-peer-status">
                            <span style="width:6px;height:6px;background:currentColor;border-radius:50%;display:inline-block;"></span>
                            Online
                        </div>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="call-btn voice" id="voiceCallBtn" onclick="startCall('voice')">
                        <i class="fas fa-phone"></i> Voice Call
                    </button>
                    <button class="call-btn video" id="videoCallBtn" onclick="startCall('video')">
                        <i class="fas fa-video"></i> Video Call
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="messages-area" id="messagesArea">
                <div class="typing-indicator" id="typingIndicator" style="display:none;">
                    <div class="msg-avatar" id="typingAvatar" style="background:#2ec4a5">?</div>
                    <div class="typing-dots">
                        <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="chat-input-area">
                <div class="input-toolbar">
                    <button class="toolbar-btn" title="Attach file"><i class="fas fa-paperclip"></i></button>
                    <button class="toolbar-btn" title="Share property"><i class="fas fa-home"></i></button>
                    <button class="toolbar-btn" title="Send image"><i class="fas fa-image"></i></button>
                    <button class="toolbar-btn" title="Location"><i class="fas fa-map-marker-alt"></i></button>
                </div>
                <div class="input-row">
                    <div class="msg-input-wrap">
                        <textarea class="msg-input" id="msgInput" placeholder="Type a message..." rows="1"
                            onkeydown="handleEnter(event)"
                            oninput="autoResize(this); notifyTyping()"></textarea>
                        <button class="emoji-btn">😊</button>
                    </div>
                    <button class="send-btn" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── New Conversation Modal ── --}}
<div class="modal-overlay" id="newConvModal">
    <div class="modal-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <h3>New Conversation</h3>
            <button onclick="closeNewConvModal()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--text-muted);"><i class="fas fa-times"></i></button>
        </div>
        <input type="text" id="userSearch" placeholder="Search users..." oninput="filterUsers(this.value)"
            style="padding:9px 14px;border:1.5px solid var(--border);border-radius:9px;font-family:'DM Sans',sans-serif;font-size:0.85rem;outline:none;width:100%;background:var(--bg);color:var(--text);">
        <div class="user-list" id="userList">
            @foreach($allUsers as $u)
            <div class="user-item" data-name="{{ strtolower($u['name']) }}" onclick="startConversation({{ $u['id'] }})">
                <div class="conv-avatar" style="background:{{ $u['color'] }};width:36px;height:36px;font-size:0.8rem;">{{ $u['initials'] }}</div>
                <div>
                    <div style="font-size:0.875rem;font-weight:600;">{{ $u['name'] }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ ucfirst($u['role']) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Active Call Modal ── --}}
<div class="call-modal" id="callModal">
    {{-- Voice card (shown for voice calls or while video is loading) --}}
    <div class="call-card" id="callCard">
        <div class="call-type"   id="callType">Voice Call</div>
        <div class="call-avatar" id="callAvatar" style="background:#2ec4a5">A</div>
        <div class="call-name"   id="callName">—</div>
        <div class="call-status" id="callStatus">Calling...</div>
        <div class="call-timer"  id="callTimer" style="display:none">00:00</div>
        <div class="call-controls">
            <button class="call-ctrl ctrl-mute"    id="muteBtn"    onclick="toggleMute()">   <i class="fas fa-microphone"  id="muteIcon"></i></button>
            <button class="call-ctrl ctrl-end"                      onclick="endCall()">      <i class="fas fa-phone-slash"></i></button>
            <button class="call-ctrl ctrl-speaker" id="speakerBtn" onclick="toggleSpeaker()"><i class="fas fa-volume-up"   id="speakerIcon"></i></button>
        </div>
    </div>

    {{-- Video wrap (shown for video calls once connected) --}}
    <div id="videoWrap" style="display:none;flex-direction:column;align-items:center;gap:16px;">
        <div class="call-video-wrap">
            <video id="remoteVideo" class="video-remote" autoplay playsinline></video>
            <video id="localVideo"  class="video-local"  autoplay playsinline muted></video>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="color:#fff;font-size:0.85rem;font-weight:600;" id="videoCallName">—</span>
            <span style="color:var(--teal);font-size:0.85rem;font-weight:700;" id="videoCallTimer">00:00</span>
        </div>
        <div class="call-controls">
            <button class="call-ctrl ctrl-mute" id="muteBtn2"    onclick="toggleMute()">   <i class="fas fa-microphone"  id="muteIcon2"></i></button>
            <button class="call-ctrl ctrl-cam"  id="camBtn"      onclick="toggleCamera()"> <i class="fas fa-video"       id="camIcon"></i></button>
            <button class="call-ctrl ctrl-end"                    onclick="endCall()">      <i class="fas fa-phone-slash"></i></button>
        </div>
    </div>
</div>

{{-- ── Incoming Call Toast ── --}}
<div class="incoming-modal" id="incomingModal">
    <div class="incoming-title" id="incomingType">Incoming Voice Call</div>
    <div class="incoming-name"  id="incomingName">Someone</div>
    <div class="incoming-btns">
        <button class="btn-accept"  onclick="acceptCall()"><i class="fas fa-phone"></i> Accept</button>
        <button class="btn-decline" onclick="declineCall()"><i class="fas fa-phone-slash"></i> Decline</button>
    </div>
</div>

<audio id="ringtone" loop>
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAA..." type="audio/wav">
</audio>
<audio id="remoteAudio" autoplay></audio>
@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const ME_ID    = {{ auth()->id() }};
const ME_INIT  = '{{ $userInitials }}';
const ME_COLOR = '{{ $userColor }}';

// ── State ─────────────────────────────────────────────────────
let currentConvId    = null;
let currentPeerInit  = 'A';
let currentPeerColor = '#2ec4a5';
let typingTimeout    = null;
let typingHideTimer  = null;

// ── WebRTC State ───────────────────────────────────────────────
let peerConnection   = null;
let localStream      = null;
let callType         = null;
let callInterval     = null;
let callSeconds      = 0;
let isMuted          = false;
let isCamOff         = false;
let isCaller         = false;
let incomingOffer    = null;
let incomingCallType = null;
let incomingConvId   = null;

const ICE_SERVERS = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'turn:openrelay.metered.ca:80',  username: 'openrelayproject', credential: 'openrelayproject' },
        { urls: 'turn:openrelay.metered.ca:443', username: 'openrelayproject', credential: 'openrelayproject' },
        { urls: 'turn:openrelay.metered.ca:443?transport=tcp', username: 'openrelayproject', credential: 'openrelayproject' },
    ]
};

// ── Init Echo ─────────────────────────────────────────────────
function initEcho() {
    if (typeof window.Echo === 'undefined') { setTimeout(initEcho, 500); return; }
    try {
        window.Echo.private(`chat.${ME_ID}`)
            .listen('.message.sent', (e) => {
                if (e.conversation_id === currentConvId) {
                    appendMessage({ content: e.content, time: e.time, initials: e.initials, color: e.color, sent: false });
                    hideTypingIndicator();
                }
                updateSidebarPreview(e.conversation_id, e.content);
            })
            .listen('.user.typing', (e) => {
                if (currentConvId) showTypingIndicator(e.name);
            })
            .listen('.webrtc.signal', (e) => {
                handleSignal(e);
            });
    } catch (err) {
        console.warn('Echo listener error:', err.message);
    }
}

// ── Send WebRTC signal ────────────────────────────────────────
async function sendSignal(type, payload) {
    if (!currentConvId) return;
    try {
        await fetch(`/chat/${currentConvId}/signal`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ type, payload }),
        });
    } catch (err) {
        console.warn('Signal send error:', err.message);
    }
}

// ── Handle incoming WebRTC signals ───────────────────────────
async function handleSignal(e) {
    const { type, payload, sender_id, sender_name } = e;

    if (type === 'offer') {
        incomingOffer    = payload;
        incomingCallType = payload.callType;
        incomingConvId   = e.conversation_id ?? currentConvId;
        showIncomingCall(sender_name, payload.callType);
        return;
    }

    if (type === 'call-rejected') {
        updateCallStatus('Call declined');
        setTimeout(() => endCall(), 1500);
        return;
    }

    if (type === 'call-ended') {
        cleanupCall();
        return;
    }

    if (type === 'answer' && peerConnection) {
        try {
            if (peerConnection.signalingState !== 'have-local-offer') return;
            await peerConnection.setRemoteDescription(new RTCSessionDescription({
                type: payload.type,
                sdp:  payload.sdp,
            }));
        } catch (err) {
            console.error('setRemoteDescription (answer) failed:', err);
        }
        return;
    }

    if (type === 'ice-candidate' && peerConnection) {
        try {
            const candidate = payload.candidate ?? payload;
            if (candidate && candidate.candidate !== undefined) {
                await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
            }
        } catch (err) {
            console.warn('ICE candidate error:', err);
        }
        return;
    }
}

// ── Create PeerConnection ─────────────────────────────────────
function createPeerConnection() {
    const pc = new RTCPeerConnection(ICE_SERVERS);

    pc.onicecandidate = (e) => {
        if (e.candidate) {
            sendSignal('ice-candidate', { candidate: e.candidate.toJSON() });
        }
    };

    pc.ontrack = (e) => {
        if (callType === 'video') {
            document.getElementById('remoteVideo').srcObject = e.streams[0];
        } else {
            document.getElementById('remoteAudio').srcObject = e.streams[0];
        }
        onCallConnected();
    };

    pc.onconnectionstatechange = () => {
        if (['disconnected','failed','closed'].includes(pc.connectionState)) {
            cleanupCall();
        }
    };

    return pc;
}

// ── Start Call (caller side) ──────────────────────────────────
async function startCall(type) {
    if (!currentConvId) return;
    callType = type;
    isCaller = true;

    showCallModal(type, document.getElementById('chatPeerName').textContent,
                        document.getElementById('chatPeerAvatar').textContent,
                        document.getElementById('chatPeerAvatar').style.background);
    updateCallStatus('Calling...');
    disableCallButtons(true);

    try {
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: type === 'video' });
    } catch (err) {
        updateCallStatus('Microphone/Camera access denied');
        setTimeout(() => endCall(), 2000);
        return;
    }

    if (type === 'video') {
        document.getElementById('localVideo').srcObject = localStream;
    }

    peerConnection = createPeerConnection();
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    await sendSignal('offer', { sdp: offer.sdp, callType: type, type: offer.type });
}

// ── Accept incoming call ──────────────────────────────────────
async function acceptCall() {
    hideIncomingCall();

    if (incomingConvId) currentConvId = incomingConvId;

    callType = incomingCallType;
    isCaller = false;

    showCallModal(callType,
        document.getElementById('incomingName').textContent,
        document.getElementById('incomingName').textContent.charAt(0).toUpperCase(),
        currentPeerColor
    );
    updateCallStatus('Connecting...');
    disableCallButtons(true);

    try {
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: callType === 'video' });
    } catch (err) {
        updateCallStatus('Microphone/Camera access denied');
        setTimeout(() => endCall(), 2000);
        return;
    }

    if (callType === 'video') {
        document.getElementById('localVideo').srcObject = localStream;
    }

    peerConnection = createPeerConnection();
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    try {
        await peerConnection.setRemoteDescription(new RTCSessionDescription({
            type: incomingOffer.type,
            sdp:  incomingOffer.sdp,
        }));
    } catch (err) {
        console.error('setRemoteDescription failed:', err);
        updateCallStatus('Connection failed');
        setTimeout(() => endCall(), 2000);
        return;
    }

    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    await sendSignal('answer', { sdp: answer.sdp, type: answer.type });
}

// ── Decline incoming call ─────────────────────────────────────
async function declineCall() {
    hideIncomingCall();
    await sendSignal('call-rejected', { declined: true });
}

// ── Call connected ────────────────────────────────────────────
function onCallConnected() {
    updateCallStatus('Connected');
    document.getElementById('callTimer').style.display = 'block';

    if (callType === 'video') {
        document.getElementById('callCard').style.display  = 'none';
        document.getElementById('videoWrap').style.display = 'flex';
        document.getElementById('videoCallName').textContent = document.getElementById('callName').textContent;
    }

    callSeconds = 0;
    clearInterval(callInterval);
    callInterval = setInterval(() => {
        callSeconds++;
        const t = formatTime(callSeconds);
        document.getElementById('callTimer').textContent      = t;
        document.getElementById('videoCallTimer').textContent = t;
    }, 1000);
}

// ── End Call ──────────────────────────────────────────────────
async function endCall() {
    await sendSignal('call-ended', { ended: true });
    cleanupCall();
}

function cleanupCall() {
    clearInterval(callInterval);
    callSeconds = 0;

    if (localStream) { localStream.getTracks().forEach(t => t.stop()); localStream = null; }
    if (peerConnection) { peerConnection.close(); peerConnection = null; }

    const rv = document.getElementById('remoteVideo');
    const lv = document.getElementById('localVideo');
    const ra = document.getElementById('remoteAudio');
    if (rv) rv.srcObject = null;
    if (lv) lv.srcObject = null;
    if (ra) ra.srcObject = null;

    document.getElementById('callModal').classList.remove('active');
    document.getElementById('callCard').style.display  = 'block';
    document.getElementById('videoWrap').style.display = 'none';
    document.getElementById('callTimer').style.display = 'none';

    isMuted  = false;
    isCamOff = false;
    disableCallButtons(false);
    resetMuteIcons();
}

// ── Toggle Mute ───────────────────────────────────────────────
function toggleMute() {
    isMuted = !isMuted;
    if (localStream) { localStream.getAudioTracks().forEach(t => t.enabled = !isMuted); }
    const cls = isMuted ? 'fas fa-microphone-slash' : 'fas fa-microphone';
    const bg  = isMuted ? 'rgba(239,68,68,0.3)' : 'rgba(255,255,255,0.1)';
    ['muteIcon','muteIcon2'].forEach(id => { const el = document.getElementById(id); if (el) el.className = cls; });
    ['muteBtn','muteBtn2'].forEach(id => { const el = document.getElementById(id); if (el) el.style.background = bg; });
}

// ── Toggle Camera ─────────────────────────────────────────────
function toggleCamera() {
    isCamOff = !isCamOff;
    if (localStream) { localStream.getVideoTracks().forEach(t => t.enabled = !isCamOff); }
    const icon = document.getElementById('camIcon');
    const btn  = document.getElementById('camBtn');
    if (icon) icon.className = isCamOff ? 'fas fa-video-slash' : 'fas fa-video';
    if (btn)  btn.style.background = isCamOff ? 'rgba(239,68,68,0.3)' : 'rgba(255,255,255,0.1)';
}

function toggleSpeaker() {}

// ── Show/Hide Call Modal ──────────────────────────────────────
function showCallModal(type, name, initials, color) {
    document.getElementById('callType').textContent        = type === 'video' ? '📹 Video Call' : '📞 Voice Call';
    document.getElementById('callName').textContent        = name;
    document.getElementById('callAvatar').textContent      = initials;
    document.getElementById('callAvatar').style.background = color;
    document.getElementById('callModal').classList.add('active');
}

function updateCallStatus(text) {
    document.getElementById('callStatus').textContent = text;
}

// ── Incoming Call Toast ───────────────────────────────────────
function showIncomingCall(name, type) {
    document.getElementById('incomingType').textContent = type === 'video' ? '📹 Incoming Video Call' : '📞 Incoming Voice Call';
    document.getElementById('incomingName').textContent = name;
    document.getElementById('incomingModal').classList.add('active');
}

function hideIncomingCall() {
    document.getElementById('incomingModal').classList.remove('active');
}

// ── Helpers ───────────────────────────────────────────────────
function formatTime(s) {
    const m = Math.floor(s / 60).toString().padStart(2, '0');
    const sec = (s % 60).toString().padStart(2, '0');
    return `${m}:${sec}`;
}

function disableCallButtons(disabled) {
    document.getElementById('voiceCallBtn').disabled = disabled;
    document.getElementById('videoCallBtn').disabled = disabled;
}

function resetMuteIcons() {
    ['muteIcon','muteIcon2'].forEach(id => { const el = document.getElementById(id); if (el) el.className = 'fas fa-microphone'; });
    ['muteBtn','muteBtn2'].forEach(id => { const el = document.getElementById(id); if (el) el.style.background = 'rgba(255,255,255,0.1)'; });
}

// ── Open Conversation ─────────────────────────────────────────
async function openConversation(id, name, initials, color) {
    currentConvId    = id;
    currentPeerInit  = initials;
    currentPeerColor = color;

    document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
    const item = document.getElementById(`conv-item-${id}`);
    if (item) item.classList.add('active');

    document.getElementById('chatEmpty').style.display = 'none';
    const active = document.getElementById('chatActive');
    active.style.display  = 'flex';
    active.style.flex     = '1';
    active.style.overflow = 'hidden';
    active.style.width    = '100%';

    document.getElementById('chatPeerAvatar').textContent      = initials;
    document.getElementById('chatPeerAvatar').style.background = color;
    document.getElementById('chatPeerName').textContent        = name;
    document.getElementById('typingAvatar').textContent        = initials;
    document.getElementById('typingAvatar').style.background   = color;

    const badge = document.getElementById(`conv-unread-${id}`);
    if (badge) badge.style.display = 'none';

    await loadMessages(id);
}

// ── Load Messages ─────────────────────────────────────────────
async function loadMessages(convId) {
    const area   = document.getElementById('messagesArea');
    const typing = document.getElementById('typingIndicator');
    area.innerHTML = '';
    area.appendChild(typing);

    try {
        const res = await fetch(`/chat/${convId}/messages`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (!data.messages || data.messages.length === 0) {
            const div = document.createElement('div');
            div.className   = 'msg-day-divider';
            div.textContent = 'Start of conversation';
            area.insertBefore(div, typing);
        } else {
            data.messages.forEach(m => appendMessage({ ...m }));
        }
    } catch (err) {
        console.warn('Load messages error:', err.message);
        const div = document.createElement('div');
        div.className   = 'msg-day-divider';
        div.textContent = 'Start of conversation';
        area.insertBefore(div, typing);
    }

    area.scrollTop = area.scrollHeight;
}

// ── Send Message ──────────────────────────────────────────────
async function sendMessage() {
    if (!currentConvId) return;
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();
    if (!text) return;

    input.value        = '';
    input.style.height = 'auto';

    const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    appendMessage({ content: text, time, sent: true });
    updateSidebarPreview(currentConvId, text);

    try {
        await fetch(`/chat/${currentConvId}/messages`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ content: text }),
        });
    } catch (err) {
        console.warn('Send error:', err.message);
    }
}

// ── Append Message ────────────────────────────────────────────
function appendMessage({ content, time, initials, color, sent }) {
    const area   = document.getElementById('messagesArea');
    const typing = document.getElementById('typingIndicator');
    const div    = document.createElement('div');
    div.className = `msg-group ${sent ? 'sent' : ''}`;

    const aColor = sent ? ME_COLOR : (color    || currentPeerColor);
    const aInit  = sent ? ME_INIT  : (initials || currentPeerInit);

    div.innerHTML = `
        <div class="msg-avatar" style="background:${aColor}">${escHtml(aInit)}</div>
        <div class="msg-bubbles">
            <div class="msg-bubble">${escHtml(content)}</div>
            <div class="msg-time">${escHtml(time)}${sent ? ' <i class="fas fa-check-double msg-read"></i>' : ''}</div>
        </div>`;

    area.insertBefore(div, typing);
    area.scrollTop = area.scrollHeight;
}

// ── Typing ────────────────────────────────────────────────────
function notifyTyping() {
    if (!currentConvId) return;
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        fetch(`/chat/${currentConvId}/typing`, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).catch(() => {});
    }, 400);
}

function showTypingIndicator() {
    document.getElementById('typingIndicator').style.display = 'flex';
    document.getElementById('messagesArea').scrollTop        = 99999;
    clearTimeout(typingHideTimer);
    typingHideTimer = setTimeout(hideTypingIndicator, 3000);
}

function hideTypingIndicator() {
    document.getElementById('typingIndicator').style.display = 'none';
}

// ── Sidebar Update ────────────────────────────────────────────
function updateSidebarPreview(convId, text) {
    const el = document.getElementById(`conv-preview-${convId}`);
    const te = document.getElementById(`conv-time-${convId}`);
    if (el) el.textContent = text.length > 38 ? text.slice(0, 38) + '…' : text;
    if (te) te.textContent = 'Just now';
}

// ── New Conversation Modal ────────────────────────────────────
function openNewConvModal()  { document.getElementById('newConvModal').classList.add('active'); }
function closeNewConvModal() { document.getElementById('newConvModal').classList.remove('active'); }

document.getElementById('newConvModal').addEventListener('click', function(e) {
    if (e.target === this) closeNewConvModal();
});

function filterUsers(q) {
    document.querySelectorAll('#userList .user-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? 'flex' : 'none';
    });
}

async function startConversation(userId) {
    closeNewConvModal();
    try {
        const res  = await fetch('/chat/start', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ user_id: userId }),
        });
        const data = await res.json();
        if (data.conversation_id) {
            window.location.href = `/chat?open=${data.conversation_id}`;
        }
    } catch (err) {
        console.warn('Start conversation error:', err.message);
    }
}

// ── Misc Helpers ──────────────────────────────────────────────
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function searchConvs(q) {
    document.querySelectorAll('.conv-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? 'flex' : 'none';
    });
}

function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// ── Auto-open on load ─────────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    initEcho();

    const openId = parseInt(new URLSearchParams(window.location.search).get('open'));
    if (openId) {
        const el = document.getElementById(`conv-item-${openId}`);
        if (el) {
            el.click();
        } else {
            const first = document.querySelector('.conv-item');
            if (first) first.click();
        }
    } else {
        @if($conversations->isNotEmpty())
        openConversation(
            {{ $conversations->first()['id'] }},
            '{{ addslashes($conversations->first()['other']->name) }}',
            '{{ $conversations->first()['initials'] }}',
            '{{ $conversations->first()['color'] }}'
        );
        @endif
    }
});
</script>
@endpush