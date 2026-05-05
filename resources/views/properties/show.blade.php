@extends('layouts.app')

@section('title', $property->title . ' - Property Detail')
@section('search-placeholder', 'Search for locations...')

@push('styles')
<style>
    .detail-layout { display:grid;grid-template-columns:1fr 340px;gap:24px; }

    .gallery { display:grid;grid-template-columns:2fr 1fr;grid-template-rows:1fr;gap:6px;border-radius:14px;overflow:hidden;margin-bottom:24px;height:600px;cursor:pointer;position:relative; }
    .gallery-main { grid-row:1/3; }
    .gallery-main img { width:100%;height:100%;object-fit:cover;transition:transform 0.3s; }
    .gallery-main:hover img { transform:scale(1.03); }
    .gallery-main { overflow:hidden; }
    .gallery-more { position:absolute;bottom:12px;right:12px;background:rgba(0,0,0,0.7);color:#fff;border-radius:8px;padding:6px 12px;font-size:0.8rem;font-weight:700;display:flex;align-items:center;gap:5px; }
    .gallery-badges { position:absolute;top:12px;left:12px;display:flex;gap:6px; }
    .gallery-badge { padding:5px 12px;border-radius:6px;font-size:0.75rem;font-weight:700; }
    .gb-popular { background:var(--orange);color:#fff; }
    .gb-verified { background:var(--teal);color:#fff; }

    .prop-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px; }
    .prop-title { font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700; }
    .prop-actions { display:flex;gap:8px; }
    .icon-action { width:38px;height:38px;border:1.5px solid var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:0.95rem;transition:all 0.2s; }
    .icon-action:hover { border-color:var(--teal);color:var(--teal); }
    .prop-rating-row { display:flex;align-items:center;gap:6px;margin-bottom:6px; }
    .stars i { color:var(--yellow);font-size:0.85rem; }
    .review-count { font-size:0.82rem;color:var(--text-muted); }
    .prop-location { font-size:0.875rem;color:var(--text-muted);display:flex;align-items:center;gap:5px;margin-bottom:20px; }
    .prop-location i { color:var(--teal); }
    .section-title { font-size:1rem;font-weight:700;margin-bottom:12px;padding-top:20px;border-top:1px solid var(--border); }
    .about-text { font-size:0.9rem;line-height:1.7;color:var(--text-muted);margin-bottom:4px; }

    .amenities-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:8px; }
    .amenity-item { display:flex;align-items:center;gap:8px;padding:10px 12px;background:var(--bg);border-radius:10px; }
    .amenity-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.8rem; }
    .ai-teal { background:#ccfbf1;color:#0d9488; }
    .ai-blue { background:#dbeafe;color:#2563eb; }
    .ai-orange { background:#fff7ed;color:var(--orange); }
    .ai-green { background:#dcfce7;color:#16a34a; }
    .ai-purple { background:#f5f3ff;color:#7c3aed; }
    .amenity-name { font-size:0.8rem;font-weight:600; }

    .rooms-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px; }
    .room-card { border:1.5px solid var(--border);border-radius:12px;padding:14px;transition:all 0.2s;cursor:pointer; }
    .room-card:hover,.room-card.selected { border-color:var(--teal);background:#f0fdfb; }
    .room-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:8px; }
    .room-name { font-size:0.9rem;font-weight:700; }
    .room-avail { font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:4px; }
    .avail-yes { background:#dcfce7;color:#16a34a; }
    .avail-pop { background:var(--orange);color:#fff; }
    .room-desc { font-size:0.78rem;color:var(--text-muted);margin-bottom:8px; }
    .room-specs { display:flex;gap:10px;font-size:0.75rem;color:var(--text-muted);margin-bottom:10px; }
    .room-specs span { display:flex;align-items:center;gap:4px; }
    .room-price-row { display:flex;align-items:center;justify-content:space-between; }
    .room-price { font-size:0.95rem;font-weight:800;color:var(--text); }

    .reviews-section { display:flex;flex-direction:column;gap:14px;margin-bottom:8px; }
    .review-item { padding:16px;background:var(--bg);border-radius:12px;border:1px solid var(--border); }
    .review-item-header { display:flex;align-items:center;gap:10px;margin-bottom:8px; }
    .reviewer-avatar { width:38px;height:38px;border-radius:50%;background:var(--teal);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.95rem;flex-shrink:0; }
    .reviewer-name { font-weight:700;font-size:0.875rem; }
    .reviewer-date { font-size:0.75rem;color:var(--text-muted); }
    .review-item-stars { margin-left:auto;display:flex;gap:2px; }
    .review-item-stars i { font-size:0.78rem; }
    .review-item-body { font-size:0.875rem;line-height:1.6;color:var(--text); }
    .no-reviews { font-size:0.875rem;color:var(--text-muted);padding:12px 0;margin-bottom:8px; }

    #propertyMap { height:200px;border-radius:10px;overflow:hidden; }
    .map-nearby { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px; }
    .nearby-item { display:flex;align-items:center;gap:8px;background:var(--bg);border-radius:8px;padding:8px 10px; }
    .nearby-icon { font-size:0.9rem; }
    .nearby-info { font-size:0.8rem; }
    .nearby-name { font-weight:600; }
    .nearby-dist { color:var(--text-muted); }

    .booking-sidebar { position:sticky;top:80px; }
    .booking-card { background:var(--card);border-radius:14px;border:1px solid var(--border);padding:22px; }
    .price-from { font-size:0.75rem;color:var(--text-muted);margin-bottom:2px; }
    .price-main { font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:700;margin-bottom:2px; }
    .price-period { font-size:0.82rem;color:var(--text-muted); }
    .instant-badge { display:inline-flex;align-items:center;gap:4px;background:#fff7ed;color:var(--orange);font-size:0.72rem;font-weight:700;padding:3px 8px;border-radius:5px;margin-left:8px; }
    .form-row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
    .book-label { font-size:0.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;margin-bottom:6px; }
    .book-select { width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;outline:none;appearance:none;background:var(--bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none'/%3E%3C/svg%3E") no-repeat right 10px center;cursor:pointer; }
    .book-select:focus { border-color:var(--teal); }
    .book-input { width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;outline:none;background:var(--bg); }
    .book-input:focus { border-color:var(--teal); }
    .price-breakdown { border:1px solid var(--border);border-radius:10px;padding:14px;margin:14px 0; }
    .pb-row { display:flex;justify-content:space-between;font-size:0.875rem;padding:4px 0; }
    .pb-row.total { border-top:1px solid var(--border);margin-top:8px;padding-top:10px;font-weight:800;font-size:0.95rem; }
    .reserve-btn { width:100%;padding:14px;background:var(--orange);color:#fff;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;transition:background 0.2s;letter-spacing:0.3px; }
    .reserve-btn:hover { background:#d4581f; }
    .guarantee-note { font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:10px; }
</style>
@endpush

@section('content')

@php
    $image = $property->image
        ? Storage::url($property->image)
        : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&q=80';

    $amenityMap = [
        'WiFi Included'      => ['icon'=>'fas fa-wifi',       'label'=>'High-speed WiFi',    'class'=>'ai-teal'],
        'Air Conditioning'   => ['icon'=>'fas fa-snowflake',  'label'=>'Air Conditioning',   'class'=>'ai-blue'],
        '24/7 Security'      => ['icon'=>'fas fa-shield-alt', 'label'=>'24/7 Security',      'class'=>'ai-orange'],
        'Unlimited Water'    => ['icon'=>'fas fa-tint',       'label'=>'Unlimited Water',    'class'=>'ai-green'],
        'Shared Laundry'     => ['icon'=>'fas fa-tshirt',     'label'=>'Shared Laundry',     'class'=>'ai-purple'],
        'Electricity Backup' => ['icon'=>'fas fa-bolt',       'label'=>'Electricity Backup', 'class'=>'ai-orange'],
        'Parking'            => ['icon'=>'fas fa-car',        'label'=>'Parking',            'class'=>'ai-blue'],
        'CCTV'               => ['icon'=>'fas fa-video',      'label'=>'CCTV',               'class'=>'ai-orange'],
        'Shared Kitchen'     => ['icon'=>'fas fa-utensils',   'label'=>'Shared Kitchen',     'class'=>'ai-green'],
        'Gym'                => ['icon'=>'fas fa-dumbbell',   'label'=>'Gym',                'class'=>'ai-purple'],
        'Elevator'           => ['icon'=>'fas fa-elevator',   'label'=>'Elevator',           'class'=>'ai-blue'],
        'Pet Friendly'       => ['icon'=>'fas fa-paw',        'label'=>'Pet Friendly',       'class'=>'ai-green'],
    ];

    $storedAmenities = $property->amenities
        ? array_filter(array_map('trim', explode(',', $property->amenities)))
        : [];

    $displayAmenities = [];
    foreach ($storedAmenities as $key) {
        $k = trim($key);
        if (isset($amenityMap[$k])) {
            $displayAmenities[] = $amenityMap[$k];
        } else {
            $displayAmenities[] = ['icon'=>'fas fa-check-circle','label'=>ucfirst($key),'class'=>'ai-teal'];
        }
    }

    if (empty($displayAmenities)) {
        $displayAmenities = array_values($amenityMap);
    }

    $extraPhotos = $property->photos
        ? json_decode($property->photos, true)
        : [];
    $extraPhotos  = is_array($extraPhotos) ? $extraPhotos : [];
    $totalExtras  = count($extraPhotos);

    $lat   = $property->latitude  ?? 7.4460;
    $lng   = $property->longitude ?? 125.8050;
    $price = number_format($property->price, 0);
@endphp

{{-- ═══════════════════════════════════════════
     GALLERY
     Left  : main profile photo (tall, full height)
     Right : up to 4 additional photos stacked
════════════════════════════════════════════ --}}
<div style="position:relative;margin-bottom:24px;">

    <div class="gallery" style="display:grid;grid-template-columns:2fr 1fr;grid-template-rows:1fr;gap:6px;height:600px;border-radius:14px;overflow:hidden;">

        {{-- Main photo --}}
        <div style="overflow:hidden;height:600px;">
            <img src="{{ $image }}" alt="{{ $property->title }}"
                 style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;"
                 onmouseover="this.style.transform='scale(1.03)'"
                 onmouseout="this.style.transform='scale(1)'">
        </div>

        {{-- Right column: 4 stacked slots --}}
        <div style="display:grid;grid-template-rows:repeat(4,1fr);gap:6px;height:600px;">
            @for($i = 0; $i < 4; $i++)
            @php
                $photoUrl  = isset($extraPhotos[$i]) ? Storage::url($extraPhotos[$i]) : null;
                $isLast    = ($i === 3);
                $remaining = $totalExtras - 4;
            @endphp

            @if($photoUrl)
                <div style="position:relative;overflow:hidden;">
                    <img src="{{ $photoUrl }}" alt="Photo {{ $i + 1 }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;"
                         onmouseover="this.style.transform='scale(1.03)'"
                         onmouseout="this.style.transform='scale(1)'">
                    @if($isLast && $remaining > 0)
                    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.55);display:flex;align-items:center;justify-content:center;pointer-events:none;">
                        <span style="color:#fff;font-size:1.1rem;font-weight:700;">+{{ $remaining }} more</span>
                    </div>
                    @endif
                </div>
            @elseif($i < 2)
                {{-- First 2 empty slots: show a dim placeholder --}}
                <div style="overflow:hidden;background:#e2e8f0;">
                    <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=400&q=80"
                         alt="No photo"
                         style="width:100%;height:100%;object-fit:cover;opacity:0.35;">
                </div>
            @else
                {{-- Slots 3 & 4 when empty: subtle blank --}}
                <div style="background:var(--bg);border:1px dashed var(--border);display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:0.72rem;color:var(--text-muted);">No photo</span>
                </div>
            @endif
            @endfor
        </div>

    </div>

    {{-- Badges top-left --}}
    <div class="gallery-badges">
        <span class="gallery-badge gb-popular">POPULAR CHOICE</span>
        <span class="gallery-badge gb-verified">VERIFIED</span>
    </div>

    {{-- Label bottom-right --}}
    <div class="gallery-more">
        <i class="fas fa-images"></i> Official Pictures
    </div>

</div>
{{-- END GALLERY --}}

<div class="detail-layout">

    {{-- ═══ LEFT COLUMN ═══ --}}
    <div>
        <div class="prop-header">
            <div>
                <h1 class="prop-title">{{ $property->title }}</h1>

                <div class="prop-rating-row">
                    <div class="stars">
                        @php
                            $r    = $avgRating ?? 0;
                            $full = floor($r);
                            $half = ($r - $full) >= 0.5;
                        @endphp
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
                    <span style="font-weight:700;font-size:0.875rem;">
                        {{ $avgRating ?? 'No ratings' }}
                    </span>
                    <span class="review-count">
                        ({{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }})
                    </span>
                </div>

                <div class="prop-location">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $property->address }}
                </div>
            </div>
            <div class="prop-actions">
                <button class="icon-action"
                        id="favBtn"
                        onclick="toggleFavorite({{ $property->id }})"
                        title="{{ $isFavorited ? 'Remove from favorites' : 'Save to favorites' }}"
                        style="{{ $isFavorited ? 'color:var(--red);border-color:var(--red);' : '' }}">
                    <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
                </button>
                <button class="icon-action"><i class="fas fa-share-alt"></i></button>
            </div>
        </div>

        {{-- About --}}
        <div class="section-title">About this property</div>
        <p class="about-text">{{ $property->description }}</p>

        {{-- Amenities --}}
        <div class="section-title">What this place offers</div>
        <div class="amenities-grid">
            @foreach($displayAmenities as $a)
            <div class="amenity-item">
                <div class="amenity-icon {{ $a['class'] }}"><i class="{{ $a['icon'] }}"></i></div>
                <span class="amenity-name">{{ $a['label'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Available Rooms --}}
        <div class="section-title">Available Rooms</div>
        <div class="rooms-grid">
            @php
                $roomTypes = [
    'single'    => ['label'=>'Single Occupancy', 'desc'=>'Private unit, one occupant',    'size'=>'12 m²','icon'=>'fas fa-bed',  'badge'=>'AVAILABLE','badgeClass'=>'avail-yes','multiplier'=>1.0],
    'double'    => ['label'=>'Double Occupancy', 'desc'=>'Shared room, two separate beds','size'=>'19 m²','icon'=>'fas fa-users','badge'=>'POPULAR', 'badgeClass'=>'avail-pop','multiplier'=>0.85],
    'triple'    => ['label'=>'Triple Occupancy', 'desc'=>'Shared room, three occupants',  'size'=>'25 m²','icon'=>'fas fa-users','badge'=>'AVAILABLE','badgeClass'=>'avail-yes','multiplier'=>0.75],
    'dormitory' => ['label'=>'Dormitory',        'desc'=>'Shared dorm-style room',        'size'=>'30 m²','icon'=>'fas fa-bed',  'badge'=>'AVAILABLE','badgeClass'=>'avail-yes','multiplier'=>0.65],
];
                $currentType = strtolower($property->room_type ?? 'double');
                $showTypes   = isset($roomTypes[$currentType])
                    ? [$currentType => $roomTypes[$currentType]]
                    : array_slice($roomTypes, 0, 2, true);
                if (count($showTypes) === 1) {
                    $others    = array_diff_key($roomTypes, $showTypes);
                    $showTypes = $showTypes + array_slice($others, 0, 1, true);
                }
            @endphp

            @foreach($showTypes as $typeKey => $room)
            @php $roomPrice = number_format($property->price * $room['multiplier'], 0); @endphp
            <div class="room-card {{ $loop->first ? 'selected' : '' }}"
                 onclick="selectRoom(this, '{{ $typeKey }}', {{ (int)($property->price * $room['multiplier']) }})">
                <div class="room-header">
                    <span class="room-name">{{ $room['label'] }}</span>
                    <span class="room-avail {{ $room['badgeClass'] }}">{{ $room['badge'] }}</span>
                </div>
                <div class="room-desc">{{ $room['desc'] }}</div>
                <div class="room-specs">
                    <span><i class="fas fa-ruler-combined"></i> {{ $room['size'] }}</span>
                    <span><i class="{{ $room['icon'] }}"></i></span>
                </div>
                <div class="room-price-row">
                    <span class="room-price">₱{{ $roomPrice }}/mo</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Reviews --}}
        <div class="section-title">
            Reviews
            @if($reviews->count())
                <span style="font-size:0.82rem;font-weight:500;color:var(--text-muted);margin-left:8px;">
                    {{ $avgRating }} ★ · {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
                </span>
            @endif
        </div>

        @if($reviews->count())
            <div class="reviews-section">
                @foreach($reviews as $review)
                <div class="review-item">
                    <div class="review-item-header">
                        <div class="reviewer-avatar">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="reviewer-name">{{ $review->user->name }}</div>
                            <div class="reviewer-date">{{ $review->created_at->format('M Y') }}</div>
                        </div>
                        <div class="review-item-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color:{{ $i <= $review->rating ? 'var(--yellow)' : 'var(--border)' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="review-item-body">{{ $review->body }}</div>
                </div>
                @endforeach
            </div>
        @else
            <div class="no-reviews">
                No reviews yet. Be the first to review this boarding house!
            </div>
        @endif

        {{-- Map --}}
        <div class="section-title">Location & Surroundings</div>
        <div id="propertyMap"></div>
        <div class="map-nearby">
            <div class="nearby-item">
                <span class="nearby-icon">🎓</span>
                <div class="nearby-info">
                    <div class="nearby-name">Nearby School</div>
                    <div class="nearby-dist">Within walking distance</div>
                </div>
            </div>
            <div class="nearby-item">
                <span class="nearby-icon">🍽️</span>
                <div class="nearby-info">
                    <div class="nearby-name">Restaurants</div>
                    <div class="nearby-dist">Nearby dining options</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ BOOKING SIDEBAR ═══ --}}
    <div class="booking-sidebar">
        <div class="booking-card">
            <div style="display:flex;align-items:baseline;gap:6px;margin-bottom:16px;">
                <div>
                    <div class="price-from">Starting from</div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <span class="price-main" id="sidebarPrice">₱{{ $price }}</span>
                        <span class="price-period">/mo</span>
                        <span class="instant-badge"><i class="fas fa-bolt"></i> INSTANT BOOK</span>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:12px;">
                <div class="book-label">Room Type</div>
                <select class="book-select" id="roomTypeSelect" onchange="updatePrice()">
                    @foreach($showTypes as $typeKey => $room)
                    <option value="{{ (int)($property->price * $room['multiplier']) }}" data-type="{{ $typeKey }}">
                        {{ $room['label'] }} (₱{{ number_format($property->price * $room['multiplier'], 0) }}/mo)
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row" style="margin-bottom:12px;">
                <div>
                    <div class="book-label">Move-in Date</div>
                    <input type="date" class="book-input" id="moveInDate"
                           value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           onchange="updatePrice()">
                </div>
                <div>
                    <div class="book-label">Duration</div>
                    <select class="book-select" id="durationSelect" onchange="updatePrice()">
                        <option value="1">1 Month</option>
                        <option value="3">3 Months</option>
                        <option value="6" selected>6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                </div>
            </div>

            <div class="price-breakdown">
                <div class="pb-row">
                    <span>Monthly Rent</span>
                    <span id="pbMonthly">₱{{ $price }}.00</span>
                </div>
                <div class="pb-row">
                    <span>Security Deposit (1mo)</span>
                    <span id="pbDeposit">₱{{ $price }}.00</span>
                </div>
                <div class="pb-row total">
                    <span>Total Initial Pay</span>
                    <span id="pbTotal" style="color:var(--orange)">
                        ₱{{ number_format($property->price * 2, 0) }}.00
                    </span>
                </div>
            </div>

            <div style="background:#f0fdfb;border:1px solid #99f6e4;border-radius:8px;padding:10px 12px;margin-bottom:14px;">
                <div style="font-size:0.75rem;color:#0d9488;font-weight:700;margin-bottom:3px;">
                    📅 Projected Move-out Date
                </div>
                <div style="font-size:0.875rem;font-weight:700;" id="moveOutPreview">Calculating...</div>
                <div style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;" id="moveOutCountdown">...</div>
            </div>

            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="room_type"    id="hiddenRoomType" value="{{ $currentType }}">
                <input type="hidden" name="monthly_rate" id="hiddenRate"     value="{{ $property->price }}">
                <input type="hidden" name="check_in"     id="hiddenCheckIn"  value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                <input type="hidden" name="duration"     id="hiddenDuration" value="6">
                <button type="submit" class="reserve-btn">RESERVE NOW</button>
            </form>

            <p class="guarantee-note">
                You won't be charged yet. Our admin will contact you to verify your application within 24 hours.
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const propLat     = {{ $lat }};
const propLng     = {{ $lng }};
const propTitle   = @json($property->title);
const propAddress = @json($property->address);

const pmap = L.map('propertyMap').setView([propLat, propLng], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(pmap);

const propIcon = L.divIcon({
    html: `<div style="background:var(--orange,#e8692a);color:#fff;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:700;box-shadow:0 3px 10px rgba(0,0,0,0.25)">📍 ${propTitle}</div>`,
    className: '',
    iconAnchor: [60, 28]
});
L.marker([propLat, propLng], { icon: propIcon })
    .addTo(pmap)
    .bindPopup(`<strong>${propTitle}</strong><br>${propAddress}`)
    .openPopup();
L.circle([propLat, propLng], {
    radius: 500,
    color: 'var(--teal,#2ec4a5)',
    fillOpacity: 0.05,
    weight: 1.5
}).addTo(pmap);

function toggleFavorite(propertyId) {
    const btn  = document.getElementById('favBtn');
    const icon = btn.querySelector('i');
    fetch(`/favorites/${propertyId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.favorited) {
            icon.className        = 'fas fa-heart';
            btn.style.color       = 'var(--red)';
            btn.style.borderColor = 'var(--red)';
            btn.title             = 'Remove from favorites';
        } else {
            icon.className        = 'far fa-heart';
            btn.style.color       = '';
            btn.style.borderColor = '';
            btn.title             = 'Save to favorites';
        }
    });
}

function updatePrice() {
    const rate     = parseInt(document.getElementById('roomTypeSelect').value);
    const duration = parseInt(document.getElementById('durationSelect').value);
    const moveIn   = document.getElementById('moveInDate').value;

    document.getElementById('sidebarPrice').textContent = '₱' + rate.toLocaleString();
    document.getElementById('pbMonthly').textContent    = '₱' + rate.toLocaleString() + '.00';
    document.getElementById('pbDeposit').textContent    = '₱' + rate.toLocaleString() + '.00';
    document.getElementById('pbTotal').textContent      = '₱' + (rate * 2).toLocaleString() + '.00';
    document.getElementById('hiddenRate').value         = rate;
    document.getElementById('hiddenDuration').value     = duration;

    if (moveIn) {
        document.getElementById('hiddenCheckIn').value = moveIn;
        const mo   = new Date(moveIn);
        mo.setMonth(mo.getMonth() + duration);
        const opts = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('moveOutPreview').textContent =
            mo.toLocaleDateString('en-US', opts);

        function tickMoveOut() {
            const diff = mo - new Date();
            if (diff <= 0) {
                document.getElementById('moveOutCountdown').textContent = 'Move-out date reached';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            document.getElementById('moveOutCountdown').textContent =
                `⏱ ${d}d ${h}h ${m}m ${s}s remaining`;
        }
        tickMoveOut();
        if (window._moveOutTimer) clearInterval(window._moveOutTimer);
        window._moveOutTimer = setInterval(tickMoveOut, 1000);
    }

    const sel = document.getElementById('roomTypeSelect');
    document.getElementById('hiddenRoomType').value =
        sel.options[sel.selectedIndex].dataset.type;
}

function selectRoom(el, type, price) {
    document.querySelectorAll('.room-card').forEach(r => r.classList.remove('selected'));
    el.classList.add('selected');
    const sel = document.getElementById('roomTypeSelect');
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].dataset.type === type) {
            sel.selectedIndex = i;
            break;
        }
    }
    updatePrice();
}

updatePrice();
</script>
@endpush