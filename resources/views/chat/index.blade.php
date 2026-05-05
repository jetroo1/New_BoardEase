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
                {{-- No call buttons per your request --}}
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

@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const ME_ID    = {{ auth()->id() }};
const ME_INIT  = '{{ $userInitials }}';
const ME_COLOR = '{{ $userColor }}';
const ME_AVATAR = @json(auth()->user()->avatar_url ?? '');

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

// Stop heartbeat when user leaves page
window.addEventListener('beforeunload', stopHeartbeat);
</script>
@endpush