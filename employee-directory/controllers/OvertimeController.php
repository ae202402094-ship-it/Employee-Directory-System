<?php
// ============================================================
// controllers/OvertimeController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/OvertimeLog.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/helpers/Auth.php';

class OvertimeController extends Controller {

    public function index(): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $employee = $empModel->findByUserId(Auth::id());
        $isHRDept = $employee && (int)$employee['department_id'] === 2;
        $otModel = new OvertimeLog();
        
        $pendingLogs = [];
        $historyLogs = [];
        
        if (Auth::isAdmin() || Auth::isHR() || $isHRDept) {
            $pendingLogs = $otModel->getPendingLogs();
            $historyLogs = $otModel->getAllHistory();
        } else {
            if ($employee) {
                if (Auth::isDeptHead()) {
                    $deptId = $employee['department_id'];
                    $pendingLogs = $otModel->query(
                        "SELECT o.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name 
                         FROM overtime_logs o
                         LEFT JOIN employees e ON e.id = o.employee_id
                         LEFT JOIN departments d ON d.id = e.department_id
                         WHERE o.status = 'pending' AND e.department_id = ?
                         ORDER BY o.created_at ASC",
                        [$deptId]
                    );
                    $historyLogs = $otModel->query(
                        "SELECT o.*, e.first_name, e.last_name, e.profile_picture, d.name AS department_name, u.username AS approver_name
                         FROM overtime_logs o
                         LEFT JOIN employees e ON e.id = o.employee_id
                         LEFT JOIN departments d ON d.id = e.department_id
                         LEFT JOIN users u ON u.id = o.approved_by
                         WHERE e.department_id = ?
                         ORDER BY o.ot_date DESC",
                        [$deptId]
                    );
                } else {
                    $historyLogs = $otModel->getByEmployee($employee['id']);
                }
            }
        }

        $this->view('overtime.index', [
            'title'        => 'Overtime Manager',
            'employee'     => $employee,
            'pendingLogs'  => $pendingLogs,
            'historyLogs'  => $historyLogs
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $employee = $empModel->findByUserId(Auth::id());
        
        if (!$employee) {
            $this->redirect('/dashboard');
        }

        $date   = $this->input('ot_date');
        $hours  = (float)$this->input('hours');
        $reason = $this->input('reason');

        if (!$date || !$hours || !$reason) {
            $this->redirect('/overtime');
        }

        $otModel = new OvertimeLog();
        $success = $otModel->logHours($employee['id'], $date, $hours, $reason);

        if ($success) {
            ActivityLog::log('LOG_OVERTIME', "Logged {$hours} overtime hours for {$date}");
        }

        $this->redirect('/overtime');
    }

    public function approve(string $id): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $approverEmp = $empModel->findByUserId(Auth::id());
        $isHRDept = $approverEmp && (int)$approverEmp['department_id'] === 2;

        if (!Auth::isAdmin() && !Auth::isHR() && !Auth::isDeptHead() && !$isHRDept) {
            $this->redirect('/overtime');
        }

        $otModel = new OvertimeLog();
        $log = $otModel->find((int)$id);
        if (!$log) {
            $this->redirect('/overtime');
        }

        $targetEmp = $empModel->find($log['employee_id']);
        if (!$targetEmp || !$this->canApprove($targetEmp)) {
            $this->flash('error', 'You are not authorized to approve this employee\'s request.');
            $this->redirect('/overtime');
            return;
        }

        $success = $otModel->updateStatus((int)$id, 'approved', Auth::id());

        if ($success) {
            $empModel->updateAvailabilityStatus($log['employee_id'], 'overtime');
            
            ActivityLog::log('APPROVE_OVERTIME', "Approved overtime hours for employee #{$log['employee_id']}");
        }

        $this->redirect('/overtime');
    }

    public function reject(string $id): void {
        $this->requireAuth();
        
        $empModel = new Employee();
        $approverEmp = $empModel->findByUserId(Auth::id());
        $isHRDept = $approverEmp && (int)$approverEmp['department_id'] === 2;

        if (!Auth::isAdmin() && !Auth::isHR() && !Auth::isDeptHead() && !$isHRDept) {
            $this->redirect('/overtime');
        }

        $otModel = new OvertimeLog();
        $log = $otModel->find((int)$id);
        if (!$log) {
            $this->redirect('/overtime');
        }

        $targetEmp = $empModel->find($log['employee_id']);
        if (!$targetEmp || !$this->canApprove($targetEmp)) {
            $this->flash('error', 'You are not authorized to reject this employee\'s request.');
            $this->redirect('/overtime');
            return;
        }

        $success = $otModel->updateStatus((int)$id, 'rejected', Auth::id());

        if ($success) {
            ActivityLog::log('REJECT_OVERTIME', "Rejected overtime hours for employee #{$log['employee_id']}");
        }

        $this->redirect('/overtime');
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
        if ((int)($targetEmp['user_id'] ?? 0) === (int)Auth::id()) {
            return false;
        }

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
