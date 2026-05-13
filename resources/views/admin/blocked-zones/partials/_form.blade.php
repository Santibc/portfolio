@php
    $isEdit = isset($zone);
    $type = $isEdit ? $zone->type : ($type ?? 'polygon');
    $action = $isEdit ? route('admin.blocked-zones.update', $zone) : route('admin.blocked-zones.store');
    $polygonCoords = $isEdit && $zone->polygon_coordinates ? json_encode($zone->polygon_coordinates) : '[]';
@endphp

<form method="POST" action="{{ $action }}" id="zone-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <input type="hidden" name="type" value="{{ $type }}">

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Internal name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required maxlength="150"
                           value="{{ old('name', $isEdit ? $zone->name : '') }}"
                           placeholder="@if($type==='polygon')Outback NT @elseif($type==='postcode')Darwin 0800 @else Casuarina @endif">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State (optional)</label>
                    <select name="state" class="form-select">
                        <option value="">—</option>
                        @foreach(['NSW','VIC','QLD','WA','SA','TAS','ACT','NT'] as $st)
                            <option value="{{ $st }}" @selected(old('state', $isEdit ? $zone->state : '') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                @if($type === 'postcode')
                    <div class="col-md-6">
                        <label class="form-label">Postcode <span class="text-danger">*</span></label>
                        <input type="text" name="postcode" class="form-control" required maxlength="20"
                               value="{{ old('postcode', $isEdit ? $zone->postcode : '') }}" placeholder="0800">
                    </div>
                @endif

                @if($type === 'suburb')
                    <div class="col-md-6">
                        <label class="form-label">Suburb name <span class="text-danger">*</span></label>
                        <input type="text" name="suburb" class="form-control" required maxlength="150"
                               value="{{ old('suburb', $isEdit ? $zone->suburb : '') }}" placeholder="Casuarina">
                        <small class="text-muted">Match is case-insensitive.</small>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="1000">{{ old('notes', $isEdit ? $zone->notes : '') }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                               @checked(old('is_active', $isEdit ? $zone->is_active : true))>
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($type === 'polygon')
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Draw the blocked area on the map</h5>
                <p class="text-muted small">Click the polygon tool, then click on the map to add vertices. Double-click to finish. You can drag vertices to adjust. Click <strong>Clear & Redraw</strong> to start over.</p>

                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-polygon">
                        <i class="bi bi-eraser"></i> Clear & Redraw
                    </button>
                    <span class="ms-auto text-muted small" id="polygon-info">No polygon drawn yet.</span>
                </div>

                <div id="map" style="height: 500px; border: 1px solid #dee2e6; border-radius: 6px;"></div>

                <input type="hidden" name="polygon_coordinates" id="polygon_coordinates"
                       value="{{ old('polygon_coordinates', $polygonCoords) }}">
            </div>
        </div>
    @endif

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Update' : 'Create' }} Blocked Zone
        </button>
        <a href="{{ route('admin.blocked-zones.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

@if($type === 'polygon')
    @if(empty($layoutConfig?->google_maps_api_key))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Google Maps API Key is not configured. Please set it in
            <a href="{{ route('admin.landing.index') }}#layout">Admin → Landing → Layout</a> before drawing polygons.
        </div>
    @else
    @push('scripts')
        <script>
            let map, drawingManager, currentPolygon = null;
            const initialCoords = {!! $polygonCoords !!};

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: -25.2744, lng: 133.7751 },
                    zoom: 4,
                    mapTypeId: 'roadmap'
                });

                drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: initialCoords.length > 0 ? null : google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                    },
                    polygonOptions: {
                        fillColor: '#dc3545', fillOpacity: 0.3,
                        strokeColor: '#dc3545', strokeWeight: 2,
                        editable: true, draggable: false
                    }
                });
                drawingManager.setMap(map);

                google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                    if (currentPolygon) currentPolygon.setMap(null);
                    currentPolygon = polygon;
                    drawingManager.setDrawingMode(null);
                    google.maps.event.addListener(polygon.getPath(), 'set_at', updateCoords);
                    google.maps.event.addListener(polygon.getPath(), 'insert_at', updateCoords);
                    google.maps.event.addListener(polygon.getPath(), 'remove_at', updateCoords);
                    updateCoords();
                });

                if (initialCoords.length >= 3) {
                    currentPolygon = new google.maps.Polygon({
                        paths: initialCoords,
                        fillColor: '#dc3545', fillOpacity: 0.3,
                        strokeColor: '#dc3545', strokeWeight: 2,
                        editable: true, map: map
                    });
                    google.maps.event.addListener(currentPolygon.getPath(), 'set_at', updateCoords);
                    google.maps.event.addListener(currentPolygon.getPath(), 'insert_at', updateCoords);
                    google.maps.event.addListener(currentPolygon.getPath(), 'remove_at', updateCoords);

                    const bounds = new google.maps.LatLngBounds();
                    initialCoords.forEach(c => bounds.extend(new google.maps.LatLng(c.lat, c.lng)));
                    map.fitBounds(bounds);
                    document.getElementById('polygon-info').textContent = initialCoords.length + ' vertices';
                }

                document.getElementById('btn-clear-polygon').addEventListener('click', function() {
                    if (currentPolygon) {
                        currentPolygon.setMap(null);
                        currentPolygon = null;
                    }
                    document.getElementById('polygon_coordinates').value = '[]';
                    document.getElementById('polygon-info').textContent = 'No polygon drawn yet.';
                    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
                });
            }

            function updateCoords() {
                if (!currentPolygon) return;
                const path = currentPolygon.getPath();
                const coords = [];
                for (let i = 0; i < path.getLength(); i++) {
                    const ll = path.getAt(i);
                    coords.push({ lat: ll.lat(), lng: ll.lng() });
                }
                document.getElementById('polygon_coordinates').value = JSON.stringify(coords);
                document.getElementById('polygon-info').textContent = coords.length + ' vertices';
            }

            document.getElementById('zone-form').addEventListener('submit', function(e) {
                const val = document.getElementById('polygon_coordinates').value;
                let coords = [];
                try { coords = JSON.parse(val); } catch (err) {}
                if (!Array.isArray(coords) || coords.length < 3) {
                    e.preventDefault();
                    alert('Please draw a polygon with at least 3 points.');
                }
            });
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key={{ $layoutConfig->google_maps_api_key }}&libraries=drawing&callback=initMap"></script>
    @endpush
    @endif
@endif
