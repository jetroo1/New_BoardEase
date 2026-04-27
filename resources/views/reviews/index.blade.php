@extends('layouts.app')

@section('title', 'Reviews')
@section('search-placeholder', 'Search reviews...')

@push('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 700; }
    .page-header p { font-size: 0.875rem; color: var(--text-muted); margin-top: 4px; }

    .reviews-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; }

    .reviews-list { display: flex; flex-direction: column; gap: 14px; }

    .review-card { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }

    .review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .review-prop-img { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
    .review-prop-name { font-weight: 700; font-size: 0.95rem; }
    .review-prop-date { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }

    .review-stars { display: flex; gap: 3px; margin-bottom: 10px; }
    .review-stars i { color: var(--yellow); font-size: 0.85rem; }
    .review-stars i.empty { color: var(--border); }

    .review-text { font-size: 0.875rem; line-height: 1.6; color: var(--text); }

    .review-card-pending {
        background: var(--card);
        border-radius: 14px;
        border: 2px dashed var(--border);
        padding: 20px;
    }

    .star-picker { display: flex; gap: 6px; margin-bottom: 12px; }
    .star-picker i { font-size: 1.5rem; color: var(--border); cursor: pointer; transition: color 0.15s; }
    .star-picker i.active { color: var(--yellow); }

    .review-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        resize: none;
        outline: none;
        margin-bottom: 12px;
        transition: border-color 0.2s;
        background: var(--card);
        color: var(--text);
    }
    .review-textarea:focus { border-color: var(--teal); }

    /* Custom select dropdown */
    .bh-select {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        outline: none;
        margin-bottom: 12px;
        background: var(--card);
        color: var(--text);
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .bh-select:focus { border-color: var(--teal); }

    /* Summary card */
    .summary-card { background: var(--card); border-radius: 14px; border: 1px solid var(--border); padding: 20px; }
    .summary-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 16px; }

    .overall-rating { text-align: center; margin-bottom: 20px; }
    .big-score { font-family: 'Syne', sans-serif; font-size: 3rem; font-weight: 800; color: var(--navy); line-height: 1; }
    .big-stars { display: flex; justify-content: center; gap: 4px; margin: 8px 0 4px; }
    .big-stars i { color: var(--yellow); }
    .big-label { font-size: 0.78rem; color: var(--text-muted); }

    .rating-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 0.8rem; }
    .rating-bar .bar { flex: 1; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
    .rating-bar .fill { height: 100%; background: var(--yellow); border-radius: 3px; }
    .rating-bar .count { color: var(--text-muted); width: 20px; text-align: right; }

    /* Section label */
    .section-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    /* Alert flash */
    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.875rem;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

    /* Delete button */
    .btn-delete {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 0.78rem;
        margin-top: 10px;
        padding: 0;
        transition: color 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-delete:hover { color: #ef4444; }

    /* Pick any BH card */
    .pick-bh-card {
        background: var(--card);
        border-radius: 14px;
        border: 1.5px solid var(--teal, #0ea5e9);
        padding: 20px;
        margin-bottom: 14px;
    }
    .pick-bh-card .pick-title {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pick-bh-card .pick-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 14px;
    }

    /* Property preview */
    #selected-prop-preview {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: var(--bg, #f8fafc);
        border-radius: 10px;
        margin-bottom: 12px;
        border: 1px solid var(--border);
    }
    #selected-prop-preview img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
    #selected-prop-preview span { font-size: 0.875rem; font-weight: 600; }

    .char-count { font-size: 0.75rem; color: var(--text-muted); text-align: right; margin-top: -8px; margin-bottom: 12px; }
    .char-count.near { color: #f59e0b; }
    .char-count.over  { color: #ef4444; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Reviews</h1>
    <p>Your reviews and pending feedback</p>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
@endif

<div class="reviews-layout">
    <div>

        {{-- ── PICK ANY BOARDING HOUSE ── --}}
        <div class="pick-bh-card">
            <div class="pick-title">
                <i class="fas fa-search" style="color:var(--teal);font-size:0.9rem;"></i>
                Review Any Boarding House
            </div>
            <div class="pick-sub">Choose any boarding house from the list below to write a review.</div>

            <form method="POST" action="{{ route('reviews.store') }}" id="pick-form">
                @csrf
                <input type="hidden" name="property_id" id="pick-property-id">
                <input type="hidden" name="rating"      id="pick-rating" value="0">

                <select class="bh-select" id="pick-bh-select" onchange="onSelectBH(this)">
    <option value="">— Select a boarding house —</option>
    @foreach($allProperties as $prop)
        <option
            value="{{ $prop->id }}"
            data-name="{{ $prop->title }}"
            data-img="{{ $prop->image ? asset('storage/' . $prop->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=100&q=80' }}"
        >
            {{ $prop->title }}
        </option>
    @endforeach
</select>

                {{-- Selected property preview --}}
                <div id="selected-prop-preview">
                    <img id="preview-img" src="" alt="">
                    <span id="preview-name"></span>
                </div>

                {{-- Star picker --}}
                <div class="star-picker" id="pick-stars">
                    <i class="fas fa-star" onclick="setPickStars(1)" title="1 star"></i>
                    <i class="fas fa-star" onclick="setPickStars(2)" title="2 stars"></i>
                    <i class="fas fa-star" onclick="setPickStars(3)" title="3 stars"></i>
                    <i class="fas fa-star" onclick="setPickStars(4)" title="4 stars"></i>
                    <i class="fas fa-star" onclick="setPickStars(5)" title="5 stars"></i>
                </div>

                <textarea
                    class="review-textarea"
                    name="body"
                    id="pick-body"
                    rows="3"
                    maxlength="1000"
                    placeholder="Share your experience at this boarding house..."
                    oninput="updateCharCount(this, 'pick-char')"
                ></textarea>
                <div class="char-count" id="pick-char">0 / 1000</div>

                <button type="submit" class="btn btn-primary btn-sm" id="pick-submit" disabled>
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
        </div>

        {{-- ── PENDING REVIEWS (from completed bookings) ── --}}
        @if($pendingReviews->count())
        <div class="section-label" style="margin-top:24px;">
            Pending Reviews ({{ $pendingReviews->count() }})
        </div>

        @foreach($pendingReviews as $booking)
        <div class="review-card-pending" style="margin-bottom:14px;">
            <div class="review-header">
                <img class="review-prop-img"
                     src="{{ $booking->property->image ? asset('storage/' . $booking->property->image) : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=100&q=80' }}"
                     alt="{{ $booking->property->name }}">
                <div>
                    <div class="review-prop-name">{{ $booking->property->name }}</div>
                    <div class="review-prop-date">
                        Stay: {{ \Carbon\Carbon::parse($booking->start_date)->format('M j, Y') }}
                        – {{ \Carbon\Carbon::parse($booking->end_date)->format('M j, Y') }}
                        · ₱{{ number_format($booking->total_amount, 2) }} paid
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('reviews.store') }}">
                @csrf
                <input type="hidden" name="property_id" value="{{ $booking->property_id }}">
                <input type="hidden" name="booking_id"  value="{{ $booking->id }}">
                <input type="hidden" name="rating" class="rating-val" value="0">

                <div class="star-picker" id="stars-{{ $booking->id }}">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star" onclick="setStars('stars-{{ $booking->id }}', {{ $i }})"></i>
                    @endfor
                </div>

                <textarea
                    class="review-textarea"
                    name="body"
                    rows="3"
                    maxlength="1000"
                    placeholder="Share your experience at this boarding house..."
                    oninput="updateCharCount(this, 'char-{{ $booking->id }}')"
                ></textarea>
                <div class="char-count" id="char-{{ $booking->id }}">0 / 1000</div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
        </div>
        @endforeach
        @endif

        {{-- ── YOUR SUBMITTED REVIEWS ── --}}
        <div class="section-label" style="margin-top:24px;">
            Your Reviews ({{ $myReviews->count() }})
        </div>

        @if($myReviews->count())
        <div class="reviews-list">
            @foreach($myReviews as $review)
            <div class="review-card">
                <div class="review-header">
                    <img class="review-prop-img"
                         src="{{ $review->property->image ? asset('storage/' . $review->property->image) : 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=100&q=80' }}"
                         alt="{{ $review->property->name }}">
                    <div>
                        <div class="review-prop-name">{{ $review->property->name }}</div>
                        <div class="review-prop-date">{{ $review->created_at->format('M Y') }}</div>
                    </div>
                </div>
                <div class="review-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i > $review->rating ? ' empty' : '' }}"></i>
                    @endfor
                </div>
                <div class="review-text">{{ $review->body }}</div>

                {{-- Delete own review --}}
                <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                      onsubmit="return confirm('Delete this review?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">
                        <i class="fas fa-trash-alt"></i> Delete review
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
            <div style="color:var(--text-muted);font-size:0.875rem;padding:16px 0;">
                You haven't submitted any reviews yet.
            </div>
        @endif

    </div>

    {{-- ── SUMMARY SIDEBAR ── --}}
    <div>
        <div class="summary-card">
            <h3>Your Rating Summary</h3>

            <div class="overall-rating">
                <div class="big-score">{{ $avgRating ?: '—' }}</div>
                <div class="big-stars">
                    @php $full = floor($avgRating); $half = ($avgRating - $full) >= 0.5; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $full)
                            <i class="fas fa-star"></i>
                        @elseif($i == $full + 1 && $half)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star" style="color:var(--border)"></i>
                        @endif
                    @endfor
                </div>
                <div class="big-label">Average across {{ $myReviews->count() }} {{ Str::plural('review', $myReviews->count()) }}</div>
            </div>

            @php $total = $myReviews->count() ?: 1; @endphp
            @foreach($distribution as $stars => $count)
            <div class="rating-bar">
                <span>{{ $stars }}</span>
                <div class="bar">
                    <div class="fill" style="width:{{ ($count / $total) * 100 }}%"></div>
                </div>
                <span class="count">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Star picker for pending booking cards ── */
function setStars(containerId, val) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const stars = container.querySelectorAll('i');
    stars.forEach((s, i) => {
        const on = i < val;
        s.classList.toggle('active', on);
        s.style.color = on ? 'var(--yellow)' : 'var(--border)';
    });
    // Write to hidden rating input in the nearest form
    const form = container.closest('form');
    if (form) {
        const hidden = form.querySelector('.rating-val');
        if (hidden) hidden.value = val;
    }
}

/* ── Star picker for the "pick any BH" card ── */
function setPickStars(val) {
    document.getElementById('pick-rating').value = val;
    const stars = document.querySelectorAll('#pick-stars i');
    stars.forEach((s, i) => {
        const on = i < val;
        s.classList.toggle('active', on);
        s.style.color = on ? 'var(--yellow)' : 'var(--border)';
    });
    validatePickForm();
}

/* ── Property selector ── */
function onSelectBH(select) {
    const opt     = select.options[select.selectedIndex];
    const propId  = opt.value;
    const propImg = opt.dataset.img  || '';
    const propName= opt.dataset.name || '';

    document.getElementById('pick-property-id').value = propId;

    const preview = document.getElementById('selected-prop-preview');
    if (propId) {
        document.getElementById('preview-img').src   = propImg;
        document.getElementById('preview-name').textContent = propName;
        preview.style.display = 'flex';
    } else {
        preview.style.display = 'none';
    }

    validatePickForm();
}

/* ── Validate before enabling submit ── */
function validatePickForm() {
    const propId = document.getElementById('pick-property-id').value;
    const rating = parseInt(document.getElementById('pick-rating').value);
    const body   = document.getElementById('pick-body').value.trim();
    const btn    = document.getElementById('pick-submit');
    btn.disabled = !(propId && rating >= 1 && body.length >= 10);
}

document.getElementById('pick-body').addEventListener('input', validatePickForm);

/* ── Character counter ── */
function updateCharCount(textarea, countId) {
    const el  = document.getElementById(countId);
    if (!el) return;
    const len = textarea.value.length;
    el.textContent = `${len} / 1000`;
    el.className = 'char-count' + (len > 950 ? ' over' : len > 800 ? ' near' : '');
}
</script>
@endpush
