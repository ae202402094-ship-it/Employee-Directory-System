<?php
// ============================================================
// controllers/LocationController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/helpers/Auth.php';

class LocationController extends Controller {
    
    // POST /location/sync (AJAX)
    public function sync(): void {
        $this->requireAuth();
        
        // Parse incoming JSON payload
        $json = file_get_contents('php://input');
        $payload = json_decode($json, true);

        if (!isset($payload['lat']) || !isset($payload['lng'])) {
            $this->json(['error' => 'Missing coordinate data'], 400);
        }

        $empModel = new Employee();
        
        // Find the employee record attached to the currently logged-in user
        $employee = $empModel->findBy('user_id', Auth::id());

        if (!$employee) {
            $this->json(['error' => 'No employee profile linked to this user account'], 404);
        }

        // Update the coordinates in the database
        $status = $payload['status'] ?? 'available';
        $success = $empModel->updateLocation($employee['id'], (float)$payload['lat'], (float)$payload['lng'], $status);

        if ($success) {
            $this->json(['success' => true, 'message' => 'Location synchronized successfully']);
        } else {
            $this->json(['error' => 'Database update failed'], 500);
        }
    }
}