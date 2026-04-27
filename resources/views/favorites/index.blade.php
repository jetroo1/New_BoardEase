@extends('layouts.app')

@section('title', 'Favorites')
@section('search-placeholder', 'Search favorites...')

@push('styles')
<style>
.fav-page-title { color: var(--text); }
.fav-page-sub   { color: var(--text-muted); }

.fav-empty-icon  { color: var(--text-muted); }
.fav-empty-title { color: var(--text-muted); }
.fav-empty-sub   { color: var(--text-muted); }

.fav-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 1rem;
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
}
.fav-card:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    transform: translateY(-4px);
}
.fav-card-img-wrap {
    background: var(--border);
}
.fav-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}
.fav-card-address {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 12px;
}
.fav-tag {
    background: var(--bg);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 500;
    padding: 2px 10px;
    border-radius: 6px;
}
.fav-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #e8692a;
}
.fav-price-mo {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 400;
}
.fav-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-muted);
}
.fav-divider {
    border-top: 1px solid var(--border);
    margin-top: 12px;
    padding-top: 12px;
}
.fav-btn-dark {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
    background: var(--navy);
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 8px;
    transition: background 0.2s;
    text-decoration: none;
}
.fav-btn-dark:hover { background: var(--navy-light); }
.fav-btn-teal {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
    background: var(--teal);
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 8px;
    transition: background 0.2s;
    text-decoration: none;
}
.fav-btn-teal:hover { background: var(--teal-dark); }
</style>
@endpush

@section('content')
<div class="mb-6">
    <h1 class="font-syne text-3xl font-bold fav-page-title">Favorites</h1>
    <p class="text-sm mt-1 fav-page-sub">Boarding houses you've saved for later</p>
</div>

@if($favorites->isEmpty())
<div class="flex flex-col items-center justify-center py-20 fav-empty-icon">
    <i class="fas fa-heart text-5xl mb-4 opacity-20"></i>
    <p class="text-base font-semibold fav-empty-title">No favorites yet</p>
    <p class="text-sm mt-1 fav-empty-sub">Start saving boarding houses you like!</p>
    <a href="{{ route('search') }}"
       class="mt-4 px-5 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-semibold rounded-lg transition">
        Browse Properties
    </a>
</div>

@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @foreach($favorites as $fav)
    @php $property = $fav->property; @endphp
    @if($property)
    <div class="fav-card" id="fav-card-{{ $property->id }}">

        {{-- Image --}}
        <div class="relative h-44 fav-card-img-wrap overflow-hidden">
            <img src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400&q=80' }}"
                 alt="{{ $property->title }}"
                 loading="lazy"
                 class="w-full h-full object-cover">

            {{-- Saved Badge --}}
            <div class="absolute top-2.5 left-2.5 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
                Saved
            </div>

            {{-- Heart Button --}}
            <button onclick="removeFav(this, {{ $property->id }})"
                    title="Remove from favorites"
                    class="absolute top-2.5 right-2.5 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-500 hover:bg-white transition border-none cursor-pointer">
                <i class="fas fa-heart text-sm"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-4">
            <div class="fav-card-title">{{ $property->title }}</div>

            <div class="fav-card-address">
                <i class="fas fa-map-marker-alt text-teal-500"></i>
                {{ $property->address }}
            </div>

            {{-- Amenity Tags --}}
            @if($property->amenities)
            <div class="flex flex-wrap gap-1.5 mb-3">
                @foreach(array_slice(is_array($property->amenities) ? $property->amenities : explode(',', $property->amenities), 0, 3) as $tag)
                <span class="fav-tag">{{ trim($tag) }}</span>
                @endforeach
            </div>
            @endif

            {{-- Price & Rating --}}
            <div class="flex items-center justify-between">
                <div class="fav-price">
                    ₱{{ number_format($property->price) }}
                    <span class="fav-price-mo">/mo</span>
                </div>
                <div class="fav-rating">
                    <i class="fas fa-star text-yellow-400"></i>
                    {{ $property->reviews_avg_rating ? number_format($property->reviews_avg_rating, 1) : '—' }}
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="fav-divider flex gap-2">
                <a href="{{ route('property.show', $property->id) }}" class="fav-btn-dark">
                    View Details
                </a>
                <a href="{{ route('property.show', $property->id) }}" class="fav-btn-teal">
                    Book Now
                </a>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
function removeFav(btn, propertyId) {
    fetch(`/favorites/${propertyId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        const card = document.getElementById(`fav-card-${propertyId}`);
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        card.style.transition = 'all 0.3s';
        setTimeout(() => card.remove(), 300);
    });
}
</script>
@endpush