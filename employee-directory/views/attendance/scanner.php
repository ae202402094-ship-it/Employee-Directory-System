<div class="container" style="max-width: 600px; padding: 24px;">
    
    <div class="card">
        <div class="card-header" style="text-align: center;">
            <h2 class="card-title">QR Code Check-in / Check-out Scanner</h2>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Hold your physical ID card QR code in front of the camera</div>
        </div>
        <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            
            <!-- GPS Status Tracker -->
            <div id="gps-status" style="width: 100%; padding: 12px; border-radius: var(--r-md); background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #3b82f6; font-size: 13px; text-align: center; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span class="pulse-marker" style="display:inline-block; width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span>
                <span>Acquiring Geolocation Coordinates...</span>
            </div>

            <!-- Scanner Box Frame -->
            <div style="position: relative; width: 100%; max-width: 380px; aspect-ratio: 1; border-radius: var(--r-lg); overflow: hidden; background: #000; border: 4px solid var(--border); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                <!-- Camera view container -->
                <div id="reader" style="width: 100%; height: 100%;"></div>
                
                <!-- Overlay Frame Guide -->
                <div style="position: absolute; inset: 40px; border: 3px dashed rgba(255,255,255,0.4); border-radius: var(--r-md); pointer-events: none; z-index: 10; display: flex; align-items: center; justify-content: center;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.6); font-weight: 700; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px;">Align QR Code Here</div>
                </div>
            </div>

            <!-- Feedback Notifications -->
            <div id="scan-feedback" style="width: 100%; margin-top: 20px; display: none;"></div>

            <div style="width: 100%; border-top: 1px solid var(--border); margin-top: 24px; padding-top: 20px; text-align: center;">
                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Having camera issues? Input Employee Number or ID manually:</div>
                <div style="display: flex; gap: 8px; max-width: 380px; margin: 0 auto;">
                    <input type="text" id="manual-emp-id" class="form-control" placeholder="e.g. EMP-2026-0001 or 1">
                    <button type="button" onclick="submitManualCheckIn()" class="btn btn-secondary">Submit</button>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Load HTML5 QR Code library via secure CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    var currentLat = null;
    var currentLng = null;
    var html5QrcodeScanner = null;
    var isProcessing = false;

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Request Geolocation Coordinates
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                function(position) {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;
                    
                    var gpsStatus = document.getElementById('gps-status');
                    gpsStatus.style.background = 'rgba(16, 185, 129, 0.1)';
                    gpsStatus.style.borderColor = 'rgba(16, 185, 129, 0.2)';
                    gpsStatus.style.color = '#10b981';
                    gpsStatus.querySelector('.pulse-marker').style.background = '#10b981';
                    gpsStatus.querySelector('span:last-child').textContent = 'GPS Synchronized: Ready inside Office Area';
                },
                function(error) {
                    var gpsStatus = document.getElementById('gps-status');
                    gpsStatus.style.background = 'rgba(245, 158, 11, 0.1)';
                    gpsStatus.style.borderColor = 'rgba(245, 158, 11, 0.2)';
                    gpsStatus.style.color = '#f59e0b';
                    gpsStatus.querySelector('.pulse-marker').style.background = '#f59e0b';
                    gpsStatus.querySelector('span:last-child').textContent = 'GPS Unavailable. Geofence defaults active.';
                },
                { enableHighAccuracy: true }
            );
        }

        // 2. Initialize Camera Scanner
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        var config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            config, 
            onScanSuccess
        ).catch(function(err) {
            console.error("Camera access failed:", err);
        });
    });

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        // Play scanner beep sound
        try {
            var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            osc.frequency.value = 1000;
            osc.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.1);
        } catch(e) {}

        // Check if token scan
        var tokenMatch = decodedText.match(/[?&]token=([a-f0-9]+)/i);
        if (tokenMatch) {
            var token = tokenMatch[1];
            performCheckIn(null, token);
        } else {
            // Check if employee ID URL match
            var match = decodedText.match(/\/employees\/(\d+)/);
            var empId = match ? parseInt(match[1]) : parseInt(decodedText);

            if (isNaN(empId)) {
                showFeedback("Invalid QR Code payload detected.", "danger");
                setTimeout(function() { isProcessing = false; }, 3000);
                return;
            }
            performCheckIn(empId, null);
        }
    }

    function performCheckIn(employeeId, token, employeeNumber) {
        showFeedback("Syncing with Attendance Server...", "info");

        var payload = {
            lat: currentLat,
            lng: currentLng
        };
        if (employeeId) {
            payload.employee_id = employeeId;
        }
        if (token) {
            payload.token = token;
        }
        if (employeeNumber) {
            payload.employee_number = employeeNumber;
        }

        fetch('<?= BASE_URL ?>/attendance/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(function(res) {
            return res.json().then(function(data) {
                if (!res.ok) {
                    throw new Error(data.error || "Server transaction error");
                }
                return data;
            });
        })
        .then(function(data) {
            showFeedback(data.message, "success");
            // Highlight checking success
            setTimeout(function() {
                isProcessing = false;
            }, 4000);
        })
        .catch(function(err) {
            showFeedback(err.message, "danger");
            setTimeout(function() {
                isProcessing = false;
            }, 4000);
        });
    }

    function submitManualCheckIn() {
        var empVal = document.getElementById('manual-emp-id').value.trim();
        if (!empVal) {
            alert("Please input a valid Employee Number or ID.");
            return;
        }
        performCheckIn(null, null, empVal);
    }

    function showFeedback(text, type) {
        var box = document.getElementById('scan-feedback');
        box.style.display = 'block';
        box.className = '';
        
        var bg = 'rgba(59, 130, 246, 0.15)';
        var textCol = '#3b82f6';
        if (type === 'success') {
            bg = 'rgba(16, 185, 129, 0.15)';
            textCol = '#10b981';
        } else if (type === 'danger') {
            bg = 'rgba(239, 68, 68, 0.15)';
            textCol = '#ef4444';
        }

        box.style.background = bg;
        box.style.color = textCol;
        box.style.padding = '12px 16px';
        box.style.borderRadius = 'var(--r-md)';
        box.style.fontSize = '13.5px';
        box.style.fontWeight = '600';
        box.style.textAlign = 'center';
        box.textContent = text;
    }
</script>

<style>
    .pulse-marker {
        animation: marker-pulse 2s infinite;
    }
    @keyframes marker-pulse {
        0% { transform: scale(0.95); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.5; }
    }
    /* Hide default camera select button inside html5-qrcode library */
    #reader button {
        background: var(--brand) !important;
        border: none !important;
        color: #fff !important;
        padding: 6px 12px !important;
        border-radius: var(--r-md) !important;
        cursor: pointer;
        font-family: inherit;
        font-weight: 600;
        font-size: 13px;
    }
</style>
