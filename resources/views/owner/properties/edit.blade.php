@extends('layouts.app')

@section('title', isset($property) ? 'Edit Property' : 'Add New Property')
@section('search-placeholder', 'Search...')

@push('styles')
<style>
    .form-card { background:var(--card);border:1px solid var(--border);border-radius:14px;padding:28px 32px;max-width:800px; }
    .form-section-title { font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px; }
    .form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
    .form-group { margin-bottom:16px; }
    .form-group.full { grid-column:1/-1; }
    .form-label { display:block;font-size:0.85rem;font-weight:600;margin-bottom:7px;color:var(--text); }
    .form-input, .form-select, .form-textarea {
        width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;
        font-family:'DM Sans',sans-serif;font-size:0.9rem;color:var(--text);
        background:var(--bg);outline:none;transition:border-color 0.2s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--teal);background:#fff; }
    .form-textarea { resize:vertical;min-height:90px; }
    #propertyMap { height:280px;border-radius:10px;border:1.5px solid var(--border);margin-top:8px; }
    .map-hint { font-size:0.78rem;color:var(--text-muted);margin-bottom:8px;display:flex;align-items:center;gap:5px; }
    .coords-row { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:10px; }
    .btn-save { padding:12px 28px;background:var(--navy);color:#fff;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:0.95rem;font-weight:700;cursor:pointer;transition:background 0.2s; }
    .btn-save:hover { background:var(--navy-light); }
    .btn-cancel { padding:12px 20px;background:transparent;color:var(--text-muted);border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:0.95rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s; }
    .btn-cancel:hover { border-color:var(--navy);color:var(--navy); }
    .flash-error { background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;font-size:0.875rem; }
    .photo-thumb { position:relative;text-align:center; }
    .photo-thumb img { width:100px;height:70px;object-fit:cover;border-radius:8px;display:block; }
    .photo-thumb label { display:flex;align-items:center;justify-content:center;gap:4px;margin-top:5px;font-size:0.75rem;cursor:pointer;color:var(--text); }
    .photo-thumb input[type=checkbox] { accent-color:var(--teal); }
</style>
@endpush

@section('content')
<div style="margin-bottom:20px;">
    <h1 style="font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:700;">
        {{ isset($property) ? 'Edit Property' : 'Add New Property' }}
    </h1>
    <p style="font-size:0.875rem;color:var(--text-muted);margin-top:4px;">
        {{ isset($property) ? 'Update your listing details.' : 'List your boarding house for tenants to find.' }}
    </p>
</div>

@if($errors->any())
<div class="flash-error">
    <i class="fas fa-exclamation-circle"></i>
    <ul style="margin:0;padding-left:16px;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<div class="form-card">
    <form method="POST"
          action="{{ isset($property) ? route('owner.properties.update', $property->id) : route('owner.properties.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($property)) @method('PUT') @endif

        {{-- Basic Info --}}
        <div class="form-section-title">
            <i class="fas fa-info-circle" style="color:var(--teal)"></i> Basic Information
        </div>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Property Title *</label>
                <input type="text" name="title" class="form-input"
                       value="{{ old('title', $property->title ?? '') }}"
                       placeholder="e.g. Sunny Studio Room near UM Tagum" required>
            </div>
            <div class="form-group full">
                <label class="form-label">Address *</label>
                <input type="text" name="address" class="form-input"
                       value="{{ old('address', $property->address ?? '') }}"
                       placeholder="Full address" required>
            </div>
            <div class="form-group">
                <label class="form-label">Monthly Price (₱) *</label>
                <input type="number" name="price" class="form-input" step="0.01" min="0"
                       value="{{ old('price', $property->price ?? '') }}"
                       placeholder="e.g. 3500" required>
            </div>
            <div class="form-group">
                <label class="form-label">Room Type *</label>
                <select name="room_type" class="form-select" required>
                    <option value="">Select type...</option>
                    @foreach(['single','double','studio','shared','dormitory'] as $type)
                    <option value="{{ $type }}" {{ old('room_type', $property->room_type ?? '') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea"
                          placeholder="Describe your property...">{{ old('description', $property->description ?? '') }}</textarea>
            </div>

            {{-- Amenities Checkboxes --}}
            <div class="form-group full">
                <label class="form-label">Amenities</label>
                @php
                    $amenityOptions = [
                        'WiFi Included', 'Air Conditioning', 'Shared Kitchen', '24/7 Security',
                        'Unlimited Water', 'Shared Laundry', 'Electricity Backup', 'Parking',
                        'CCTV', 'Gym', 'Elevator', 'Pet Friendly',
                    ];
                    $savedAmenities = array_map('trim', explode(',', old('amenities_raw', $property->amenities ?? '')));
                @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;margin-top:4px;">
                    @foreach($amenityOptions as $amenity)
                    <label style="display:flex;align-items:center;gap:8px;padding:9px 12px;border:1.5px solid var(--border);border-radius:10px;cursor:pointer;font-size:0.875rem;transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='var(--teal)'"
                           onmouseout="this.style.borderColor=this.querySelector('input').checked?'var(--teal)':'var(--border)'">
                        <input type="checkbox"
                               name="amenities[]"
                               value="{{ $amenity }}"
                               {{ in_array($amenity, $savedAmenities) ? 'checked' : '' }}
                               onchange="this.closest('label').style.borderColor=this.checked?'var(--teal)':'var(--border)'">
                        {{ $amenity }}
                    </label>
                    @endforeach
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:6px;">Check all that apply.</div>
            </div>
        </div>

        {{-- Map Picker --}}
        <div class="form-section-title" style="margin-top:8px;">
            <i class="fas fa-map-marker-alt" style="color:var(--orange)"></i> Location on Map
        </div>
        <div class="map-hint">
            <i class="fas fa-hand-pointer"></i> Click on the map to set exact coordinates, or type them manually below.
        </div>
        <div id="propertyMap"></div>
        <div class="coords-row">
            <div class="form-group">
                <label class="form-label">Latitude</label>
                <input type="number" name="latitude" id="latInput" class="form-input" step="any"
                       value="{{ old('latitude', $property->latitude ?? '') }}"
                       placeholder="e.g. 7.4479">
            </div>
            <div class="form-group">
                <label class="form-label">Longitude</label>
                <input type="number" name="longitude" id="lngInput" class="form-input" step="any"
                       value="{{ old('longitude', $property->longitude ?? '') }}"
                       placeholder="e.g. 125.8085">
            </div>
        </div>

        {{-- Main Profile Photo --}}
        <div class="form-section-title" style="margin-top:8px;">
            <i class="fas fa-image" style="color:var(--purple)"></i> Property Photo
        </div>
        <div class="form-group">
            @if(isset($property) && $property->image)
                <img src="{{ Storage::url($property->image) }}" id="imgPreview"
                     style="width:100%;max-height:200px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
            @else
                <img id="imgPreview"
                     style="display:none;width:100%;max-height:200px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
            @endif
            <input type="file" name="image" class="form-input" accept="image/*"
                   onchange="previewImage(this)">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">
                {{ isset($property) && $property->image
                    ? 'Choose a new file to replace the current photo.'
                    : 'Upload a photo of your property.' }}
            </div>
        </div>

        {{-- Additional Photos --}}
        <div class="form-section-title" style="margin-top:8px;">
            <i class="fas fa-images" style="color:var(--teal)"></i> Additional Photos (up to 6)
        </div>
        <div class="form-group">

            {{-- Existing photos with keep/remove checkboxes --}}
            @if(isset($property) && $property->photos)
            @php $existingPhotos = json_decode($property->photos, true) ?: []; @endphp
            @if(count($existingPhotos) > 0)
            <div style="font-size:0.82rem;font-weight:600;margin-bottom:8px;color:var(--text);">
                Current Photos — uncheck to remove:
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
                @foreach($existingPhotos as $photo)
                <div class="photo-thumb">
                    <img src="{{ Storage::url($photo) }}">
                    <label>
                        <input type="checkbox"
                               name="keep_photos[]"
                               value="{{ $photo }}"
                               checked>
                        Keep
                    </label>
                </div>
                @endforeach
            </div>
            @endif
            @endif

            {{-- Upload new photos --}}
            <div style="font-size:0.82rem;font-weight:600;margin-bottom:6px;color:var(--text);">
                Add New Photos:
            </div>
            <input type="file" name="photos[]" class="form-input" accept="image/*" multiple
                   onchange="previewNewPhotos(this)">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">
                New uploads are added to existing ones. Maximum 6 additional photos total.
            </div>

            {{-- Preview of newly selected files --}}
            <div id="newPhotoPreviews"
                 style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px;"></div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                {{ isset($property) ? 'Update Property' : 'List Property' }}
            </button>
            <a href="{{ route('owner.properties') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Map Picker ────────────────────────────────────────────────
const defaultLat = {{ old('latitude', $property->latitude ?? 7.4479) }};
const defaultLng = {{ old('longitude', $property->longitude ?? 125.8085) }};

const pickerMap = L.map('propertyMap').setView([defaultLat, defaultLng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(pickerMap);

let pickerMarker = null;

if (document.getElementById('latInput').value && document.getElementById('lngInput').value) {
    const lat = parseFloat(document.getElementById('latInput').value);
    const lng = parseFloat(document.getElementById('lngInput').value);
    pickerMarker = L.marker([lat, lng], { draggable: true }).addTo(pickerMap);
    pickerMarker.on('dragend', syncFromMarker);
}

pickerMap.on('click', function(e) {
    const { lat, lng } = e.latlng;
    if (pickerMarker) {
        pickerMarker.setLatLng([lat, lng]);
    } else {
        pickerMarker = L.marker([lat, lng], { draggable: true }).addTo(pickerMap);
        pickerMarker.on('dragend', syncFromMarker);
    }
    document.getElementById('latInput').value = lat.toFixed(7);
    document.getElementById('lngInput').value = lng.toFixed(7);
});

['latInput','lngInput'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('latInput').value);
        const lng = parseFloat(document.getElementById('lngInput').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            if (pickerMarker) {
                pickerMarker.setLatLng([lat, lng]);
            } else {
                pickerMarker = L.marker([lat, lng], { draggable: true }).addTo(pickerMap);
                pickerMarker.on('dragend', syncFromMarker);
            }
            pickerMap.setView([lat, lng], 15);
        }
    });
});

function syncFromMarker() {
    const { lat, lng } = pickerMarker.getLatLng();
    document.getElementById('latInput').value = lat.toFixed(7);
    document.getElementById('lngInput').value = lng.toFixed(7);
}

// ── Image preview ─────────────────────────────────────────────
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src          = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── New additional photos preview ─────────────────────────────
function previewNewPhotos(input) {
    const container = document.getElementById('newPhotoPreviews');
    container.innerHTML = '';
    if (!input.files) return;
    const keptPhotos = document.querySelectorAll('input[name="keep_photos[]"]:checked').length;
    const remainingSlots = Math.max(6 - keptPhotos, 0);

    if (input.files.length > remainingSlots) {
        alert(`You can add ${remainingSlots} more photo${remainingSlots === 1 ? '' : 's'} only. Maximum is 6 additional photos total.`);
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap       = document.createElement('div');
            wrap.style.cssText = 'text-align:center;';
            wrap.innerHTML   = `
                <img src="${e.target.result}"
                     style="width:100px;height:70px;object-fit:cover;border-radius:8px;display:block;">
                <div style="font-size:0.72rem;color:var(--text-muted);margin-top:4px;">New ${i + 1}</div>`;
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
