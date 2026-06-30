<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
    align-items: start;
}
@media (max-width: 992px) {
    .dashboard-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<div class="dashboard">

    <?php if (Auth::isHR() || Auth::isAdmin()): ?>
        <!-- Admin & HR Layout -->
        <h2 style="margin-bottom: 24px; font-weight: 800; font-size: 22px;">Workforce Overview</h2>
        
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

    <?php elseif (Auth::isDeptHead()): ?>
        <!-- Manager & Supervisor Layout -->
        <h2 style="margin-bottom: 8px; font-weight: 800; font-size: 22px;">Welcome back, <?= htmlspecialchars($_SESSION['user_username']) ?>!</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Department: <strong style="color: var(--brand);"><?= htmlspecialchars($deptName) ?></strong> (Role: <?= ucfirst(Auth::role()) ?>)</p>
        
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 24px;">
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= number_format($stats['total_employees']) ?></span>
                    <span class="stat-label">Department Members</span>
                </div>
            </div>

            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= number_format($stats['active_employees']) ?></span>
                    <span class="stat-label">Active Members</span>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Regular Employee Layout -->
        <h2 style="margin-bottom: 8px; font-weight: 800; font-size: 22px;">Welcome, <?= htmlspecialchars($employee['first_name'] ?? $_SESSION['user_username']) ?>!</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Team: <strong style="color: var(--brand);"><?= htmlspecialchars($deptName) ?></strong></p>

        <?php if ($employee): ?>
        <div class="card" style="margin-bottom: 24px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <?php if (!empty($employee['profile_picture'])): ?>
                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($employee['profile_picture']) ?>" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <div class="profile-avatar" style="width: 64px; height: 64px; font-size: 24px;">
                        <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 style="font-weight: 700; margin: 0; font-size: 16px;"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h3>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);"><?= htmlspecialchars($employee['position'] ?? 'Employee') ?> &bull; <?= htmlspecialchars($employee['employee_number']) ?></p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>" class="btn btn-secondary btn-sm">View My Profile Page</a>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Map & Chart Section Grid -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php if (Auth::check()): ?> 
    <div class="dashboard-grid">
        
        <!-- Left: Map Tracker Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h2 class="card-title">Live Workforce Map Tracker</h2>
                <button onclick="window.location.reload()" class="btn btn-secondary btn-sm">Refresh Locations</button>
            </div>
            <div class="card-body p-0" style="position: relative;">
                <div id="employeeMap" style="height: 500px; width: 100%; border-radius: 0 0 var(--r-lg) var(--r-lg); z-index: 1;"></div>
            </div>
        </div>

        <!-- Right: Department Distribution Chart Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h2 class="card-title">Department Distribution</h2>
            </div>
            <div class="card-body" style="padding: 24px; min-height: 400px; display: flex; flex-direction: column; justify-content: center; align-items: center; background: var(--bg-card);">
                <?php if (empty($deptBreakdown)): ?>
                    <div style="text-align: center; color: var(--text-muted); font-size: 13.5px;">No active department distribution data.</div>
                <?php else: ?>
                    <div style="position: relative; height: 220px; width: 100%; max-width: 220px; margin-bottom: 24px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                    <!-- Custom Department Counts List -->
                    <div style="width: 100%; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px; text-align: left; letter-spacing: 0.5px;">Members Count</div>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                            <?php 
                            $chartColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#ef4444', '#06b6d4'];
                            foreach ($deptBreakdown as $idx => $d): 
                                $color = $chartColors[$idx % count($chartColors)];
                            ?>
                            <li style="display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; color: var(--text-secondary);">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: <?= $color ?>;"></span>
                                    <span><?= htmlspecialchars($d['department']) ?></span>
                                </div>
                                <strong style="color: var(--text-primary);"><?= number_format($d['total']) ?> <?= (int)$d['total'] === 1 ? 'member' : 'members' ?></strong>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Render Department Chart
            <?php if (!empty($deptBreakdown)): ?>
            (function() {
                var ctx = document.getElementById('deptChart').getContext('2d');
                var deptData = <?= json_encode($deptBreakdown) ?>;
                var labels = deptData.map(function(d) { return d.department; });
                var totals = deptData.map(function(d) { return parseInt(d.total) || 0; });
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: totals,
                            backgroundColor: [
                                '#3b82f6', // Bright Blue
                                '#10b981', // Bright Emerald
                                '#f59e0b', // Vibrant Amber
                                '#8b5cf6', // Indigo
                                '#ec4899', // Pink
                                '#ef4444', // Rose Red
                                '#06b6d4'  // Cyan
                            ],
                            borderWidth: 2,
                            borderColor: 'rgba(255,255,255,0.05)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                                    color: '#94a3b8'
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            })();
            <?php endif; ?>
            var map = L.map('employeeMap').setView([6.9214, 122.0790], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
            var locations = <?= json_encode($locations ?? []) ?>;
            var routingControl = null;
            var fallbackLine = null;

            // Draw a straight line between locations if OSRM engine fails or is blocked
            function drawFallbackPolyline(startLat, startLng, destLat, destLng) {
                if (routingControl) {
                    try { map.removeControl(routingControl); } catch(e) {}
                    routingControl = null;
                }
                if (fallbackLine) {
                    try { map.removeLayer(fallbackLine); } catch(e) {}
                }
                
                fallbackLine = L.polyline([
                    [startLat, startLng],
                    [destLat, destLng]
                ], {
                    color: '#ef4444', 
                    weight: 4,
                    dashArray: '8, 8',
                    opacity: 0.8
                }).addTo(map);
                
                map.fitBounds(fallbackLine.getBounds(), { padding: [50, 50] });
            }

            window.routeTo = function(destLat, destLng) {
                if (routingControl) {
                    try { map.removeControl(routingControl); } catch(e) {}
                    routingControl = null;
                }
                if (fallbackLine) {
                    try { map.removeLayer(fallbackLine); } catch(e) {}
                    fallbackLine = null;
                }

                var drawRoute = function(startLat, startLng) {
                    try {
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(startLat, startLng),
                                L.latLng(destLat, destLng)
                            ],
                            routeWhileDragging: false,
                            addWaypoints: false,
                            show: false,
                            lineOptions: {
                                styles: [{color: '#4f46e5', weight: 5, opacity: 0.85}]
                            }
                        }).addTo(map);

                        routingControl.on('routingerror', function() {
                            drawFallbackPolyline(startLat, startLng, destLat, destLng);
                        });
                    } catch(err) {
                        drawFallbackPolyline(startLat, startLng, destLat, destLng);
                    }
                };

                // Fallback to default map center if user blocks geolocation or GPS is unavailable
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            drawRoute(position.coords.latitude, position.coords.longitude);
                        },
                        function(error) {
                            var center = map.getCenter();
                            drawRoute(center.lat, center.lng);
                        },
                        { enableHighAccuracy: true, timeout: 5000 }
                    );
                } else {
                    var center = map.getCenter();
                    drawRoute(center.lat, center.lng);
                }
            };;

            locations.forEach(function(emp) {
                if (emp.latitude && emp.longitude) {
                    var statusColor = emp.availability_status === 'available' ? '#10b981' : (emp.availability_status === 'out-of-town' ? '#f59e0b' : '#ef4444');
                    var picUrl = emp.profile_picture ? `${BASE_URL}/assets/images/profiles/${emp.profile_picture}` : '';
                    var iconHtml = '';
                    
                    if (picUrl) {
                        iconHtml = `<div style="width: 36px; height: 36px; border-radius: 50%; border: 3px solid ${statusColor}; background-image: url('${picUrl}'); background-size: cover; background-position: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>`;
                    } else {
                        var initials = emp.first_name.charAt(0) + emp.last_name.charAt(0);
                        iconHtml = `<div style="width: 36px; height: 36px; border-radius: 50%; border: 3px solid ${statusColor}; background-color: #f1f5f9; color: #1e293b; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">${initials.toUpperCase()}</div>`;
                    }

                    var customIcon = L.divIcon({
                        className: 'custom-profile-pin',
                        html: iconHtml,
                        iconSize: [42, 42],
                        iconAnchor: [21, 21]
                    });

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

    <!-- Today's Operational Status Board -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header" style="display: flex; flex-direction: column; gap: 16px; align-items: stretch; border-bottom: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 class="card-title">Daily Workforce Duty Board</h2>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Daily updates of active status lists for quick contacts</div>
                </div>
            </div>
            <!-- Tab Buttons -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <button type="button" onclick="switchDutyTab('working')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-working" style="background: var(--brand); color: #fff;">Working (<?= count($workingList) ?>)</button>
                <button type="button" onclick="switchDutyTab('late')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-late">Late (<?= count($lateList) ?>)</button>
                <button type="button" onclick="switchDutyTab('absent')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-absent">Absent (<?= count($absentList) ?>)</button>
                <button type="button" onclick="switchDutyTab('on-break')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-on-break">On Break (<?= count($onBreakList) ?>)</button>
                <button type="button" onclick="switchDutyTab('half-day')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-half-day">Half Day (<?= count($halfDayList) ?>)</button>
                <button type="button" onclick="switchDutyTab('out-of-town')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-out-of-town">Out of Town (<?= count($outOfTownList) ?>)</button>
                <button type="button" onclick="switchDutyTab('day-off')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-day-off">Day Off (<?= count($dayOffList) ?>)</button>
                <button type="button" onclick="switchDutyTab('on-leave')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-on-leave">On Leave (<?= count($onLeaveList) ?>)</button>
                <button type="button" onclick="switchDutyTab('overtime')" class="btn btn-secondary btn-xs duty-tab-btn" id="btn-tab-overtime">Overtime (<?= count($overtimeList) ?>)</button>
            </div>
        </div>

        <?php
        // Dynamic tab table rendering helper
        if (!function_exists('renderDutyTable')) {
            function renderDutyTable(array $list, string $emptyMessage, string $badgeStyle, string $badgeText) {
                if (empty($list)): ?>
                    <div style="padding: 32px; text-align: center; color: var(--text-muted); font-size: 13.5px;"><?= htmlspecialchars($emptyMessage) ?></div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Position & Dept</th>
                                    <th>Availability</th>
                                    <th class="text-right">Contact Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $emp): ?>
                                <tr>
                                    <td>
                                        <div class="table-emp">
                                            <?php if (!empty($emp['profile_picture'])): ?>
                                                <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($emp['profile_picture']) ?>" class="emp-avatar emp-avatar-sm" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="emp-avatar emp-avatar-sm">
                                                    <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="emp-fullname"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></span>
                                                <span class="emp-email"><?= htmlspecialchars($emp['employee_number']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 500; display: block; font-size: 13.5px;"><?= htmlspecialchars($emp['position'] ?: 'Employee') ?></span>
                                        <span style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($emp['department_name'] ?: 'No Dept') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge" style="<?= $badgeStyle ?>">
                                            <?= htmlspecialchars($badgeText) ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($emp['phone'] ?: 'No Phone') ?></div>
                                        <a href="mailto:<?= htmlspecialchars($emp['email']) ?>" style="font-size: 12px; color: var(--brand);"><?= htmlspecialchars($emp['email']) ?></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif;
            }
        }
        ?>
        
        <!-- Tab 1: Working -->
        <div class="duty-tab-content" id="tab-working" style="display: block;">
            <?php renderDutyTable($workingList, "No employees are currently working.", "background: var(--green-bg); color: var(--green); border-color: var(--green-border);", "Working"); ?>
        </div>

        <!-- Tab 2: Late -->
        <div class="duty-tab-content" id="tab-late" style="display: none;">
            <?php renderDutyTable($lateList, "No employees are logged as late.", "background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2);", "Late"); ?>
        </div>

        <!-- Tab: Absent -->
        <div class="duty-tab-content" id="tab-absent" style="display: none;">
            <?php renderDutyTable($absentList, "No employees are logged as absent today.", "background: rgba(100, 116, 139, 0.1); color: #64748b; border-color: rgba(100, 116, 139, 0.2);", "Absent"); ?>
        </div>

        <!-- Tab 3: On Break -->
        <div class="duty-tab-content" id="tab-on-break" style="display: none;">
            <?php renderDutyTable($onBreakList, "No employees are currently on break.", "background: var(--amber-bg); color: var(--amber); border-color: var(--amber-border);", "On Break"); ?>
        </div>

        <!-- Tab 4: Half Day -->
        <div class="duty-tab-content" id="tab-half-day" style="display: none;">
            <?php renderDutyTable($halfDayList, "No employees took a half day today.", "background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border-color: rgba(139, 92, 246, 0.2);", "Half Day"); ?>
        </div>

        <!-- Tab 5: Out of Town -->
        <div class="duty-tab-content" id="tab-out-of-town" style="display: none;">
            <?php renderDutyTable($outOfTownList, "No employees are out of town today.", "background: rgba(6, 182, 212, 0.1); color: #06b6d4; border-color: rgba(6, 182, 212, 0.2);", "Out Of Town"); ?>
        </div>

        <!-- Tab 6: Day Off -->
        <div class="duty-tab-content" id="tab-day-off" style="display: none;">
            <?php renderDutyTable($dayOffList, "No employees are currently on day off.", "background: var(--surface-2); color: var(--text-secondary); border-color: var(--border);", "Day Off"); ?>
        </div>

        <!-- Tab 7: On Leave -->
        <div class="duty-tab-content" id="tab-on-leave" style="display: none;">
            <?php renderDutyTable($onLeaveList, "No employees are currently on leave.", "background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2);", "On Leave"); ?>
        </div>

        <!-- Tab 8: Overtime -->
        <div class="duty-tab-content" id="tab-overtime" style="display: none;">
            <?php renderDutyTable($overtimeList, "No employees are currently working overtime.", "background: var(--green-bg); color: var(--green); border-color: var(--green-border);", "Overtime (OT)"); ?>
        </div>
    </div>

    <!-- Switch Tab Script -->
    <script>
    function switchDutyTab(tabName) {
        // Hide all contents
        document.querySelectorAll('.duty-tab-content').forEach(function(el) {
            el.style.display = 'none';
        });
        // Deactivate all buttons
        document.querySelectorAll('.duty-tab-btn').forEach(function(el) {
            el.style.background = '';
            el.style.color = '';
        });
        
        // Show selected tab content
        var targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.style.display = 'block';
        }
        
        // Activate selected button styling
        const activeBtn = document.getElementById('btn-tab-' + tabName);
        if (activeBtn) {
            activeBtn.style.background = 'var(--brand)';
            activeBtn.style.color = '#fff';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        switchDutyTab('working');
    });
    </script>

    <!-- Display Team Members List for standard employees -->
    <?php if (!Auth::isHR() && !Auth::isAdmin() && !empty($teamMembers)): ?>
    <div class="card" style="margin-top: 24px;">
        <div class="card-header"><h2 class="card-title">Your Team Members (<?= count($teamMembers) ?> colleagues)</h2></div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Colleague</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teamMembers as $colleague): ?>
                    <tr>
                        <td>
                            <div class="table-emp">
                                <?php if (!empty($colleague['profile_picture'])): ?>
                                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($colleague['profile_picture']) ?>" class="emp-avatar emp-avatar-sm" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="emp-avatar emp-avatar-sm">
                                        <?= strtoupper(substr($colleague['first_name'], 0, 1) . substr($colleague['last_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <span class="emp-fullname"><?= htmlspecialchars($colleague['first_name'] . ' ' . $colleague['last_name']) ?></span>
                                    <span class="emp-email"><?= htmlspecialchars($colleague['email']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($colleague['position'] ?? '—') ?></td>
                        <td><span class="badge badge-<?= $colleague['status'] ?>"><?= ucfirst($colleague['status']) ?></span></td>
                        <td class="text-right"><a href="<?= BASE_URL ?>/employees/<?= $colleague['id'] ?>" class="btn btn-ghost btn-xs">View Profile</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>