<div class="dashboard">

    <!-- KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['total_employees']) ?></span>
                <span class="stat-label">Total Employees</span>
            </div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['active_employees']) ?></span>
                <span class="stat-label">Active Employees</span>
            </div>
        </div>

        <div class="stat-card stat-amber">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['total_depts']) ?></span>
                <span class="stat-label">Departments</span>
            </div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?= number_format($stats['new_this_month']) ?></span>
                <span class="stat-label">New This Month</span>
            </div>
        </div>
    </div>






   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

   <?php if (Auth::check()): ?> 
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h2 class="card-title">Live Workforce Map Tracker</h2>
            <button onclick="window.location.reload()" class="btn btn-secondary btn-sm">Refresh Locations</button>
        </div>
        <div class="card-body p-0" style="position: relative;">
            <div id="employeeMap" style="height: 500px; width: 100%; border-radius: 0 0 var(--r-lg) var(--r-lg); z-index: 1;"></div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map (Default center: Zamboanga City)
            var map = L.map('employeeMap').setView([6.9214, 122.0790], 12);

            // Add the OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Fetch the locations passed from the controller
            var locations = <?= json_encode($locations ?? []) ?>;
            var routingControl = null; // Variable to store our active route

            // Function to draw route from Admin to Employee
            window.routeTo = function(destLat, destLng) {
                // Remove existing route if there is one
                if (routingControl) {
                    map.removeControl(routingControl);
                }
                
                // Get Admin's current location as the starting point
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var startLat = position.coords.latitude;
                        var startLng = position.coords.longitude;
                        
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(startLat, startLng), // Start (Admin)
                                L.latLng(destLat, destLng)    // End (Employee)
                            ],
                            routeWhileDragging: false,
                            addWaypoints: false,
                            show: false, // Hides the step-by-step text box to keep the UI clean
                            lineOptions: {
                                styles: [{color: '#2563eb', weight: 4}] // Blue route line
                            }
                        }).addTo(map);
                    }, function(error) {
                        alert("Could not get your current location for routing. Please ensure GPS is enabled.");
                    }, { enableHighAccuracy: true });
                }
            };

            // Plot markers for each employee
            locations.forEach(function(emp) {
                if (emp.latitude && emp.longitude) {
                    // Determine Status Color Ring
                    var statusColor = emp.availability_status === 'available' ? '#10b981' : (emp.availability_status === 'out-of-town' ? '#f59e0b' : '#ef4444');
                    
                    // Build the Custom Profile Picture Marker
                    var picUrl = emp.profile_picture ? `${BASE_URL}/assets/images/profiles/${emp.profile_picture}` : '';
                    var iconHtml = '';
                    
                    if (picUrl) {
                        // User has a profile picture
                        iconHtml = `<div style="width: 36px; height: 36px; border-radius: 50%; border: 3px solid ${statusColor}; background-image: url('${picUrl}'); background-size: cover; background-position: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>`;
                    } else {
                        // Fallback to initials if no picture
                        var initials = emp.first_name.charAt(0) + emp.last_name.charAt(0);
                        iconHtml = `<div style="width: 36px; height: 36px; border-radius: 50%; border: 3px solid ${statusColor}; background-color: #f1f5f9; color: #1e293b; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">${initials.toUpperCase()}</div>`;
                    }

                    var customIcon = L.divIcon({
                        className: 'custom-profile-pin',
                        html: iconHtml,
                        iconSize: [42, 42],
                        iconAnchor: [21, 21] // Centers the pin perfectly
                    });

                    // Build the Popup Content (Now with a "Get Route" button)
                    var popupContent = `
                        <div style="text-align:center; min-width: 140px;">
                            <strong style="font-size: 14px;">${emp.first_name} ${emp.last_name}</strong><br>
                            <span style="font-size:11px; color:#666; display:block; margin-bottom: 4px;">${emp.department_name || 'No Dept'}</span>
                            <span class="badge badge-${emp.availability_status}">${emp.availability_status}</span><br>
                            <span style="font-size:10px; color:#999; display:block; margin: 8px 0;">Last seen: ${emp.location_updated_at}</span>
                            <button onclick="routeTo(${emp.latitude}, ${emp.longitude})" class="btn btn-primary btn-xs" style="width: 100%;">📍 Get Route</button>
                        </div>
                    `;

                    L.marker([emp.latitude, emp.longitude], {icon: customIcon})
                     .addTo(map)
                     .bindPopup(popupContent);
                }
            });
        });
    </script>
    <?php endif; ?>