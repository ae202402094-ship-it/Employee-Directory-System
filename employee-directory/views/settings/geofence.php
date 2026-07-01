<?php require_once BASE_PATH . '/helpers/CSRF.php'; ?>
<div class="container-fluid" style="padding: 24px;">
    
    <div class="row" style="display: flex; gap: 24px; flex-wrap: wrap;">
        <!-- Left Column: Coordinates Form -->
        <div style="flex: 1; min-width: 320px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Office Geofence Coordinates Settings</h2>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <form action="<?= BASE_URL ?>/settings/geofence" method="POST" id="geofence-form">
                        <?= CSRF::field() ?>
                        
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Office Location Name</label>
                            <input type="text" name="office_name" id="office_name" class="form-control" value="<?= htmlspecialchars($setting['office_name'] ?? 'Main Headquarters') ?>" required>
                        </div>

                        <div class="form-row" style="display: flex; gap: 16px; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control mono" value="<?= htmlspecialchars($setting['latitude'] ?? '6.92140000') ?>" required>
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control mono" value="<?= htmlspecialchars($setting['longitude'] ?? '122.07900000') ?>" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label">Geofence Boundary Radius (Meters)</label>
                            <input type="number" name="radius_meters" id="radius_meters" class="form-control" value="<?= htmlspecialchars($setting['radius_meters'] ?? '200') ?>" min="10" max="5000" required>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">Employees must check in inside this radius circle.</div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Save Geofence Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Visual Circle Map Overlay -->
        <div style="flex: 2; min-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Geofence Boundary Circle Map Visualizer</h2>
                </div>
                <div class="card-body" style="padding: 0; min-height: 400px; position: relative;">
                    <div id="geofenceMap" style="height: 450px; width: 100%; z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Leaflet Styles & Scripts CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var defaultLat = parseFloat(document.getElementById('latitude').value) || 6.9214;
        var defaultLng = parseFloat(document.getElementById('longitude').value) || 122.0790;
        var defaultRadius = parseInt(document.getElementById('radius_meters').value) || 200;

        // Initialize Map
        var map = L.map('geofenceMap').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Add Office Center marker
        var marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        // Add Geofence boundary circle overlay
        var geofenceCircle = L.circle([defaultLat, defaultLng], {
            color: '#4f46e5',
            fillColor: '#818cf8',
            fillOpacity: 0.15,
            radius: defaultRadius
        }).addTo(map);

        // Handle Pin Drag event
        marker.on('drag', function(e) {
            var position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(8);
            document.getElementById('longitude').value = position.lng.toFixed(8);
            geofenceCircle.setLatLng(position);
        });

        // Handle Map Click to place marker
        map.on('click', function(e) {
            var position = e.latlng;
            marker.setLatLng(position);
            geofenceCircle.setLatLng(position);
            document.getElementById('latitude').value = position.lat.toFixed(8);
            document.getElementById('longitude').value = position.lng.toFixed(8);
        });

        // Handle Radius slider / input change
        document.getElementById('radius_meters').addEventListener('input', function() {
            var rad = parseInt(this.value) || 200;
            geofenceCircle.setRadius(rad);
        });

        // Fit map bounds to show geofence circle fully
        map.fitBounds(geofenceCircle.getBounds());
    });
</script>
