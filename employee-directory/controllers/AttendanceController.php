<?php
// ============================================================
// controllers/AttendanceController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/AttendanceLog.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/OfficeSetting.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/helpers/Auth.php';

class AttendanceController extends Controller {

    // GET /attendance/scanner
    public function scanner(): void {
        $this->requireAuth();
        $this->view('attendance.scanner', [
            'title' => 'Workforce QR Attendance Scanner'
        ]);
    }

    public function checkIn(): void {
        $this->requireAuth();
        
        $json = file_get_contents('php://input');
        $payload = json_decode($json, true);
        
        $empModel = new Employee();
        $employee = null;

        if (isset($payload['token'])) {
            $token = $payload['token'];
            $employee = $empModel->findByLoginToken($token);
        } elseif (isset($payload['employee_number'])) {
            $empNum = trim($payload['employee_number']);
            if (is_numeric($empNum)) {
                $employee = $empModel->find((int)$empNum);
            }
            if (!$employee) {
                $employee = $empModel->findByEmployeeNumber($empNum);
            }
        } elseif (isset($payload['employee_id'])) {
            $employeeId = (int)$payload['employee_id'];
            $employee = $empModel->find($employeeId);
        }

        if (!$employee) {
            $this->json(['error' => 'Employee record or token match not found.'], 404);
            return;
        }

        $employeeId = (int)$employee['id'];

        // Distance & Geofencing Calculation (Haversine formula)
        $officeModel = new OfficeSetting();
        $office = $officeModel->getActiveSetting();

        $clientLat = isset($payload['lat']) ? (float)$payload['lat'] : null;
        $clientLng = isset($payload['lng']) ? (float)$payload['lng'] : null;

        $inRange = true;
        $distance = 0;

        if ($clientLat && $clientLng) {
            $earthRadius = 6371000; // in meters
            $latFrom = deg2rad($clientLat);
            $lonFrom = deg2rad($clientLng);
            $latTo = deg2rad((float)$office['latitude']);
            $lonTo = deg2rad((float)$office['longitude']);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            $distance = $angle * $earthRadius;

            if ($distance > (int)$office['radius_meters']) {
                $inRange = false;
            }
        }

        if (!$inRange) {
            $this->json([
                'error' => "Check-in blocked. You are outside the office boundary. (Distance: " . round($distance) . " meters)"
            ], 400);
            return;
        }

        // Determine if Present vs Late (Let's say standard start time is 9:00 AM)
        $currentTime = date('H:i:s');
        $status = 'present';
        if ($currentTime > '09:00:00') {
            $status = 'late';
        }

        $attModel = new AttendanceLog();
        
        // Check if already checked in today
        $todayLog = $attModel->getTodayLogByEmployee($employeeId);
        
        if ($todayLog) {
            if ($todayLog['check_out_time'] === null) {
                // Perform check out
                $attModel->checkOut($employeeId, date('Y-m-d H:i:s'));
                
                // Set status to day-off or offline
                $empModel->updateAvailabilityStatus($employeeId, 'day-off');
                
                ActivityLog::log('CHECK_OUT', "Checked out employee #{$employeeId} via QR scan");
                
                $this->json([
                    'success' => true,
                    'message' => "Goodbye " . $employee['first_name'] . "! Checked out successfully."
                ]);
            } else {
                $this->json(['error' => 'You have already checked in and checked out for today.'], 400);
            }
            return;
        }

        // Perform Check-in
        $deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown QR Device';
        $success = $attModel->checkIn($employeeId, date('Y-m-d H:i:s'), $status, $deviceInfo);

        if ($success) {
            $newStatus = ($status === 'late') ? 'late' : 'available';
            $empModel->updateAvailabilityStatus($employeeId, $newStatus);
            
            ActivityLog::log('CHECK_IN', "Checked in employee #{$employeeId} via QR scan ({$status})");

            $this->json([
                'success' => true,
                'message' => "Welcome " . $employee['first_name'] . "! Checked in successfully as: " . ucfirst($status)
            ]);
        } else {
            $this->json(['error' => 'Database log failure.'], 500);
        }
    }

    // GET /attendance/logs
    public function logs(): void {
        $this->requireAuth();
        
        $attModel = new AttendanceLog();
        $logs = [];
        
        $empModel = new Employee();
        $employee = $empModel->findByUserId(Auth::id());
        
        if (Auth::isAdmin() || Auth::isHR()) {
            $logs = $attModel->getAllLogs();
        } else {
            if ($employee) {
                if (Auth::isDeptHead()) {
                    $deptId = $employee['department_id'];
                    $logs = $attModel->getLogsByDepartment($deptId);
                } else {
                    $logs = $attModel->getByEmployee($employee['id']);
                }
            }
        }

        $this->view('attendance.logs', [
            'title' => 'Daily Attendance Registry',
            'logs'  => $logs
        ]);
    }
}
