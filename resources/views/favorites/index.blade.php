@extends('layouts.app')

@section('title', 'Favorites')
@section('search-placeholder', 'Search favorites...')

@section('content')
<div class="mb-6">
    <h1 class="font-syne text-3xl font-bold text-slate-800">Favorites</h1>
    <p class="text-sm text-slate-500 mt-1">Boarding houses you've saved for later</p>
</div>

@if($favorites->isEmpty())
<div class="flex flex-col items-center justify-center py-20 text-slate-400">
    <i class="fas fa-heart text-5xl mb-4 opacity-20"></i>
    <p class="text-base font-semibold">No favorites yet</p>
    <p class="text-sm mt-1">Start saving boarding houses you like!</p>
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
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden transition duration-200 hover:shadow-xl hover:-translate-y-1"
         id="fav-card-{{ $property->id }}">

        {{-- Image --}}
        <div class="relative h-44 bg-slate-200 overflow-hidden">
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
            <div class="text-base font-bold text-slate-800 mb-1">{{ $property->title }}</div>

            <div class="flex items-center gap-1 text-xs text-slate-500 mb-3">
                <i class="fas fa-map-marker-alt text-teal-500"></i>
                {{ $property->address }}
            </div>

            {{-- Amenity Tags --}}
            @if($property->amenities)
            <div class="flex flex-wrap gap-1.5 mb-3">
                @foreach(array_slice(is_array($property->amenities) ? $property->amenities : explode(',', $property->amenities), 0, 3) as $tag)
                <span class="bg-slate-100 border border-slate-200 text-slate-500 text-xs font-medium px-2.5 py-0.5 rounded-md">
                    {{ trim($tag) }}
                </span>
                @endforeach
            </div>
            @endif

            {{-- Price & Rating --}}
            <div class="flex items-center justify-between">
                <div class="text-lg font-bold text-orange-500">
                    ₱{{ number_format($property->price) }}
                    <span class="text-xs text-slate-400 font-normal">/mo</span>
                </div>
                <div class="flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <i class="fas fa-star text-yellow-400"></i>
                    {{ $property->reviews_avg_rating ? number_format($property->reviews_avg_rating, 1) : '—' }}
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                <a href="{{ route('property.show', $property->id) }}"
                   class="flex-1 text-center py-2 px-3 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg transition">
                    View Details
                </a>
                <a href="{{ route('property.show', $property->id) }}"
                   class="flex-1 text-center py-2 px-3 bg-teal-500 hover:bg-teal-600 text-white text-sm font-semibold rounded-lg transition">
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