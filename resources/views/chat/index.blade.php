@extends('layouts.app')

@section('title', 'Messages')
@section('search-placeholder', 'Search conversations...')

@push('styles')
<style>
    .page-content { padding: 0 !important; height: calc(100vh - 64px); display: flex; overflow: hidden; }
    .chat-layout  { display: flex; width: 100%; height: 100%; overflow: hidden; }

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
    .conv-item:hover  { background: var(--bg); }
    .conv-item.active { background: color-mix(in srgb, var(--blue-accent) 10%, var(--card)); }
    .conv-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: #fff; overflow: hidden; }    .online-dot  { position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: var(--green); border-radius: 50%; border: 2px solid var(--card); }
    .offline-dot { position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: var(--text-muted); border-radius: 50%; border: 2px solid var(--card); }
    .conv-info    { flex: 1; overflow: hidden; }
    .conv-name    { font-size: 0.875rem; font-weight: 700; margin-bottom: 2px; }
    .conv-preview { font-size: 0.78rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .conv-meta    { text-align: right; flex-shrink: 0; }
    .conv-time    { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 4px; }
    .unread-badge { background: var(--teal); color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.68rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-left: auto; }

    /* ── Chat Window ── */
    .chat-window  { flex: 1; display: flex; flex-direction: column; background: var(--bg); overflow: hidden; }
    .chat-topbar  { background: var(--card); border-bottom: 1px solid var(--border); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }
    .chat-peer    { display: flex; align-items: center; gap: 12px; }
    .chat-peer-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; position: relative; overflow: hidden; }    .chat-peer-name   { font-size: 0.95rem; font-weight: 700; }
    .chat-peer-status { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-top: 1px; }
    .chat-peer-status.is-online { color: var(--green); }
    .status-dot   { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }

    /* ── Load More ── */
    .load-more-wrap { text-align: center; padding: 10px 0 4px; }
    .load-more-btn  { background: var(--bg); border: 1.5px solid var(--border); color: var(--text-muted); font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 600; padding: 6px 16px; border-radius: 20px; cursor: pointer; transition: all 0.2s; }
    .load-more-btn:hover { border-color: var(--teal); color: var(--teal); }

    /* ── Messages ── */
    .messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
    .msg-day-divider { display: flex; align-items: center; gap: 12px; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
    .msg-day-divider::before, .msg-day-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .msg-group      { display: flex; gap: 10px; max-width: 70%; }
    .msg-group.sent { margin-left: auto; flex-direction: row-reverse; }
    .msg-avatar { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; align-self: flex-end; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #fff; overflow: hidden; }    .msg-bubbles    { display: flex; flex-direction: column; gap: 3px; }
    .msg-bubble     { padding: 10px 14px; border-radius: 14px; font-size: 0.875rem; line-height: 1.5; max-width: 100%; word-wrap: break-word; }
    .msg-group:not(.sent) .msg-bubble { background: var(--card); color: var(--text); border-bottom-left-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .msg-group.sent .msg-bubble       { background: var(--navy); color: #fff; border-bottom-right-radius: 4px; }

    /* Image messages */
    .msg-image { max-width: 220px; border-radius: 10px; display: block; cursor: pointer; transition: opacity 0.2s; }
    .msg-image:hover { opacity: 0.88; }
    .msg-group.sent  .msg-image { border-bottom-right-radius: 4px; }
    .msg-group:not(.sent) .msg-image { border-bottom-left-radius: 4px; }

    /* Timestamp + seen receipt */
    .msg-time   { font-size: 0.68rem; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 4px; }
    .msg-group.sent .msg-time { justify-content: flex-end; }
    /* Grey ✓✓ = delivered, Teal ✓✓ = seen */
    .msg-tick         { font-size: 0.72rem; color: var(--text-muted); }
    .msg-tick.seen    { color: var(--teal); }

    /* ── Typing ── */
    .typing-indicator { display: flex; gap: 10px; max-width: 70%; align-items: flex-end; }
    .typing-dots { background: var(--card); padding: 12px 16px; border-radius: 14px; border-bottom-left-radius: 4px; display: flex; gap: 4px; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .typing-dot  { width: 7px; height: 7px; border-radius: 50%; background: var(--text-muted); animation: typingBounce 1.2s infinite ease-in-out; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

    /* ── Input area ── */
    .chat-input-area { background: var(--card); border-top: 1px solid var(--border); padding: 14px 20px; }
    .input-toolbar { display: flex; gap: 6px; margin-bottom: 10px; }
    .toolbar-btn   { width: 32px; height: 32px; border: none; border-radius: 7px; background: var(--bg); color: var(--text-muted); cursor: pointer; font-size: 0.85rem; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .toolbar-btn:hover { background: var(--border); color: var(--text); }
    .input-row     { display: flex; gap: 10px; align-items: flex-end; }
    .msg-input-wrap { flex: 1; position: relative; }
    .msg-input  { width: 100%; min-height: 44px; max-height: 120px; padding: 11px 44px 11px 14px; border: 1.5px solid var(--border); border-radius: 12px; font-family: 'DM Sans', sans-serif; font-size: 0.875rem; outline: none; resize: none; background: var(--bg); color: var(--text); transition: border-color 0.2s; line-height: 1.4; }
    .msg-input:focus { border-color: var(--teal); background: var(--card); }
    .emoji-btn  { position: absolute; right: 12px; bottom: 10px; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--text-muted); transition: transform 0.2s; }
    .emoji-btn:hover { transform: scale(1.2); }
    .send-btn   { width: 44px; height: 44px; background: var(--navy); color: #fff; border: none; border-radius: 12px; cursor: pointer; font-size: 0.95rem; transition: all 0.2s; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .send-btn:hover  { background: var(--teal); }
    .send-btn:active { transform: scale(0.95); }

    /* Image preview strip (shown after picking image) */
    .img-preview-strip { display: none; align-items: center; gap: 8px; margin-bottom: 8px; padding: 8px 10px; background: var(--bg); border-radius: 8px; border: 1.5px dashed var(--border); }
    .img-preview-strip.show { display: flex; }
    .img-preview-strip img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
    .img-preview-strip span { font-size: 0.78rem; color: var(--text-muted); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .img-preview-strip button { background: none; border: none; color: var(--red); cursor: pointer; font-size: 1rem; }

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

    /* ── Image Lightbox ── */
    .lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; cursor: zoom-out; }
    .lightbox.active { display: flex; }
    .lightbox img { max-width: 90vw; max-height: 90vh; border-radius: 8px; object-fit: contain; }

    .realtime-badge { display: inline-flex; align-items: center; gap: 5px; background: color-mix(in srgb, var(--green) 12%, var(--card)); color: var(--green); font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid color-mix(in srgb, var(--green) 30%, var(--card)); }
    .rt-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: rtPulse 1.5s infinite; }
    @keyframes rtPulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

    .chat-actions { display: flex; align-items: center; gap: 8px; }
    .call-action-btn { width: 36px; height: 36px; border: 1.5px solid var(--border); border-radius: 10px; background: var(--card); color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .call-action-btn:hover:not(:disabled) { border-color: var(--teal); color: var(--teal); background: color-mix(in srgb, var(--teal) 8%, var(--card)); }
    .call-action-btn:disabled { opacity: 0.45; cursor: not-allowed; }

    .call-overlay { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(10,15,28,0.86); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 24px; }
    .call-overlay.active { display: flex; }
    .call-shell { width: min(960px, 96vw); max-height: 92vh; background: var(--card); border: 1px solid color-mix(in srgb, var(--border) 70%, transparent); border-radius: 14px; overflow: hidden; box-shadow: 0 24px 70px rgba(0,0,0,0.36); display: flex; flex-direction: column; }
    .call-stage { min-height: 500px; background: #0b1120; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .call-stage video { background: #0b1120; }
    #remoteVideo { width: 100%; height: 100%; min-height: 500px; object-fit: cover; }
    #localVideo { position: absolute; right: 18px; bottom: 18px; width: 180px; aspect-ratio: 16/10; object-fit: cover; border: 2px solid rgba(255,255,255,0.86); border-radius: 10px; background: #111827; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    .call-placeholder { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; gap: 10px; text-align: center; padding: 24px; }
    .call-placeholder-icon { width: 74px; height: 74px; border-radius: 50%; background: var(--teal); display: flex; align-items: center; justify-content: center; font-size: 1.7rem; font-weight: 800; }
    .call-placeholder-name { font-size: 1.05rem; font-weight: 700; }
    .call-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 18px; background: var(--card); border-top: 1px solid var(--border); }
    .call-peer-title { font-size: 0.95rem; font-weight: 800; }
    .call-status { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
    .call-controls { display: flex; align-items: center; gap: 8px; }
    .call-control-btn { width: 40px; height: 40px; border: none; border-radius: 10px; background: var(--bg); color: var(--text); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.18s; }
    .call-control-btn:hover:not(:disabled) { background: var(--border); }
    .call-control-btn.off { background: color-mix(in srgb, var(--red) 12%, var(--bg)); color: var(--red); }
    .call-control-btn.end { background: var(--red); color: #fff; }
    .call-control-btn.accept { background: var(--green); color: #fff; }
    .call-control-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .incoming-call-card { display: none; padding: 30px 24px 26px; text-align: center; gap: 16px; flex-direction: column; align-items: center; }
    .call-overlay.incoming .call-stage,
    .call-overlay.incoming .call-bar { display: none; }
    .call-overlay.incoming .incoming-call-card { display: flex; }

    @media (max-width: 720px) {
        .call-overlay { padding: 10px; }
        .call-shell { width: 100%; max-height: 96vh; }
        .call-stage { min-height: 65vh; }
        #remoteVideo { min-height: 65vh; }
        #localVideo { width: 120px; right: 12px; bottom: 12px; }
        .call-bar { align-items: flex-start; flex-direction: column; }
        .call-controls { width: 100%; justify-content: center; }
    }

    .modal-card input { background: var(--bg); color: var(--text); border: 1.5px solid var(--border); }
    .modal-card input:focus { border-color: var(--teal); outline: none; }

    /* Hidden file input */
    #imageFileInput { display: none; }
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
    .conv-item { border:1px solid transparent;border-radius:8px; }
    .conv-item.active {
        border-color:color-mix(in srgb, var(--teal) 34%, transparent);
        background:color-mix(in srgb, var(--teal) 9%, var(--card));
    }
    .chat-topbar { min-height:68px; }
    .msg-group.sent .msg-bubble { background:linear-gradient(135deg, var(--navy), var(--navy-light)); }
    .msg-group:not(.sent) .msg-bubble { border:1px solid color-mix(in srgb, var(--border) 74%, transparent); }
    .chat-input-area { box-shadow:0 -12px 30px rgba(15,23,42,0.04); }
    @media (max-width: 920px) {
        .conversations-panel { width:260px; }
        .msg-group { max-width:82%; }
    }
    @media (max-width: 720px) {
        .page-content { height:auto;min-height:calc(100vh - 64px); }
        .chat-layout { flex-direction:column;min-height:calc(100vh - 64px); }
        .conversations-panel { width:100%;height:36vh;border-right:0;border-bottom:1px solid var(--border); }
        .chat-window { min-height:58vh; }
        .messages-area { padding:14px; }
        .msg-group { max-width:92%; }
    }
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
                    <button onclick="openNewConvModal()" title="New conversation"
                        style="background:var(--teal);color:#fff;border:none;border-radius:7px;width:28px;height:28px;cursor:pointer;font-size:0.9rem;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="conv-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search Contacts" id="convSearch" oninput="searchConvs(this.value)">
            </div>
        </div>

        <div class="conv-list" id="convList">
            @forelse($conversations as $conv)
            <div class="conv-item"
                 id="conv-item-{{ $conv['id'] }}"
                 onclick="openConversation({{ $conv['id'] }}, '{{ addslashes($conv['other']->name) }}', '{{ $conv['initials'] }}', '{{ $conv['color'] }}', {{ $conv['is_online'] ? 'true' : 'false' }}, '{{ $conv['last_seen'] }}', '{{ $conv['avatar_url'] ?? '' }}')"
                 data-name="{{ strtolower($conv['other']->name) }}"
                 data-peer-id="{{ $conv['other']->id }}">
                <div style="position:relative;flex-shrink:0;">
    <div class="conv-avatar" style="background:{{ $conv['color'] }};overflow:hidden;border-radius:50%;">
        @if($conv['avatar_url'])
            <img src="{{ $conv['avatar_url'] }}" alt="{{ $conv['initials'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
        @else
            {{ $conv['initials'] }}
        @endif
    </div>
    @if($conv['is_online'])
        <span class="online-dot"></span>
    @else
        <span class="offline-dot"></span>
    @endif
                </div>
                <div class="conv-info">
                    <div class="conv-name">{{ $conv['other']->name }}</div>
                    <div class="conv-preview" id="conv-preview-{{ $conv['id'] }}">{{ $conv['preview'] }}</div>
                </div>
                <div class="conv-meta">
                    <div class="conv-time" id="conv-time-{{ $conv['id'] }}">{{ $conv['time'] }}</div>
                    <div class="unread-badge" id="conv-unread-{{ $conv['id'] }}"
                        style="{{ $conv['unread'] > 0 ? '' : 'display:none' }}">{{ $conv['unread'] ?: '' }}</div>
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

        {{-- Empty State --}}
        <div class="chat-empty" id="chatEmpty">
            <i class="fas fa-comments"></i>
            <div style="font-size:0.9rem;font-weight:600;">Select a conversation</div>
            <div style="font-size:0.8rem;">or start a new one with the + button</div>
        </div>

        {{-- Active Chat --}}
        <div id="chatActive" style="display:none;flex-direction:column;flex:1;overflow:hidden;width:100%;">

            {{-- Chat Topbar --}}
            <div class="chat-topbar">
                <div class="chat-peer">
                    <div class="chat-peer-avatar" id="chatPeerAvatar" style="background:#2ec4a5; position:relative;">
    <span id="chatPeerDot" class="offline-dot"></span>
</div>
                    <div>
                        <div class="chat-peer-name" id="chatPeerName">—</div>
                        <div class="chat-peer-status" id="chatPeerStatus">
                            <span class="status-dot"></span>
                            <span id="chatPeerStatusText">Offline</span>
                        </div>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="call-action-btn" id="startVideoCallBtn" onclick="startVideoCall()" title="Start video call" disabled>
                        <i class="fas fa-video"></i>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="messages-area" id="messagesArea">
                {{-- Load More button (shown when older pages exist) --}}
                <div class="load-more-wrap" id="loadMoreWrap" style="display:none;">
                    <button class="load-more-btn" onclick="loadOlderMessages()">
                        <i class="fas fa-arrow-up" style="margin-right:5px;font-size:0.7rem;"></i>Load earlier messages
                    </button>
                </div>

                {{-- Typing indicator --}}
                <div class="typing-indicator" id="typingIndicator" style="display:none;">
                    <div class="msg-avatar" id="typingAvatar" style="background:#2ec4a5"></div>
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="chat-input-area">
                {{-- Image preview strip --}}
                <div class="img-preview-strip" id="imgPreviewStrip">
                    <img id="imgPreviewThumb" src="" alt="preview">
                    <span id="imgPreviewName">image.jpg</span>
                    <button onclick="clearImageSelection()" title="Remove image"><i class="fas fa-times"></i></button>
                </div>

                <div class="input-toolbar">
                    {{-- Image upload button --}}
                    <button class="toolbar-btn" title="Send image" onclick="document.getElementById('imageFileInput').click()">
                        <i class="fas fa-image"></i>
                    </button>
                    <button class="toolbar-btn" title="Attach file"><i class="fas fa-paperclip"></i></button>
                    <button class="toolbar-btn" title="Share property"><i class="fas fa-home"></i></button>
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

{{-- Hidden file input --}}
<input type="file" id="imageFileInput" accept="image/*" onchange="onImageSelected(this)">

{{-- ── New Conversation Modal ── --}}
<div class="modal-overlay" id="newConvModal">
    <div class="modal-card">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <h3>New Conversation</h3>
            <button onclick="closeNewConvModal()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--text-muted);">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <input type="text" id="userSearch" placeholder="Search users..." oninput="filterUsers(this.value)"
            style="padding:9px 14px;border:1.5px solid var(--border);border-radius:9px;font-family:'DM Sans',sans-serif;font-size:0.85rem;outline:none;width:100%;">
        <div class="user-list" id="userList">
            @foreach($allUsers as $u)
            <div class="user-item" data-name="{{ strtolower($u['name']) }}" onclick="startConversation({{ $u['id'] }})">
                <div class="conv-avatar" style="background:{{ $u['color'] }};width:36px;height:36px;font-size:0.8rem;overflow:hidden;border-radius:50%;">
    @if(!empty($u['avatar_url']))
        <img src="{{ $u['avatar_url'] }}" alt="{{ $u['initials'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
    @else
        {{ $u['initials'] }}
    @endif
</div>
                <div>
                    <div style="font-size:0.875rem;font-weight:600;">{{ $u['name'] }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ ucfirst($u['role']) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Image Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="full image">
</div>

{{-- ── WebRTC Call Overlay ── --}}
<div class="call-overlay" id="callOverlay">
    <div class="call-shell">
        <div class="incoming-call-card" id="incomingCallCard">
            <div class="call-placeholder-icon" id="incomingCallInitial">?</div>
            <div>
                <div class="call-peer-title" id="incomingCallName">Incoming call</div>
                <div class="call-status" id="incomingCallStatus">Video call</div>
            </div>
            <div class="call-controls">
                <button class="call-control-btn accept" onclick="acceptIncomingCall()" title="Accept call">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="call-control-btn end" onclick="rejectIncomingCall()" title="Decline call">
                    <i class="fas fa-phone-slash"></i>
                </button>
            </div>
        </div>

        <div class="call-stage">
            <video id="remoteVideo" autoplay playsinline></video>
            <div class="call-placeholder" id="callPlaceholder">
                <div class="call-placeholder-icon" id="callPlaceholderInitial">?</div>
                <div class="call-placeholder-name" id="callPlaceholderName">Connecting</div>
                <div class="call-status" id="callPlaceholderStatus">Waiting for video</div>
            </div>
            <video id="localVideo" autoplay playsinline muted></video>
        </div>

        <div class="call-bar">
            <div>
                <div class="call-peer-title" id="callPeerTitle">Video call</div>
                <div class="call-status" id="callStatus">Connecting</div>
            </div>
            <div class="call-controls">
                <button class="call-control-btn" id="toggleMicBtn" onclick="toggleMic()" title="Toggle microphone">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="call-control-btn" id="toggleCameraBtn" onclick="toggleCamera()" title="Toggle camera">
                    <i class="fas fa-video"></i>
                </button>
                <button class="call-control-btn end" onclick="endCall(true)" title="End call">
                    <i class="fas fa-phone-slash"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const ME_ID    = {{ auth()->id() }};
const ME_INIT  = '{{ $userInitials }}';
const ME_COLOR = '{{ $userColor }}';
const ME_AVATAR = @json(auth()->user()->avatar_url ?? '');
const WEBRTC_ICE_SERVERS = @json(config('services.webrtc.ice_servers'));

// ── State ──────────────────────────────────────────────────────
let currentPeerAvatar = null;    
let currentConvId     = null;
let currentPeerId     = null;
let currentPeerInit   = 'A';
let currentPeerColor  = '#2ec4a5';
let typingTimeout     = null;
let typingHideTimer   = null;
let currentPage       = 1;
let lastPage          = 1;
let selectedImageFile = null;
let heartbeatInterval = null;
let peerConnection    = null;
let localStream       = null;
let remoteStream      = null;
let activeCallConvId  = null;
let activeCallPeerId  = null;
let activeCallPeerName = '';
let activeCallPeerInitial = '?';
let pendingIncomingOffer = null;
let pendingIceCandidates = [];
let callEnding       = false;

function setAvatarEl(el, url, initials, color) {
    if (!el) return;
    el.style.background = color;
    if (url) {
        el.innerHTML = `<img src="${escHtml(url)}" alt="${escHtml(initials)}"
                             style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">`;
    } else {
        el.innerHTML = escHtml(initials);
    }
}

// ── Init Echo ──────────────────────────────────────────────────
function initEcho() {
    if (typeof window.Echo === 'undefined') { setTimeout(initEcho, 500); return; }
    try {
        window.Echo.private(`chat.${ME_ID}`)

            // New message received
            .listen('.message.sent', (e) => {
                if (e.conversation_id === currentConvId) {
                    appendMessage({
                        content:   e.content,
                        image_url: e.image_url ?? null,
                        avatar_url: e.avatar_url ?? null,
                        time:      e.time,
                        initials:  e.initials,
                        color:     e.color,
                        sent:      false,
                        read:      false,
                    });
                    hideTypingIndicator();

                    // Mark as seen immediately since chat is open
                    markConversationSeen(currentConvId);
                }
                updateSidebarPreview(e.conversation_id, e.content || '📷 Image');
                bumpUnreadBadge(e.conversation_id);
            })

            // Someone is typing
            .listen('.user.typing', (e) => {
                if (currentConvId) showTypingIndicator();
            })

            // Sender sees their message was read (teal ticks)
            .listen('.message.read', (e) => {
                if (e.conversation_id === currentConvId) {
                    markAllSentAsSeen();
                }
            })

            // Online/offline status update
            .listen('.user.status', (e) => {
                updatePeerOnlineStatus(e.user_id, e.is_online, e.last_seen_text);
            })

            // WebRTC offer/answer/ICE messages
            .listen('.webrtc.signal', (e) => {
                handleWebRTCSignal(e);
            });

    } catch (err) {
        console.warn('Echo error:', err.message);
    }
}

// ── Heartbeat (keeps online status alive) ─────────────────────
function startHeartbeat() {
    stopHeartbeat();
    // Ping immediately, then every 60 seconds
    pingHeartbeat();
    heartbeatInterval = setInterval(pingHeartbeat, 60000);
}

function stopHeartbeat() {
    if (heartbeatInterval) clearInterval(heartbeatInterval);
}

function pingHeartbeat() {
    fetch('/chat/heartbeat', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    }).catch(() => {});
}

// ── Update peer online status in topbar ───────────────────────
function updatePeerOnlineStatus(userId, isOnline, lastSeenText) {
    if (userId !== currentPeerId) return;

    const dot        = document.getElementById('chatPeerDot');
    const statusEl   = document.getElementById('chatPeerStatus');
    const statusText = document.getElementById('chatPeerStatusText');

    if (dot) {
        dot.className = isOnline ? 'online-dot' : 'offline-dot';
    }
    if (statusEl) {
        statusEl.className = 'chat-peer-status' + (isOnline ? ' is-online' : '');
    }
    if (statusText) {
        statusText.textContent = lastSeenText;
    }

    // Also update the conv list dot
    const convItem = document.querySelector(`[data-peer-id="${userId}"]`);
    if (convItem) {
        const convDot = convItem.querySelector('.online-dot, .offline-dot');
        if (convDot) convDot.className = isOnline ? 'online-dot' : 'offline-dot';
    }
}

// ── Mark all sent messages in view as seen (teal ticks) ───────
function markAllSentAsSeen() {
    document.querySelectorAll('.msg-tick').forEach(el => {
        el.classList.add('seen');
        el.title = 'Seen';
    });
}

// ── Mark conversation as seen (tell server + update badge) ────
function markConversationSeen(convId) {
    fetch(`/chat/${convId}/seen`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    }).catch(() => {});

    // Hide unread badge in sidebar
    const badge = document.getElementById(`conv-unread-${convId}`);
    if (badge) badge.style.display = 'none';
}

// ── Bump unread badge for non-active conversations ─────────────
function bumpUnreadBadge(convId) {
    if (convId === currentConvId) return; // already open
    const badge = document.getElementById(`conv-unread-${convId}`);
    if (badge) {
        const current = parseInt(badge.textContent) || 0;
        badge.textContent = current + 1;
        badge.style.display = 'flex';
    }
}

// ── Open Conversation ──────────────────────────────────────────
async function openConversation(id, name, initials, color, isOnline, lastSeenText, peerAvatarUrl = null) {
    currentConvId    = id;
    currentPeerInit  = initials;
    currentPeerColor = color;
    currentPeerAvatar = peerAvatarUrl;

    // Find the peer's user id from the conv item data attribute
    const convItem = document.getElementById(`conv-item-${id}`);
    currentPeerId  = convItem ? parseInt(convItem.dataset.peerId) : null;

    const callBtn = document.getElementById('startVideoCallBtn');
    if (callBtn) callBtn.disabled = !currentPeerId;

    document.querySelectorAll('.conv-item').forEach(i => i.classList.remove('active'));
    if (convItem) convItem.classList.add('active');

    document.getElementById('chatEmpty').style.display = 'none';
    const active = document.getElementById('chatActive');
    active.style.display  = 'flex';
    active.style.flex     = '1';
    active.style.overflow = 'hidden';
    active.style.width    = '100%';

    // Set topbar peer info
    const peerAvatarEl = document.getElementById('chatPeerAvatar');
    const peerDot      = document.getElementById('chatPeerDot');
    setAvatarEl(peerAvatarEl, peerAvatarUrl, initials, color);
    peerAvatarEl.appendChild(peerDot); // re-attach dot after innerHTML wipe
    document.getElementById('chatPeerName').textContent = name;

    // Set online status
    const statusEl = document.getElementById('chatPeerStatus');
    const statusTx = document.getElementById('chatPeerStatusText');
    const dot      = document.getElementById('chatPeerDot');
    if (dot)      dot.className      = isOnline ? 'online-dot' : 'offline-dot';
    if (statusEl) statusEl.className = 'chat-peer-status' + (isOnline ? ' is-online' : '');
    if (statusTx) statusTx.textContent = lastSeenText || (isOnline ? 'Online' : 'Offline');

    // Reset pagination
    currentPage = 1;
    lastPage    = 1;

    await loadMessages(id, 1);
}

// ── Load Messages (with pagination) ───────────────────────────
async function loadMessages(convId, page) {
    const area   = document.getElementById('messagesArea');
    const typing = document.getElementById('typingIndicator');
    const lmWrap = document.getElementById('loadMoreWrap');

    if (page === 1) {
        // Fresh load — clear everything
        area.innerHTML = '';
        area.appendChild(lmWrap);
        area.appendChild(typing);
        delete area.dataset.lastMsgDate;
    }

    try {
        const res = await fetch(`/chat/${convId}/messages?page=${page}&per_page=30`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        lastPage    = data.last_page ?? 1;
        currentPage = data.current_page ?? 1;

        // Show "Load earlier" if there are older pages
        lmWrap.style.display = (lastPage > 1 && currentPage < lastPage) ? 'block' : 'none';

        if (!data.messages || data.messages.length === 0) {
            if (page === 1) {
                const div = document.createElement('div');
                div.className   = 'msg-day-divider';
                div.textContent = 'Start of conversation';
                area.insertBefore(div, lmWrap.nextSibling);
            }
        } else {
            // Preserve scroll position when prepending older messages
            const prevHeight = area.scrollHeight;

            data.messages.forEach(m => appendMessage({ ...m }, page > 1 ? 'prepend' : 'append'));

            if (page > 1) {
                area.scrollTop = area.scrollHeight - prevHeight;
            }
        }

        if (page === 1) {
            area.scrollTop = area.scrollHeight;
        }

        // Mark messages as seen
        markConversationSeen(convId);

    } catch (err) {
        console.warn('Load messages error:', err.message);
        if (page === 1) {
            const div = document.createElement('div');
            div.className   = 'msg-day-divider';
            div.textContent = 'Start of conversation';
            area.insertBefore(div, lmWrap.nextSibling);
        }
    }
}

// ── Load Older Messages (scroll up pagination) ─────────────────
async function loadOlderMessages() {
    if (currentPage >= lastPage) return;
    await loadMessages(currentConvId, currentPage + 1);
    currentPage++;
}

// ── Send Message (text or image) ──────────────────────────────
async function sendMessage() {
    if (!currentConvId) return;
    const input = document.getElementById('msgInput');
    const text  = input.value.trim();

    if (!text && !selectedImageFile) return;

    // Build FormData so we can send both text and image
    const formData = new FormData();
    if (text)              formData.append('content', text);
    if (selectedImageFile) formData.append('image', selectedImageFile);

    // Optimistic UI
    const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    if (selectedImageFile) {
        const localUrl = URL.createObjectURL(selectedImageFile);
        appendMessage({ content: text, image_url: localUrl, time, sent: true, read: false });
    } else {
        appendMessage({ content: text, time, sent: true, read: false });
    }

    updateSidebarPreview(currentConvId, text || '📷 Image');

    // Clear inputs
    input.value        = '';
    input.style.height = 'auto';
    clearImageSelection();

    try {
        await fetch(`/chat/${currentConvId}/messages`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            // NOTE: no Content-Type header — browser sets it with boundary for FormData
            body: formData,
        });
    } catch (err) {
        console.warn('Send error:', err.message);
    }
}

// ── Append Message ─────────────────────────────────────────────
function appendMessage({ content, image_url, avatar_url, time, created_at, initials, color, sent, read }, direction = 'append') {
    const area   = document.getElementById('messagesArea');
    const typing = document.getElementById('typingIndicator');
    const lmWrap = document.getElementById('loadMoreWrap');
    const div    = document.createElement('div');
    div.className = `msg-group ${sent ? 'sent' : ''}`;

    const aColor = sent ? ME_COLOR : (color    || currentPeerColor);
    const aInit  = sent ? ME_INIT  : (initials || currentPeerInit);

    // Build bubble content: image, text, or both
    let bubbleContent = '';
    if (image_url) {
        bubbleContent += `<img class="msg-image" src="${escHtml(image_url)}" alt="image" onclick="openLightbox('${escHtml(image_url)}')">`;
    }
    if (content) {
        bubbleContent += `<div class="msg-bubble">${escHtml(content)}</div>`;
    }

    // Seen tick icon (only for sent messages)
    const tickHtml = sent
        ? `<i class="fas fa-check-double msg-tick${read ? ' seen' : ''}" title="${read ? 'Seen' : 'Delivered'}"></i>`
        : '';

    const avatarUrl = sent ? ME_AVATAR : (avatar_url ?? currentPeerAvatar ?? null);

    div.innerHTML = `
    <div class="msg-avatar" style="background:${aColor}"></div>
    <div class="msg-bubbles">
        ${bubbleContent}
        <div class="msg-time">${escHtml(time)} ${tickHtml}</div>
    </div>`;

    setAvatarEl(div.querySelector('.msg-avatar'), avatarUrl, aInit, aColor);

    if (created_at) {
        const msgDateStr = new Date(created_at).toDateString();
        if (direction === 'append') {
            if (msgDateStr !== (area.dataset.lastMsgDate ?? '')) {
                const divider = document.createElement('div');
                divider.className   = 'msg-day-divider';
                divider.textContent = getDateLabel(created_at);
                area.insertBefore(divider, typing);
                area.dataset.lastMsgDate = msgDateStr;
            }
        }
        if (direction === 'prepend') {
            const existingDividers = area.querySelectorAll('.msg-day-divider');
            const firstDividerDate = existingDividers[0]?.dataset.dividerDate ?? '';
            if (msgDateStr !== firstDividerDate) {
                const divider = document.createElement('div');
                divider.className           = 'msg-day-divider';
                divider.dataset.dividerDate = msgDateStr;
                divider.textContent         = getDateLabel(created_at);
                area.insertBefore(divider, lmWrap.nextSibling);
            }
        }
    }

    if (direction === 'prepend') {

        if (direction === 'prepend') {
            const existingDividers = area.querySelectorAll('.msg-day-divider');
            const firstDividerDate = existingDividers[0]?.dataset.dividerDate ?? '';
            if (msgDateStr !== firstDividerDate) {
                const divider = document.createElement('div');
                divider.className            = 'msg-day-divider';
                divider.dataset.dividerDate  = msgDateStr;
                divider.textContent          = getDateLabel(created_at);
                area.insertBefore(divider, lmWrap.nextSibling);
            }
        }
    }

    if (direction === 'prepend') {
        // Insert after the load-more button
        const afterEl = lmWrap.nextSibling;
        area.insertBefore(div, afterEl);
    } else {
        // Insert before the typing indicator
        area.insertBefore(div, typing);
        area.scrollTop = area.scrollHeight;
    }
}

// ── Image picking ──────────────────────────────────────────────
function onImageSelected(input) {
    const file = input.files[0];
    if (!file) return;
    selectedImageFile = file;

    const strip = document.getElementById('imgPreviewStrip');
    const thumb = document.getElementById('imgPreviewThumb');
    const name  = document.getElementById('imgPreviewName');

    thumb.src        = URL.createObjectURL(file);
    name.textContent = file.name;
    strip.classList.add('show');
}

function clearImageSelection() {
    selectedImageFile = null;
    document.getElementById('imageFileInput').value = '';
    document.getElementById('imgPreviewStrip').classList.remove('show');
    document.getElementById('imgPreviewThumb').src = '';
}

// ── Image Lightbox ─────────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('active');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
}

// ── WebRTC Calls ────────────────────────────────────────────────
function getActiveCallName() {
    const topbarName = document.getElementById('chatPeerName')?.textContent?.trim();
    return activeCallPeerName || topbarName || 'Contact';
}

function getInitialFromName(name) {
    return (name || '?').trim().charAt(0).toUpperCase() || '?';
}

function setCallActionDisabled(disabled) {
    const btn = document.getElementById('startVideoCallBtn');
    if (btn) btn.disabled = disabled || !currentPeerId;
}

function showActiveCallOverlay(statusText) {
    const peerName = getActiveCallName();
    const initial  = activeCallPeerInitial || getInitialFromName(peerName);
    const overlay  = document.getElementById('callOverlay');

    overlay.classList.add('active');
    overlay.classList.remove('incoming');
    document.getElementById('callPeerTitle').textContent = peerName;
    document.getElementById('callPlaceholderInitial').textContent = initial;
    document.getElementById('callPlaceholderName').textContent = peerName;
    document.getElementById('callPlaceholderStatus').textContent = 'Waiting for video';
    setCallStatus(statusText || 'Connecting');
    setCallActionDisabled(true);
}

function showIncomingCallOverlay(signal) {
    const peerName = signal.sender_name || 'Contact';
    const initial  = getInitialFromName(peerName);
    const overlay  = document.getElementById('callOverlay');

    overlay.classList.add('active', 'incoming');
    document.getElementById('incomingCallName').textContent = peerName;
    document.getElementById('incomingCallInitial').textContent = initial;
    document.getElementById('incomingCallStatus').textContent = 'Video call';
    setCallActionDisabled(true);
}

function hideCallOverlay() {
    const overlay = document.getElementById('callOverlay');
    overlay.classList.remove('active', 'incoming');
}

function setCallStatus(text) {
    const status = document.getElementById('callStatus');
    if (status) status.textContent = text;
}

function updateRemoteVideoState() {
    const placeholder = document.getElementById('callPlaceholder');
    const hasVideo = remoteStream?.getVideoTracks().some(track => track.readyState !== 'ended');
    if (placeholder) placeholder.style.display = hasVideo ? 'none' : 'flex';
}

function getIceServers() {
    if (Array.isArray(WEBRTC_ICE_SERVERS) && WEBRTC_ICE_SERVERS.length > 0) {
        return WEBRTC_ICE_SERVERS;
    }

    return [
        { urls: ['stun:stun.l.google.com:19302', 'stun:stun1.l.google.com:19302'] },
    ];
}

async function ensureLocalStream() {
    if (localStream) return localStream;
    if (!navigator.mediaDevices?.getUserMedia) {
        throw new Error('Media devices are not available in this browser context.');
    }

    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    } catch (videoErr) {
        console.warn('Video capture failed, trying audio only:', videoErr.message);
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
    }

    document.getElementById('localVideo').srcObject = localStream;
    updateMediaButtons();
    return localStream;
}

function createPeerConnection() {
    if (peerConnection) return peerConnection;

    remoteStream = new MediaStream();
    document.getElementById('remoteVideo').srcObject = remoteStream;
    updateRemoteVideoState();

    peerConnection = new RTCPeerConnection({ iceServers: getIceServers() });

    peerConnection.onicecandidate = (event) => {
        if (!event.candidate) return;
        sendSignal('ice-candidate', {
            candidate: event.candidate.toJSON ? event.candidate.toJSON() : event.candidate,
        }).catch(err => console.warn('ICE send error:', err.message));
    };

    peerConnection.ontrack = (event) => {
        event.streams[0].getTracks().forEach(track => {
            if (!remoteStream.getTracks().some(existing => existing.id === track.id)) {
                remoteStream.addTrack(track);
            }
        });
        setCallStatus('Connected');
        updateRemoteVideoState();
    };

    peerConnection.onconnectionstatechange = () => {
        const state = peerConnection?.connectionState;
        if (state === 'connected') setCallStatus('Connected');
        if (state === 'connecting') setCallStatus('Connecting');
        if (state === 'disconnected') setCallStatus('Reconnecting');
        if (state === 'failed') setCallStatus('Connection failed');
        if (state === 'closed') updateRemoteVideoState();
    };

    return peerConnection;
}

function addLocalTracks() {
    if (!peerConnection || !localStream) return;
    const existingSenders = peerConnection.getSenders().map(sender => sender.track?.id);
    localStream.getTracks().forEach(track => {
        if (!existingSenders.includes(track.id)) {
            peerConnection.addTrack(track, localStream);
        }
    });
}

function normalizeSdp(sdp) {
    if (typeof sdp !== 'string') {
        throw new Error('Missing SDP text in WebRTC signal.');
    }

    const cleaned = sdp
        .replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F]/g, '')
        .replace(/\r\n|\r|\n/g, '\n')
        .split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0)
        .join('\r\n');

    return cleaned + '\r\n';
}

function serializeSessionDescription(description) {
    const plain = typeof description?.toJSON === 'function'
        ? description.toJSON()
        : description;

    if (!plain?.type || !plain?.sdp) {
        throw new Error('Invalid local WebRTC description.');
    }

    return {
        type: plain.type,
        sdp: normalizeSdp(plain.sdp),
    };
}

function getRemoteDescription(signal) {
    const payload = signal?.payload || {};
    const description = payload.description || payload.sdp || payload;
    const type = description?.type || payload.type || (signal.type === 'offer' || signal.type === 'answer' ? signal.type : null);
    const sdp = typeof description === 'string' ? description : description?.sdp;

    if (!type || !sdp) {
        throw new Error('Invalid remote WebRTC description.');
    }

    return new RTCSessionDescription({
        type,
        sdp: normalizeSdp(sdp),
    });
}

async function sendSignal(type, payload = {}, convId = null) {
    const targetConvId = convId || activeCallConvId || currentConvId;
    if (!targetConvId) return;

    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
    };

    const socketId = window.Echo?.socketId?.();
    if (socketId) headers['X-Socket-ID'] = socketId;

    const res = await fetch(`/chat/${targetConvId}/signal`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ type, payload }),
    });

    if (!res.ok) throw new Error(`Signal HTTP ${res.status}`);
}

async function startVideoCall() {
    if (!currentConvId || !currentPeerId || peerConnection || pendingIncomingOffer) return;

    activeCallConvId = currentConvId;
    activeCallPeerId = currentPeerId;
    activeCallPeerName = getActiveCallName();
    activeCallPeerInitial = currentPeerInit || getInitialFromName(activeCallPeerName);
    pendingIceCandidates = [];
    callEnding = false;

    showActiveCallOverlay('Starting call');

    try {
        await ensureLocalStream();
        createPeerConnection();
        addLocalTracks();

        const offer = await peerConnection.createOffer({
            offerToReceiveAudio: true,
            offerToReceiveVideo: true,
        });
        await peerConnection.setLocalDescription(offer);
        await sendSignal('offer', { description: serializeSessionDescription(peerConnection.localDescription) });
        setCallStatus('Ringing');
    } catch (err) {
        console.warn('Start call error:', err.message);
        setCallStatus('Unable to start call');
        setTimeout(() => endCall(false), 1000);
    }
}

async function handleWebRTCSignal(signal) {
    if (!signal || parseInt(signal.sender_id) === ME_ID) return;

    try {
        if (signal.type === 'offer') await handleIncomingOffer(signal);
        if (signal.type === 'answer') await handleAnswer(signal);
        if (signal.type === 'ice-candidate') await handleIceCandidate(signal);
        if (signal.type === 'call-rejected') handleCallRejected(signal);
        if (signal.type === 'call-ended') handleRemoteCallEnded(signal);
    } catch (err) {
        console.warn('WebRTC signal error:', err.message);
        setCallStatus('Call error');
    }
}

async function handleIncomingOffer(signal) {
    const convId = parseInt(signal.conversation_id);

    if (peerConnection || activeCallConvId || pendingIncomingOffer) {
        await sendSignal('call-rejected', { reason: 'busy' }, convId).catch(() => {});
        return;
    }

    pendingIncomingOffer = signal;
    pendingIceCandidates = [];
    activeCallPeerId = parseInt(signal.sender_id);
    activeCallPeerName = signal.sender_name || 'Contact';
    activeCallPeerInitial = getInitialFromName(activeCallPeerName);
    showIncomingCallOverlay(signal);
}

async function acceptIncomingCall() {
    if (!pendingIncomingOffer) return;

    const signal = pendingIncomingOffer;
    const convId = parseInt(signal.conversation_id);
    activeCallConvId = convId;
    activeCallPeerId = parseInt(signal.sender_id);
    activeCallPeerName = signal.sender_name || 'Contact';
    activeCallPeerInitial = getInitialFromName(activeCallPeerName);
    pendingIncomingOffer = null;
    callEnding = false;

    showActiveCallOverlay('Connecting');

    try {
        await ensureLocalStream();
        createPeerConnection();
        addLocalTracks();
        await peerConnection.setRemoteDescription(getRemoteDescription(signal));
        await flushPendingIceCandidates();

        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        await sendSignal('answer', { description: serializeSessionDescription(peerConnection.localDescription) }, convId);
        setCallStatus('Connecting');
    } catch (err) {
        console.warn('Accept call error:', err.message);
        setCallStatus('Unable to answer call');
        await sendSignal('call-ended', { reason: 'answer-failed' }, convId).catch(() => {});
        setTimeout(() => endCall(false), 1000);
    }
}

function rejectIncomingCall() {
    const convId = pendingIncomingOffer ? parseInt(pendingIncomingOffer.conversation_id) : activeCallConvId;
    if (convId) {
        sendSignal('call-rejected', { reason: 'declined' }, convId).catch(() => {});
    }
    cleanupCall();
}

async function handleAnswer(signal) {
    const convId = parseInt(signal.conversation_id);
    if (!peerConnection || convId !== activeCallConvId) return;
    await peerConnection.setRemoteDescription(getRemoteDescription(signal));
    await flushPendingIceCandidates();
    setCallStatus('Connecting');
}

async function handleIceCandidate(signal) {
    const convId = parseInt(signal.conversation_id);
    const candidate = signal.payload?.candidate;
    if (!candidate) return;

    const isActiveCall = convId === activeCallConvId;
    const isPendingCall = pendingIncomingOffer && convId === parseInt(pendingIncomingOffer.conversation_id);
    if (!isActiveCall && !isPendingCall) return;

    if (!peerConnection || !peerConnection.remoteDescription?.type) {
        pendingIceCandidates.push(candidate);
        return;
    }

    await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
}

async function flushPendingIceCandidates() {
    if (!peerConnection || !peerConnection.remoteDescription?.type) return;

    while (pendingIceCandidates.length > 0) {
        const candidate = pendingIceCandidates.shift();
        await peerConnection.addIceCandidate(new RTCIceCandidate(candidate)).catch(err => {
            console.warn('ICE add error:', err.message);
        });
    }
}

function handleCallRejected(signal) {
    const convId = parseInt(signal.conversation_id);
    if (convId !== activeCallConvId) return;

    const reason = signal.payload?.reason;
    setCallStatus(reason === 'busy' ? 'User is busy' : 'Call declined');
    setTimeout(() => endCall(false), 1000);
}

function handleRemoteCallEnded(signal) {
    const convId = parseInt(signal.conversation_id);
    const pendingConvId = pendingIncomingOffer ? parseInt(pendingIncomingOffer.conversation_id) : null;
    if (convId !== activeCallConvId && convId !== pendingConvId) return;

    setCallStatus('Call ended');
    setTimeout(() => cleanupCall(), 700);
}

function endCall(notifyPeer = false) {
    const convId = activeCallConvId || (pendingIncomingOffer ? parseInt(pendingIncomingOffer.conversation_id) : null);
    if (notifyPeer && convId && !callEnding) {
        sendSignal('call-ended', {}, convId).catch(() => {});
    }
    cleanupCall();
}

function cleanupCall() {
    callEnding = true;

    if (peerConnection) {
        peerConnection.onicecandidate = null;
        peerConnection.ontrack = null;
        peerConnection.onconnectionstatechange = null;
        peerConnection.close();
    }

    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }

    if (remoteStream) {
        remoteStream.getTracks().forEach(track => track.stop());
    }

    document.getElementById('localVideo').srcObject = null;
    document.getElementById('remoteVideo').srcObject = null;
    hideCallOverlay();

    peerConnection = null;
    localStream = null;
    remoteStream = null;
    activeCallConvId = null;
    activeCallPeerId = null;
    activeCallPeerName = '';
    activeCallPeerInitial = '?';
    pendingIncomingOffer = null;
    pendingIceCandidates = [];
    callEnding = false;
    updateRemoteVideoState();
    updateMediaButtons();
    setCallActionDisabled(false);
}

function updateMediaButtons() {
    const micBtn = document.getElementById('toggleMicBtn');
    const camBtn = document.getElementById('toggleCameraBtn');
    const audioTrack = localStream?.getAudioTracks()[0] || null;
    const videoTrack = localStream?.getVideoTracks()[0] || null;

    if (micBtn) {
        micBtn.disabled = !audioTrack;
        micBtn.classList.toggle('off', !!audioTrack && !audioTrack.enabled);
        micBtn.querySelector('i').className = audioTrack && audioTrack.enabled ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    }

    if (camBtn) {
        camBtn.disabled = !videoTrack;
        camBtn.classList.toggle('off', !!videoTrack && !videoTrack.enabled);
        camBtn.querySelector('i').className = videoTrack && videoTrack.enabled ? 'fas fa-video' : 'fas fa-video-slash';
    }
}

function toggleMic() {
    localStream?.getAudioTracks().forEach(track => {
        track.enabled = !track.enabled;
    });
    updateMediaButtons();
}

function toggleCamera() {
    localStream?.getVideoTracks().forEach(track => {
        track.enabled = !track.enabled;
    });
    updateMediaButtons();
}

// ── Typing ─────────────────────────────────────────────────────
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

// ── Sidebar helpers ────────────────────────────────────────────
function updateSidebarPreview(convId, text) {
    const el = document.getElementById(`conv-preview-${convId}`);
    const te = document.getElementById(`conv-time-${convId}`);
    if (el) el.textContent = text.length > 38 ? text.slice(0, 38) + '…' : text;
    if (te) te.textContent = 'Just now';
}

// ── New Conversation Modal ─────────────────────────────────────
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

function searchConvs(q) {
    document.querySelectorAll('.conv-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q.toLowerCase()) ? 'flex' : 'none';
    });
}

// ── Misc ───────────────────────────────────────────────────────
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function getDateLabel(isoString) {
    const msgDate   = new Date(isoString);
    const today     = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);

    if (msgDate.toDateString() === today.toDateString())     return 'Today';
    if (msgDate.toDateString() === yesterday.toDateString()) return 'Yesterday';

    const diffDays = Math.floor((today - msgDate) / (1000 * 60 * 60 * 24));

    if (diffDays < 7) {
        return msgDate.toLocaleDateString('en-US', { weekday: 'long' });
    }
    if (diffDays < 365) {
        return msgDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }
    return msgDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// ── Auto-open on page load ─────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    initEcho();
    startHeartbeat();

    const openId = parseInt(new URLSearchParams(window.location.search).get('open'));
    if (openId) {
        const el = document.getElementById(`conv-item-${openId}`);
        if (el) el.click();
    } else {
        @if($conversations->isNotEmpty())
        document.getElementById('conv-item-{{ $conversations->first()['id'] }}')?.click();
        @endif
    }
});

// Stop realtime helpers when user leaves page
window.addEventListener('beforeunload', () => {
    endCall(true);
    stopHeartbeat();
});
</script>
@endpush
