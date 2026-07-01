<?php
// ============================================================
// controllers/LeaveController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/LeaveRequest.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/helpers/Auth.php';

class LeaveController extends Controller {

    public function index(): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $employee = $empModel->findByUserId(Auth::id());
        
        $leaveModel = new LeaveRequest();
        
        $pendingRequests = [];
        $requestsHistory = [];
        
        if (Auth::isAdmin() || Auth::isHR()) {
            $pendingRequests = $leaveModel->getPendingRequests();
            $requestsHistory = $leaveModel->getAllRequestsHistory();
        } else {
            if ($employee) {
                if (Auth::isDeptHead()) {
                    $deptId = $employee['department_id'];
                    $pendingRequests = $leaveModel->query(
                        "SELECT l.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name 
                         FROM leave_requests l
                         LEFT JOIN employees e ON e.id = l.employee_id
                         LEFT JOIN departments d ON d.id = e.department_id
                         WHERE l.status = 'pending' AND e.department_id = ?
                         ORDER BY l.created_at ASC",
                        [$deptId]
                    );
                    $requestsHistory = $leaveModel->query(
                        "SELECT l.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name, u.username AS approver_name
                         FROM leave_requests l
                         LEFT JOIN employees e ON e.id = l.employee_id
                         LEFT JOIN departments d ON d.id = e.department_id
                         LEFT JOIN users u ON u.id = l.approved_by
                         WHERE e.department_id = ?
                         ORDER BY l.created_at DESC",
                        [$deptId]
                    );
                } else {
                    $requestsHistory = $leaveModel->getByEmployee($employee['id']);
                }
            }
        }

        $this->view('leaves.index', [
            'title'           => 'Workforce Leave Manager',
            'employee'        => $employee,
            'pendingRequests' => $pendingRequests,
            'requestsHistory' => $requestsHistory
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $employee = $empModel->findByUserId(Auth::id());
        
        if (!$employee) {
            $this->redirect('/dashboard');
        }

        $type   = $this->input('leave_type');
        $start  = $this->input('start_date');
        $end    = $this->input('end_date');
        $reason = $this->input('reason');

        if (!$type || !$start || !$end || !$reason) {
            $this->redirect('/leaves');
        }

        $leaveModel = new LeaveRequest();
        $success = $leaveModel->fileRequest($employee['id'], $type, $start, $end, $reason);

        if ($success) {
            ActivityLog::log('FILE_LEAVE', "Filed leave request: {$type} from {$start} to {$end}");
        }

        $this->redirect('/leaves');
    }

    public function approve(string $id): void {
        $this->requireAuth();
        if (!Auth::isAdmin() && !Auth::isHR() && !Auth::isDeptHead()) {
            $this->redirect('/leaves');
        }

        $leaveModel = new LeaveRequest();
        $req = $leaveModel->find((int)$id);
        if (!$req) {
            $this->redirect('/leaves');
        }

        $empModel = new Employee();
        $targetEmp = $empModel->find($req['employee_id']);
        if (!$targetEmp || !$this->canApprove($targetEmp)) {
            $this->flash('error', 'You are not authorized to approve this employee\'s request.');
            $this->redirect('/leaves');
            return;
        }

        $success = $leaveModel->updateStatus((int)$id, 'approved', Auth::id());

        if ($success) {
            if ($req['leave_type'] === 'vacation') {
                $statusVal = 'vacation';
            } elseif ($req['leave_type'] === 'absent') {
                $statusVal = 'absent';
            } else {
                $statusVal = 'on-leave';
            }
            $empModel->updateAvailabilityStatus($req['employee_id'], $statusVal);
            
            ActivityLog::log('APPROVE_LEAVE', "Approved leave request for employee #{$req['employee_id']}: {$req['leave_type']}");
        }

        $this->redirect('/leaves');
    }

    public function reject(string $id): void {
        $this->requireAuth();
        if (!Auth::isAdmin() && !Auth::isHR() && !Auth::isDeptHead()) {
            $this->redirect('/leaves');
        }

        $leaveModel = new LeaveRequest();
        $req = $leaveModel->find((int)$id);
        if (!$req) {
            $this->redirect('/leaves');
        }

        $empModel = new Employee();
        $targetEmp = $empModel->find($req['employee_id']);
        if (!$targetEmp || !$this->canApprove($targetEmp)) {
            $this->flash('error', 'You are not authorized to reject this employee\'s request.');
            $this->redirect('/leaves');
            return;
        }

        $success = $leaveModel->updateStatus((int)$id, 'rejected', Auth::id());

        if ($success) {
            ActivityLog::log('REJECT_LEAVE', "Rejected leave request for employee #{$req['employee_id']}: {$req['leave_type']}");
        }

        $this->redirect('/leaves');
    }

    private function isHighPosition(string $position): bool {
        $pos = strtolower($position);
        $highKeywords = ['director', 'manager', 'supervisor', 'assistant', 'secretary', 'hr', 'executive', 'head', 'admin', 'president', 'vp', 'lead', 'chief'];
        foreach ($highKeywords as $kw) {
            if (str_contains($pos, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function canApprove(array $targetEmp): bool {
        if (Auth::isAdmin() || Auth::isHR()) {
            return true;
        }

        $empModel = new Employee();
        $approverEmp = $empModel->findByUserId(Auth::id());
        if (!$approverEmp) {
            return false;
        }

        $approverPosition = $approverEmp['position'] ?? '';
        $targetPosition = $targetEmp['position'] ?? '';

        // HR department staff can approve any request
        if ((int)$approverEmp['department_id'] === 2) {
            return true;
        }

        // Other approvers must hold a high position
        if (!$this->isHighPosition($approverPosition)) {
            return false;
        }

        // High position employees can be approved across departments
        if ($this->isHighPosition($targetPosition)) {
            return true;
        }

        // Regular employees can only be approved if they belong to the same department
        return (int)$approverEmp['department_id'] === (int)$targetEmp['department_id'];
    }
}
