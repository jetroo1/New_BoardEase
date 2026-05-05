@extends('layouts.app')

@section('title', 'Search')
@section('search-placeholder', 'Search for locations...')

@push('styles')
<style>
    .search-layout { display:grid;grid-template-columns:280px 1fr;gap:20px;height:calc(100vh - 120px); }
    .filter-panel { background:var(--card);border-radius:14px;border:1px solid var(--border);padding:20px;overflow-y:auto;height:100%; }
    .filter-title { font-size:1rem;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:8px; }
    .filter-group { margin-bottom:22px; }
    .filter-label { font-size:0.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;display:block; }
    .price-range-display { display:flex;justify-content:space-between;font-size:0.82rem;color:var(--text-muted);margin-bottom:8px; }
    .range-slider { width:100%;appearance:none;height:4px;border-radius:4px;background:linear-gradient(to right,var(--teal) 0%,var(--teal) 20%,var(--border) 20%);outline:none;cursor:pointer; }
    .range-slider::-webkit-slider-thumb { appearance:none;width:16px;height:16px;border-radius:50%;background:var(--teal);border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.15);cursor:pointer; }
    .price-input-wrap { display:flex;align-items:center;gap:8px;margin-top:10px; }
    .price-input-wrap label { font-size:0.78rem;font-weight:700;color:var(--text-muted);white-space:nowrap; }
    .price-input { width:100%;padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;outline:none;background:var(--bg);color:var(--text);transition:border-color 0.2s; }
    .price-input:focus { border-color:var(--teal); }
    .filter-option { display:flex;align-items:center;gap:10px;padding:7px 0;cursor:pointer; }
    .filter-option input[type=checkbox], .filter-option input[type=radio] { display:none; }
    .custom-check { width:18px;height:18px;border:2px solid var(--border);border-radius:5px;display:flex;align-items:center;justify-content:center;transition:all 0.15s;flex-shrink:0; }
    .custom-radio { border-radius:50%; }
    .filter-option input:checked + .custom-check { background:var(--teal);border-color:var(--teal); }
    .filter-option input:checked + .custom-check::after { content:'✔';color:#fff;font-size:0.7rem;font-weight:700; }
    .filter-option input:checked + .custom-radio::after { content:'';width:7px;height:7px;background:#fff;border-radius:50%;display:block; }
    .filter-option span { font-size:0.875rem; }
    .star-filters { display:flex;gap:6px; }
    .star-btn { width:36px;height:36px;border:1.5px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:0.75rem;font-weight:700;transition:all 0.15s;background:transparent; }
    .star-btn.active { background:var(--navy);border-color:var(--navy);color:#fff; }
    .star-btn:hover:not(.active) { border-color:var(--teal); }
    .promo-card { background:var(--navy);border-radius:12px;padding:16px;margin-top:4px;overflow:hidden;position:relative; }
    .promo-card::before { content:'';position:absolute;right:-20px;top:-20px;width:100px;height:100px;background:rgba(46,196,165,0.1);border-radius:50%; }
    .promo-card img { width:100%;border-radius:8px;height:100px;object-fit:cover;margin-bottom:12px; }
    .promo-name { font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:4px; }
    .promo-price { font-size:0.8rem;color:var(--teal); }
    .promo-badge { display:inline-block;background:var(--green);color:#fff;font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:4px;margin-bottom:8px; }
    .results-panel { display:flex;flex-direction:column;gap:0;overflow:hidden; }
    .results-header { margin-bottom:18px; }
    .results-header h2 { font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700; }
    .results-sub { font-size:0.85rem;color:var(--text-muted);margin-top:4px; }
    .results-sub span { color:var(--orange);font-weight:700; }
    .results-controls { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
    .breadcrumb { font-size:0.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px; }
    .breadcrumb a { color:var(--text-muted);text-decoration:none; }
    .breadcrumb .sep { color:var(--teal); }
    .breadcrumb .current { color:var(--text);font-weight:600; }
    .sort-select { border:1.5px solid var(--border);border-radius:8px;padding:7px 12px;font-family:'DM Sans',sans-serif;font-size:0.85rem;outline:none;cursor:pointer;background:var(--card); }
    .props-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px;overflow-y:auto;flex:1;padding-right:4px; }
    .prop-card { background:var(--card);border-radius:14px;border:1px solid var(--border);overflow:hidden;transition:all 0.2s;cursor:pointer; }
    .prop-card:hover { box-shadow:0 8px 28px rgba(0,0,0,0.1);transform:translateY(-2px); }
    .prop-img { position:relative;height:160px;overflow:hidden; }
    .prop-img img { width:100%;height:100%;object-fit:cover;transition:transform 0.3s; }
    .prop-card:hover .prop-img img { transform:scale(1.04); }
    .prop-tag { position:absolute;top:10px;left:10px;padding:4px 10px;border-radius:6px;font-size:0.72rem;font-weight:700;text-transform:uppercase; }
    .tag-popular { background:var(--orange);color:#fff; }
    .tag-new { background:var(--teal);color:#fff; }
    .tag-best { background:var(--purple);color:#fff; }
    .save-btn { position:absolute;top:10px;right:10px;width:30px;height:30px;background:rgba(255,255,255,0.9);border-radius:50%;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;font-size:0.85rem;transition:all 0.2s; }
    .save-btn.saved { color:var(--red); }
    .save-btn:not(.saved) { color:var(--text-muted); }
    .prop-body { padding:14px; }
    .prop-name { font-size:0.95rem;font-weight:700;margin-bottom:5px; }
    .prop-dist { font-size:0.78rem;color:var(--text-muted);display:flex;align-items:center;gap:4px;margin-bottom:8px; }
    .prop-dist i { color:var(--teal); }
    .prop-tags-row { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px; }
    .prop-tag-pill { background:var(--bg);border:1px solid var(--border);border-radius:5px;padding:3px 8px;font-size:0.72rem;color:var(--text-muted);font-weight:500; }
    .prop-footer { display:flex;align-items:center;justify-content:space-between; }
    .prop-price-label { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:600; }
    .prop-price-val { font-size:1.05rem;font-weight:800;color:var(--text); }
    .prop-rating { font-size:0.8rem;display:flex;align-items:center;gap:3px; }
    .prop-rating i { color:var(--yellow); }
    .pagination-wrap { display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid var(--border); }
    .pagination { display:flex;align-items:center;gap:4px; }
    .pg-btn { width:34px;height:34px;border:1.5px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:0.85rem;font-weight:600;background:transparent;color:var(--text);transition:all 0.15s; }
    .pg-btn.active { background:var(--navy);border-color:var(--navy);color:#fff; }
    .pg-btn:hover:not(.active) { border-color:var(--teal); }
    .map-toggle-btn { background:var(--navy);color:#fff;border:none;border-radius:9px;padding:9px 16px;font-family:'DM Sans',sans-serif;font-size:0.85rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:7px;transition:background 0.2s; }
    .apply-btn { width:100%;padding:12px;background:var(--orange);color:#fff;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:0.9rem;font-weight:700;cursor:pointer;transition:background 0.2s; }
    .apply-btn:hover { background:#d4581f; }
    .empty-state { text-align:center;padding:60px 20px;color:var(--text-muted);grid-column:span 2; }
    .empty-state i { font-size:3rem;margin-bottom:12px;opacity:0.3;display:block; }
</style>
@endpush

@section('content')

@php
    $favoritedIds = auth()->check()
        ? \App\Models\Favorite::where('user_id', auth()->id())->pluck('property_id')->toArray()
        : [];
@endphp

<div class="search-layout">

    <!-- Filter Panel -->
    <div class="filter-panel">
        <div class="filter-title">
            <i class="fas fa-sliders-h" style="color:var(--teal)"></i> Filters
        </div>

        <form method="GET" action="{{ route('search') }}" id="filterForm">

            <!-- Price Range -->
            <div class="filter-group">
                <label class="filter-label">Price Range (₱)</label>
                <div class="price-range-display">
                    <span>₱1,000</span>
                    <span id="priceMax">₱{{ number_format(request('max_price', 15000)) }}</span>
                </div>
                <input type="range" class="range-slider" min="1000" max="15000"
                    value="{{ request('max_price', 15000) }}"
                    id="priceSlider" name="max_price"
                    oninput="updatePrice(this.value)">

                <!-- Manual price input -->
                <div class="price-input-wrap">
                    <label for="priceInput">Max ₱</label>
                    <input
                        type="number"
                        id="priceInput"
                        class="price-input"
                        min="1000"
                        max="15000"
                        step="100"
                        value="{{ request('max_price', 15000) }}"
                        placeholder="e.g. 5000"
                        oninput="syncFromInput(this.value)"
                    >
                </div>
            </div>

            <!-- Room Type -->
            <div class="filter-group">
                <label class="filter-label">Room Type</label>
                @foreach(['single'=>'Single Room','double'=>'Double Room','studio'=>'Studio','shared'=>'Shared Room','dormitory'=>'Dormitory'] as $val => $label)
                <label class="filter-option">
                    <input type="radio" name="room_type" value="{{ $val }}" {{ request('room_type') === $val ? 'checked' : '' }}>
                    <div class="custom-check custom-radio"></div>
                    <span>{{ $label }}</span>
                </label>
                @endforeach
            </div>

            <!-- Amenities -->
<div class="filter-group">
    <label class="filter-label">Amenities</label>
    @foreach([
        'WiFi Included', 'Air Conditioning', 'Shared Kitchen', '24/7 Security',
        'Unlimited Water', 'Shared Laundry', 'Electricity Backup', 'Parking',
        'CCTV', 'Gym', 'Elevator', 'Pet Friendly',
    ] as $amenity)
    <label class="filter-option">
        <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
            {{ in_array($amenity, request('amenities', [])) ? 'checked' : '' }}>
        <div class="custom-check"></div>
        <span>{{ $amenity }}</span>
    </label>
    @endforeach
</div>

            @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <button type="submit" class="apply-btn">Apply Filters</button>
        </form>

        @if($properties->count() > 0)
        @php $featured = $properties->first(); @endphp
        <div class="promo-card" style="margin-top:20px;">
            <img src="{{ $featured->image ? Storage::url($featured->image) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&q=80' }}" alt="{{ $featured->title }}">
            <span class="promo-badge">GOOD DEAL</span>
            <div class="promo-name">{{ $featured->title }}</div>
            <div class="promo-price">Starting from ₱{{ number_format($featured->price, 0) }}/mo</div>
        </div>
        @endif
    </div>

    <!-- Results Panel -->
    <div class="results-panel">
        <div class="results-header">
            <div class="results-controls">
                <nav class="breadcrumb">
                    <a href="{{ route('dashboard') }}">HOME</a>
                    <span class="sep">›</span>
                    <span class="current">BOARDING HOUSES</span>
                </nav>
            </div>
            <h2>Search Results</h2>
            <div class="results-sub">
                Found <span>{{ $properties->total() }}</span> boarding
                {{ Str::plural('house', $properties->total()) }}
                @if(request('q')) near <span>"{{ request('q') }}"</span> @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <span style="font-size:0.85rem;color:var(--text-muted)">Sort by:</span>
            <select class="sort-select" onchange="sortProperties(this.value)">
                <option value="latest">Newest</option>
                <option value="price_asc">Lowest Price</option>
                <option value="price_desc">Highest Price</option>
            </select>
        </div>

        <!-- Cards -->
        <div class="props-grid">
            @forelse($properties as $index => $property)
            @php
                $image = $property->image
                    ? Storage::url($property->image)
                    : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&q=80';
                $tags = $property->amenities
                    ? array_slice(array_map('trim', explode(',', $property->amenities)), 0, 2)
                    : [];
                $badge = match($index % 3) {
                    0 => ['class'=>'tag-popular','label'=>'POPULAR'],
                    1 => ['class'=>'tag-new',    'label'=>'NEW'],
                    2 => ['class'=>'tag-best',   'label'=>'BEST VALUE'],
                };
                $isFav = in_array($property->id, $favoritedIds);
            @endphp
            <a href="{{ route('property.show', $property->id) }}" style="text-decoration:none;color:inherit;">
                <div class="prop-card">
                    <div class="prop-img">
                        <img src="{{ $image }}" alt="{{ $property->title }}">
                        <span class="prop-tag {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        <button class="save-btn {{ $isFav ? 'saved' : '' }}"
                                id="fav-btn-{{ $property->id }}"
                                onclick="event.preventDefault(); toggleFav({{ $property->id }})"
                                title="{{ $isFav ? 'Remove from favorites' : 'Save to favorites' }}">
                            <i class="{{ $isFav ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </div>
                    <div class="prop-body">
                        <div style="display:flex;align-items:start;justify-content:space-between;gap:8px;margin-bottom:5px;">
                            <div class="prop-name">{{ $property->title }}</div>
                            <div class="prop-rating"><i class="fas fa-star"></i> 4.8</div>
                        </div>
                        <div class="prop-dist">
                            <i class="fas fa-map-marker-alt"></i> {{ $property->address }}
                        </div>
                        <div class="prop-tags-row">
                            @foreach($tags as $tag)
                            <span class="prop-tag-pill">{{ ucfirst($tag) }}</span>
                            @endforeach
                            <span class="prop-tag-pill">{{ ucfirst($property->room_type ?? 'Room') }}</span>
                        </div>
                        <div class="prop-footer">
                            <div>
                                <div class="prop-price-label">Per Month</div>
                                <div class="prop-price-val">₱{{ number_format($property->price, 0) }}</div>
                            </div>
                            <span class="btn btn-sm btn-primary">View Details</span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p style="font-size:1rem;font-weight:600;margin-bottom:6px;">No properties found</p>
                <p style="font-size:0.875rem;">Try adjusting your filters or search query.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pagination-wrap">
            <div>{{ $properties->withQueryString()->links() }}</div>
            <button class="map-toggle-btn" onclick="window.location='{{ route('search') }}?view=map'">
                <i class="fas fa-map"></i> Show Map View
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePrice(val) {
    val = Math.min(Math.max(parseInt(val) || 1000, 1000), 15000);
    document.getElementById('priceMax').textContent = '₱' + val.toLocaleString();
    document.getElementById('priceSlider').value = val;
    document.getElementById('priceInput').value = val;
    const pct = ((val - 1000) / (15000 - 1000)) * 100;
    document.getElementById('priceSlider').style.background =
        `linear-gradient(to right,var(--teal) 0%,var(--teal) ${pct}%,var(--border) ${pct}%)`;
}

function syncFromInput(val) {
    val = Math.min(Math.max(parseInt(val) || 1000, 1000), 15000);
    document.getElementById('priceMax').textContent = '₱' + val.toLocaleString();
    document.getElementById('priceSlider').value = val;
    document.getElementById('priceSlider').style.background =
        `linear-gradient(to right,var(--teal) 0%,var(--teal) ${((val - 1000) / (15000 - 1000)) * 100}%,var(--border) ${((val - 1000) / (15000 - 1000)) * 100}%)`;
}

function toggleFav(propertyId) {
    const btn  = document.getElementById('fav-btn-' + propertyId);
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
            icon.className = 'fas fa-heart';
            btn.classList.add('saved');
            btn.title = 'Remove from favorites';
        } else {
            icon.className = 'far fa-heart';
            btn.classList.remove('saved');
            btn.title = 'Save to favorites';
        }
    });
}

function sortProperties(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    window.location.href = url.toString();
}

// Init slider gradient on page load
updatePrice(document.getElementById('priceSlider').value);
</script>
@endpush