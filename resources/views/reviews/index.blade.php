@extends('layouts.app')

@section('title', 'Reviews')
@section('search-placeholder', 'Search reviews...')

@push('styles')
<style>
/* ── Star color fix ── */
#pick-stars i,
[id^="stars-"] i {
    color: #e2e8f0;
}

/* ── Page text ── */
.rev-page-title { color: var(--text); }
.rev-page-sub   { color: var(--text-muted); }

/* ── Section label ── */
.rev-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 12px;
    margin-top: 24px;
}

/* ── Pick-form card (teal border) ── */
.rev-pick-card {
    background: var(--card);
    border: 2px solid var(--teal);
    border-radius: 1rem;
    padding: 20px;
    margin-bottom: 16px;
}
.rev-pick-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 1rem;
    color: var(--text);
    margin-bottom: 4px;
}
.rev-pick-card-sub {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 16px;
}

/* ── Dropdown select ── */
.rev-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    background: var(--card);
    color: var(--text);
    outline: none;
    margin-bottom: 12px;
    cursor: pointer;
    transition: border-color 0.2s;
}
.rev-select:focus { border-color: var(--teal); }

/* ── Property preview strip ── */
.rev-prop-preview {
    display: none;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg);
    border-radius: 12px;
    border: 1px solid var(--border);
    margin-bottom: 12px;
}
.rev-prop-preview.show { display: flex; }
.rev-prop-preview-name { font-size: 0.875rem; font-weight: 600; color: var(--text); }

/* ── Textarea ── */
.rev-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    background: var(--card);
    color: var(--text);
    outline: none;
    resize: none;
    margin-bottom: 4px;
    transition: border-color 0.2s;
}
.rev-textarea:focus { border-color: var(--teal); }
.rev-textarea::placeholder { color: var(--text-muted); }

/* ── Char counter ── */
.rev-char { font-size: 0.75rem; color: var(--text-muted); text-align: right; margin-bottom: 12px; }

/* ── Pending card (dashed border) ── */
.rev-pending-card {
    background: var(--card);
    border: 2px dashed var(--border);
    border-radius: 1rem;
    padding: 20px;
    margin-bottom: 16px;
}
.rev-pending-title { font-weight: 700; font-size: 0.875rem; color: var(--text); }
.rev-pending-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

/* ── Submitted review card ── */
.rev-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 20px;
}
.rev-card-prop-name   { font-weight: 700; font-size: 0.875rem; color: var(--text); }
.rev-card-reviewer    { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }
.rev-card-dot         { color: var(--border); }
.rev-card-date        { font-size: 0.75rem; color: var(--text-muted); }
.rev-card-body        { font-size: 0.875rem; color: var(--text); line-height: 1.6; }
.rev-card-delete      {
    display: flex; align-items: center; gap: 4px;
    font-size: 0.75rem; color: var(--text-muted);
    background: transparent; border: none;
    cursor: pointer; padding: 0; margin-top: 12px;
    transition: color 0.2s;
}
.rev-card-delete:hover { color: #ef4444; }

/* ── Empty text ── */
.rev-empty { font-size: 0.875rem; color: var(--text-muted); padding: 16px 0; }

/* ── Rating summary card ── */
.rev-summary-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 20px;
    position: sticky;
    top: 80px;
}
.rev-summary-title  { font-weight: 700; font-size: 1rem; color: var(--text); margin-bottom: 16px; }
.rev-summary-avg    { font-family: 'Syne', sans-serif; font-size: 3rem; font-weight: 800; color: var(--text); line-height: 1; }
.rev-summary-count  { font-size: 0.75rem; color: var(--text-muted); }
.rev-bar-label      { font-size: 0.875rem; color: var(--text-muted); width: 8px; }
.rev-bar-track      { flex: 1; height: 6px; background: var(--border); border-radius: 9999px; overflow: hidden; }
.rev-bar-count      { font-size: 0.72rem; color: var(--text-muted); width: 16px; text-align: right; }

/* ── Submit btn ── */
.rev-submit-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px;
    background: var(--navy);
    color: #fff;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600;
    border: none; border-radius: 12px;
    cursor: pointer; transition: background 0.2s;
}
.rev-submit-btn:hover:not(:disabled) { background: var(--navy-light); }
.rev-submit-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Star active state ── */
.star-active { color: #f59e0b !important; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="font-syne text-3xl font-bold rev-page-title">Reviews</h1>
    <p class="text-sm mt-1 rev-page-sub">Your reviews and pending feedback</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl mb-4">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl mb-4">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl mb-4">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5">

    {{-- LEFT COLUMN --}}
    <div>

        {{-- Review Any Boarding House --}}
        <div class="rev-pick-card">
            <div class="rev-pick-card-title">
                <i class="fas fa-search text-teal-500 text-sm"></i>
                Review Any Boarding House
            </div>
            <p class="rev-pick-card-sub">Choose any boarding house from the list below to write a review.</p>

            <form method="POST" action="{{ route('reviews.store') }}" id="pick-form">
                @csrf
                <input type="hidden" name="property_id" id="pick-property-id">
                <input type="hidden" name="rating" id="pick-rating" value="0">

                {{-- BH Dropdown --}}
                <select class="rev-select" id="pick-bh-select" onchange="onSelectBH(this)">
                    <option value="">— Select a boarding house —</option>
                    @foreach($allProperties as $prop)
                    <option
                        value="{{ $prop->id }}"
                        data-name="{{ $prop->title }}"
                        data-img="{{ $prop->image ? Storage::url($prop->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=100&q=80' }}">
                        {{ $prop->title }}
                    </option>
                    @endforeach
                </select>

                {{-- Property Preview --}}
                <div id="selected-prop-preview" class="rev-prop-preview">
                    <img id="preview-img" src="" alt="" class="w-10 h-10 rounded-lg object-cover">
                    <span id="preview-name" class="rev-prop-preview-name"></span>
                </div>

                {{-- Star Picker --}}
                <div class="flex gap-2 mb-3" id="pick-stars">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-2xl cursor-pointer transition"
                       data-value="{{ $i }}"
                       onclick="setPickStars({{ $i }})"
                       onmouseover="hoverPickStars({{ $i }})"
                       onmouseout="resetPickStarsHover()"
                       title="{{ $i }} star"></i>
                    @endfor
                </div>

                {{-- Textarea --}}
                <textarea
                    class="rev-textarea"
                    name="body"
                    id="pick-body"
                    rows="3"
                    maxlength="1000"
                    placeholder="Share your experience at this boarding house..."
                    oninput="updateCharCount(this, 'pick-char'); validatePickForm();"
                ></textarea>
                <div class="rev-char" id="pick-char">0 / 1000</div>

                <button
                    type="submit"
                    class="rev-submit-btn"
                    id="pick-submit"
                    disabled>
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
        </div>

        {{-- Pending Reviews --}}
        @if($pendingReviews->count())
        <div class="rev-section-label">
            Pending Reviews ({{ $pendingReviews->count() }})
        </div>

        @foreach($pendingReviews as $booking)
        <div class="rev-pending-card">
            <div class="flex items-center gap-3 mb-4">
                <img class="w-12 h-12 rounded-xl object-cover"
                     src="{{ $booking->property->image ? Storage::url($booking->property->image) : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=100&q=80' }}"
                     alt="{{ $booking->property->title }}">
                <div>
                    <div class="rev-pending-title">{{ $booking->property->title }}</div>
                    <div class="rev-pending-sub">
                        Check-in: {{ \Carbon\Carbon::parse($booking->check_in)->format('M j, Y') }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('reviews.store') }}">
                @csrf
                <input type="hidden" name="property_id" value="{{ $booking->property_id }}">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="rating" class="rating-val" value="0">

                <div class="flex gap-2 mb-3" id="stars-{{ $booking->id }}">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-2xl cursor-pointer transition"
                       data-value="{{ $i }}"
                       onclick="setStars('stars-{{ $booking->id }}', {{ $i }})"
                       onmouseover="hoverStars('stars-{{ $booking->id }}', {{ $i }})"
                       onmouseout="resetStarsHover('stars-{{ $booking->id }}')"></i>
                    @endfor
                </div>

                <textarea
                    class="rev-textarea"
                    name="body"
                    rows="3"
                    maxlength="1000"
                    placeholder="Share your experience..."
                    oninput="updateCharCount(this, 'char-{{ $booking->id }}')"
                ></textarea>
                <div class="rev-char" id="char-{{ $booking->id }}">0 / 1000</div>

                <button type="submit" class="rev-submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
        </div>
        @endforeach
        @endif

        {{-- Your Submitted Reviews --}}
        <div class="rev-section-label">
            Your Reviews ({{ $myReviews->count() }})
        </div>

        @if($myReviews->count())
        <div class="flex flex-col gap-4">
            @foreach($myReviews as $review)
            <div class="rev-card">

                {{-- Property + Reviewer Info --}}
                <div class="flex items-center gap-3 mb-3">
                    <img class="w-12 h-12 rounded-xl object-cover flex-shrink-0"
                         src="{{ $review->property->image ? Storage::url($review->property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=100&q=80' }}"
                         alt="{{ $review->property->title }}">
                    <div class="flex-1">
                        <div class="rev-card-prop-name">{{ $review->property->title }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=2ec4a5&color=fff&size=20"
                                 class="w-4 h-4 rounded-full">
                            <span class="rev-card-reviewer">{{ $review->user->name }}</span>
                            <span class="rev-card-dot">·</span>
                            <span class="rev-card-date">{{ $review->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Stars --}}
                <div class="flex gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-sm" style="color: {{ $i <= $review->rating ? '#f59e0b' : '#e2e8f0' }};"></i>
                    @endfor
                </div>

                {{-- Review Text --}}
                <p class="rev-card-body">{{ $review->body }}</p>

                {{-- Delete --}}
                <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                      onsubmit="return confirm('Delete this review?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rev-card-delete">
                        <i class="fas fa-trash-alt"></i> Delete review
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p class="rev-empty">You haven't submitted any reviews yet.</p>
        @endif

    </div>

    {{-- RIGHT COLUMN - Summary --}}
    <div>
        <div class="rev-summary-card">
            <h3 class="rev-summary-title">Your Rating Summary</h3>

            <div class="text-center mb-5">
                <div class="rev-summary-avg">{{ $avgRating ?: '—' }}</div>
                <div class="flex justify-center gap-1 my-2">
                    @php $full = floor($avgRating); $half = ($avgRating - $full) >= 0.5; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $full)
                            <i class="fas fa-star text-yellow-400"></i>
                        @elseif($i == $full + 1 && $half)
                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                        @else
                            <i class="far fa-star" style="color:#e2e8f0;"></i>
                        @endif
                    @endfor
                </div>
                <div class="rev-summary-count">
                    Average across {{ $myReviews->count() }} {{ Str::plural('review', $myReviews->count()) }}
                </div>
            </div>

            @php $total = $myReviews->count() ?: 1; @endphp
            @foreach($distribution as $stars => $count)
            <div class="flex items-center gap-2 mb-2">
                <span class="rev-bar-label">{{ $stars }}</span>
                <div class="rev-bar-track">
                    <div class="h-full bg-yellow-400 rounded-full"
                         style="width:{{ ($count / $total) * 100 }}%"></div>
                </div>
                <span class="rev-bar-count">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Pick-form stars ──────────────────────────────────────────────
let _pickedRating = 0;

function setPickStars(val) {
    _pickedRating = val;
    document.getElementById('pick-rating').value = val;
    _renderPickStars(val);
    validatePickForm();
}

function hoverPickStars(val) { _renderPickStars(val); }
function resetPickStarsHover() { _renderPickStars(_pickedRating); }

function _renderPickStars(val) {
    document.querySelectorAll('#pick-stars i').forEach(function(s, i) {
        if (i < val) {
            s.classList.add('star-active');
        } else {
            s.classList.remove('star-active');
        }
    });
}

// ── Pending-review stars ─────────────────────────────────────────
const _pendingRatings = {};

function setStars(containerId, val) {
    _pendingRatings[containerId] = val;
    const container = document.getElementById(containerId);
    if (!container) return;
    _renderStars(containerId, val);
    const form = container.closest('form');
    if (form) {
        const hidden = form.querySelector('.rating-val');
        if (hidden) hidden.value = val;
    }
}

function hoverStars(containerId, val) { _renderStars(containerId, val); }
function resetStarsHover(containerId) { _renderStars(containerId, _pendingRatings[containerId] || 0); }

function _renderStars(containerId, val) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.querySelectorAll('i').forEach(function(s, i) {
        if (i < val) {
            s.classList.add('star-active');
        } else {
            s.classList.remove('star-active');
        }
    });
}

// ── Validate pick form ───────────────────────────────────────────
function onSelectBH(select) {
    const opt      = select.options[select.selectedIndex];
    const propId   = opt.value;
    const propImg  = opt.dataset.img  || '';
    const propName = opt.dataset.name || '';

    document.getElementById('pick-property-id').value = propId;

    const preview = document.getElementById('selected-prop-preview');
    if (propId) {
        document.getElementById('preview-img').src = propImg;
        document.getElementById('preview-name').textContent = propName;
        preview.classList.add('show');
    } else {
        preview.classList.remove('show');
    }
    validatePickForm();
}

function validatePickForm() {
    const propId = document.getElementById('pick-property-id').value;
    const rating = parseInt(document.getElementById('pick-rating').value);
    const body   = document.getElementById('pick-body').value.trim();
    const btn    = document.getElementById('pick-submit');
    const isValid = propId && rating >= 1 && body.length >= 1;
    btn.disabled = !isValid;
}

document.getElementById('pick-body').addEventListener('input', validatePickForm);

// ── Character counter ────────────────────────────────────────────
function updateCharCount(textarea, countId) {
    const el  = document.getElementById(countId);
    if (!el) return;
    const len = textarea.value.length;
    el.textContent = `${len} / 1000`;
    el.className = 'rev-char ' + (len > 950 ? 'text-red-400' : len > 800 ? 'text-yellow-400' : '');
}
</script>
@endpush