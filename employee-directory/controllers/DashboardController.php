<?php
// ============================================================
// controllers/DashboardController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/Department.php';
require_once BASE_PATH . '/models/User.php';

class DashboardController extends Controller {

    private Employee   $employeeModel;
    private Department $deptModel;
    private User       $userModel;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->deptModel     = new Department();
        $this->userModel     = new User();
    }

    // GET /dashboard
    public function index(): void {
        $this->requireAuth();

        $stats = [
            'total_employees'  => $this->employeeModel->count(),
            'active_employees' => $this->employeeModel->countByStatus('active'),
            'total_depts'      => $this->deptModel->count(),
            'new_this_month'   => $this->employeeModel->countNewThisMonth(),
        ];

        $deptBreakdown  = $this->employeeModel->countPerDepartment();
        $recentEmployees = $this->employeeModel->recent(5);

        $this->view('dashboard.index', [
            'title'           => 'Dashboard',
            'stats'           => $stats,
            'deptBreakdown'   => $deptBreakdown,
            'recentEmployees' => $recentEmployees,
        ]);
    }
}
