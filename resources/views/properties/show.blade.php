@extends('layouts.app')

@section('title', $property->title . ' - Property Detail')
@section('search-placeholder', 'Search for locations...')

@push('styles')
<style>
    .detail-layout { display:grid;grid-template-columns:1fr 340px;gap:24px; }

    .gallery { display:grid;grid-template-columns:2fr 1fr;gap:8px;border-radius:18px;overflow:hidden;margin-bottom:24px;height:560px;position:relative;background:var(--glass-card);border:1px solid var(--glass-border);box-shadow:var(--glass-shadow); }
    .gallery-main,.gallery-side-item { position:relative;overflow:hidden;border:0;background:var(--bg);padding:0;cursor:pointer; }
    .gallery-main img,.gallery-side-item img { width:100%;height:100%;object-fit:cover;transition:transform 0.3s, filter 0.3s;display:block; }
    .gallery-main:hover img,.gallery-side-item:hover img { transform:scale(1.035);filter:brightness(0.94); }
    .gallery-side { display:grid;grid-template-rows:1fr 1fr;gap:8px;min-height:0; }
    .gallery-more-overlay { position:absolute;inset:0;background:linear-gradient(135deg,rgba(8,47,73,0.72),rgba(6,182,212,0.38));color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;font-weight:900;text-align:center;backdrop-filter:blur(2px); }
    .gallery-more-overlay i { font-size:1.45rem; }
    .gallery-more { position:absolute;bottom:14px;right:14px;background:rgba(7,24,38,0.76);color:#fff;border:1px solid rgba(255,255,255,0.25);border-radius:999px;padding:8px 13px;font-size:0.8rem;font-weight:800;display:flex;align-items:center;gap:6px;backdrop-filter:blur(10px); }
    .gallery-badges { position:absolute;top:12px;left:12px;display:flex;gap:6px; }
    .gallery-badge { padding:5px 12px;border-radius:6px;font-size:0.75rem;font-weight:700; }
    .gb-popular { background:var(--orange);color:#fff; }
    .gb-verified { background:var(--teal);color:#fff; }
    .verified-inline { display:inline-flex;align-items:center;gap:5px;border:1px solid rgba(6,182,212,0.28);background:rgba(236,254,255,0.92);color:#0369a1;border-radius:999px;padding:5px 10px;font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.3px; }
    .trust-strip { display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:16px 0 4px; }
    .trust-chip { background:var(--glass-card);border:1px solid var(--glass-border);border-radius:14px;padding:12px;display:flex;align-items:center;gap:10px;box-shadow:0 12px 30px rgba(14,116,144,0.08);backdrop-filter:blur(14px); }
    .trust-icon { width:34px;height:34px;border-radius:11px;display:grid;place-items:center;background:rgba(6,182,212,0.13);color:#0284c7;flex-shrink:0; }
    .trust-title { font-size:0.8rem;font-weight:900;color:var(--text); }
    .trust-sub { font-size:0.72rem;color:var(--text-muted);margin-top:2px;line-height:1.25; }

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
    .property-map-marker { position:relative;background:linear-gradient(135deg,#0ea5e9,#06b6d4);color:#fff;border:2px solid rgba(255,255,255,0.94);border-radius:999px;padding:7px 11px;font:900 12px 'DM Sans',sans-serif;box-shadow:0 14px 30px rgba(14,165,233,0.32);white-space:nowrap;transform:translate(-50%,-50%); }
    .property-map-marker::after { content:'';position:absolute;left:50%;bottom:-7px;width:10px;height:10px;background:#06b6d4;border-right:2px solid rgba(255,255,255,0.94);border-bottom:2px solid rgba(255,255,255,0.94);transform:translateX(-50%) rotate(45deg);border-radius:2px; }
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
    .photo-lightbox { position:fixed;inset:0;background:rgba(3,10,18,0.94);z-index:99999;display:none;align-items:center;justify-content:center;padding:28px; }
    .photo-lightbox.active { display:flex; }
    .photo-lightbox-img { max-width:min(1120px,92vw);max-height:82vh;border-radius:16px;object-fit:contain;box-shadow:0 24px 80px rgba(0,0,0,0.55); }
    .photo-lightbox-close,.photo-lightbox-nav { position:absolute;border:1px solid rgba(255,255,255,0.18);background:rgba(255,255,255,0.10);color:#fff;border-radius:999px;display:grid;place-items:center;cursor:pointer;backdrop-filter:blur(12px);transition:background 0.2s,transform 0.2s; }
    .photo-lightbox-close:hover,.photo-lightbox-nav:hover { background:rgba(255,255,255,0.18);transform:scale(1.04); }
    .photo-lightbox-close { top:22px;right:24px;width:44px;height:44px;font-size:1rem; }
    .photo-lightbox-nav { top:50%;width:46px;height:46px;transform:translateY(-50%);font-size:1rem; }
    .photo-lightbox-nav:hover { transform:translateY(-50%) scale(1.04); }
    .photo-lightbox-prev { left:26px; }
    .photo-lightbox-next { right:26px; }
    .photo-lightbox-count { position:absolute;left:50%;bottom:24px;transform:translateX(-50%);color:#fff;background:rgba(255,255,255,0.11);border:1px solid rgba(255,255,255,0.16);border-radius:999px;padding:8px 14px;font-size:0.82rem;font-weight:800;backdrop-filter:blur(12px); }
    @media (max-width: 900px) {
        .trust-strip { grid-template-columns:1fr; }
        .gallery { grid-template-columns:1fr;height:auto; }
        .gallery-main { height:360px; }
        .gallery-side { grid-template-columns:1fr 1fr;grid-template-rows:180px; }
        .photo-lightbox { padding:18px; }
        .photo-lightbox-nav { width:40px;height:40px; }
    }
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
    $galleryPhotos = array_values(array_filter(array_merge(
        [$image],
        array_map(fn ($photo) => Storage::url($photo), $extraPhotos)
    )));
    $previewPhotos = array_slice($galleryPhotos, 0, 3);
    $hiddenPhotoCount = max(count($galleryPhotos) - 3, 0);

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
    <div class="gallery">
        <button type="button" class="gallery-main" onclick="openPhotoLightbox(0)" aria-label="View {{ $property->title }} photo 1">
            <img src="{{ $previewPhotos[0] ?? $image }}" alt="{{ $property->title }}">
        </button>

        <div class="gallery-side">
            @for($i = 1; $i <= 2; $i++)
                @php $photoUrl = $previewPhotos[$i] ?? null; @endphp
                @if($photoUrl)
                    <button type="button" class="gallery-side-item" onclick="openPhotoLightbox({{ $i }})" aria-label="View {{ $property->title }} photo {{ $i + 1 }}">
                        <img src="{{ $photoUrl }}" alt="{{ $property->title }} photo {{ $i + 1 }}">
                        @if($i === 2 && $hiddenPhotoCount > 0)
                            <span class="gallery-more-overlay">
                                <i class="fas fa-images"></i>
                                <span>+{{ $hiddenPhotoCount }} more photos</span>
                            </span>
                        @endif
                    </button>
                @else
                    <button type="button" class="gallery-side-item" onclick="openPhotoLightbox(0)" aria-label="View {{ $property->title }} photo">
                        <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=500&q=80" alt="Room preview" style="opacity:0.38;">
                    </button>
                @endif
            @endfor
        </div>
    </div>

    <div class="gallery-badges">
        <span class="gallery-badge gb-popular">POPULAR CHOICE</span>
        @if($property->is_approved)
        <span class="gallery-badge gb-verified">VERIFIED</span>
        @endif
    </div>

    <button type="button" class="gallery-more" onclick="openPhotoLightbox(0)">
        <i class="fas fa-images"></i> View all {{ count($galleryPhotos) }} photos
    </button>
</div>

<div class="photo-lightbox" id="photoLightbox" aria-hidden="true">
    <button type="button" class="photo-lightbox-close" onclick="closePhotoLightbox()" aria-label="Close photo viewer"><i class="fas fa-times"></i></button>
    <button type="button" class="photo-lightbox-nav photo-lightbox-prev" onclick="movePhoto(-1)" aria-label="Previous photo"><i class="fas fa-chevron-left"></i></button>
    <img src="" alt="Property photo" class="photo-lightbox-img" id="photoLightboxImg">
    <button type="button" class="photo-lightbox-nav photo-lightbox-next" onclick="movePhoto(1)" aria-label="Next photo"><i class="fas fa-chevron-right"></i></button>
    <div class="photo-lightbox-count" id="photoLightboxCount"></div>
</div>

<div class="detail-layout">

    {{-- ═══ LEFT COLUMN ═══ --}}
    <div>
        <div class="prop-header">
            <div>
                <h1 class="prop-title">{{ $property->title }}</h1>
                @if($property->is_approved)
                    <div style="margin:8px 0 10px;">
                        <span class="verified-inline"><i class="fas fa-shield-alt"></i> Admin Verified</span>
                    </div>
                @endif

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

                <div class="trust-strip">
                    <div class="trust-chip">
                        <div class="trust-icon"><i class="fas fa-user-shield"></i></div>
                        <div>
                            <div class="trust-title">Verified listing</div>
                            <div class="trust-sub">Reviewed by BoardEase admin</div>
                        </div>
                    </div>
                    <div class="trust-chip">
                        <div class="trust-icon"><i class="fas fa-message"></i></div>
                        <div>
                            <div class="trust-title">Direct messaging</div>
                            <div class="trust-sub">Ask owner before reserving</div>
                        </div>
                    </div>
                    <div class="trust-chip">
                        <div class="trust-icon"><i class="fas fa-map-location-dot"></i></div>
                        <div>
                            <div class="trust-title">Mapped location</div>
                            <div class="trust-sub">Check nearby access points</div>
                        </div>
                    </div>
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
const galleryPhotos = @json($galleryPhotos);
let currentGalleryIndex = 0;

function renderPhotoLightbox() {
    const image = document.getElementById('photoLightboxImg');
    const count = document.getElementById('photoLightboxCount');
    if (!image || !count || !galleryPhotos.length) return;

    image.src = galleryPhotos[currentGalleryIndex];
    count.textContent = `${currentGalleryIndex + 1} / ${galleryPhotos.length}`;
}

function openPhotoLightbox(index = 0) {
    if (!galleryPhotos.length) return;

    const lightbox = document.getElementById('photoLightbox');
    if (lightbox && lightbox.parentElement !== document.body) {
        document.body.appendChild(lightbox);
    }

    currentGalleryIndex = Math.max(0, Math.min(index, galleryPhotos.length - 1));
    renderPhotoLightbox();
    lightbox?.classList.add('active');
    lightbox?.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closePhotoLightbox() {
    document.getElementById('photoLightbox')?.classList.remove('active');
    document.getElementById('photoLightbox')?.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function movePhoto(direction) {
    if (!galleryPhotos.length) return;

    currentGalleryIndex = (currentGalleryIndex + direction + galleryPhotos.length) % galleryPhotos.length;
    renderPhotoLightbox();
}

document.getElementById('photoLightbox')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closePhotoLightbox();
    }
});

document.addEventListener('keydown', function(event) {
    const lightbox = document.getElementById('photoLightbox');
    if (!lightbox?.classList.contains('active')) return;

    if (event.key === 'Escape') closePhotoLightbox();
    if (event.key === 'ArrowLeft') movePhoto(-1);
    if (event.key === 'ArrowRight') movePhoto(1);
});

const propLat     = {{ $lat }};
const propLng     = {{ $lng }};
const propTitle   = @json($property->title);
const propAddress = @json($property->address);

const pmap = L.map('propertyMap').setView([propLat, propLng], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(pmap);

const propIcon = L.divIcon({
    html: `<div class="property-map-marker">&#8369;${@json($price)}</div>`,
    className: '',
    iconSize: [1, 1],
    iconAnchor: [0, 0]
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

setTimeout(() => pmap.invalidateSize(), 120);
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
