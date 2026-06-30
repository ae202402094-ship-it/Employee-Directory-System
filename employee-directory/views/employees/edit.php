<!-- views/employees/edit.php -->

<div class="page-toolbar">
    <a href="<?= BASE_URL ?>/employees/<?= $employee['id'] ?>" class="btn btn-ghost btn-sm">← Back to Profile</a>
</div>

<div class="form-layout">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit: <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
        </div>
        <div class="card-body">
            <?php
            // Normalize $old from employee data for the shared form
            $old = $old ?? $employee;
            $nextNumber = $employee['employee_number'];
            $isEdit = true;
            include __DIR__ . '/_form.php';
            ?>
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