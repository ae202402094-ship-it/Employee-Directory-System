<div class="page-toolbar" style="margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>/employees" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Directory
    </a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Register New Employee</h2>
        </div>
        <div class="card-body">
            <?php include __DIR__ . '/_form.php'; ?>
        </div>
    </div>
</div>

<script>
// Define center coordinates for each Barangay
const barangayCoords = {
    "Tetuan": [6.9155, 122.0722],
    "Pasonanca": [6.9350, 122.0680],
    "Guiwan": [6.9120, 122.0850]
};

function updateMapLocation() {
    const brgy = document.getElementById('barangaySelect').value;
    if (barangayCoords[brgy]) {
        const [lat, lng] = barangayCoords[brgy];
        
        // 1. Update hidden inputs if you have them
        // 2. If you are in the Create/Edit form, you might want to 
        //    show a mini-map preview that centers on this choice
        console.log("Map will center to: " + lat + ", " + lng);
        
        // Optional: If you use Leaflet in your form, trigger a map update here
        if (typeof map !== 'undefined') {
            map.setView([lat, lng], 15);
        }
    }
}
</script>