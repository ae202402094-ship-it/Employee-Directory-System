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

        $employee = $this->employeeModel->findByUserId(Auth::id());
        $deptBreakdown = $this->employeeModel->countPerDepartment();

        // Fetch lists for duty tracking
        $workingList   = $this->employeeModel->getEmployeesByAvailability('available');
        $lateList      = $this->employeeModel->getEmployeesByAvailability('late');
        $absentList    = $this->employeeModel->getEmployeesByAvailability('absent');
        $onBreakList   = $this->employeeModel->getEmployeesByAvailability('on-break');
        $halfDayList   = $this->employeeModel->getEmployeesByAvailability('half-day');
        $outOfTownList = $this->employeeModel->getEmployeesByAvailability('out-of-town');
        $dayOffList    = $this->employeeModel->getEmployeesByAvailability('day-off');
        $onLeaveList   = $this->employeeModel->getEmployeesOnLeave();
        $overtimeList  = $this->employeeModel->getEmployeesByAvailability('overtime');

        if (Auth::isHR() || Auth::isAdmin()) {
            // Admin & HR full dashboard view
            $stats = [
                'total_employees'  => $this->employeeModel->count(),
                'active_employees' => $this->employeeModel->countByStatus('active'),
                'total_depts'      => $this->deptModel->count(),
                'new_this_month'   => $this->employeeModel->countNewThisMonth(),
            ];
            $recentEmployees = $this->employeeModel->recent(5);
            $locations       = $this->sanitizeLocations($this->employeeModel->getAllLocations());

            $this->view('dashboard.index', [
                'title'           => 'Dashboard',
                'stats'           => $stats,
                'deptBreakdown'   => $deptBreakdown,
                'recentEmployees' => $recentEmployees,
                'locations'       => $locations,
                'employee'        => $employee,
                'workingList'     => $workingList,
                'lateList'        => $lateList,
                'absentList'      => $absentList,
                'onBreakList'     => $onBreakList,
                'halfDayList'     => $halfDayList,
                'outOfTownList'   => $outOfTownList,
                'dayOffList'      => $dayOffList,
                'onLeaveList'     => $onLeaveList,
                'overtimeList'    => $overtimeList,
            ]);
        } elseif (Auth::isDeptHead()) {
            // Manager & Supervisor dashboard view
            $stats = [
                'total_employees'  => 0,
                'active_employees' => 0,
            ];
            $recentEmployees = [];
            $locations       = [];
            $deptName        = 'No Department';

            if ($employee && $employee['department_id']) {
                $deptId = $employee['department_id'];
                $deptName = $employee['department_name'];

                $deptStats = $this->employeeModel->getDepartmentStats($deptId);
                $stats['total_employees'] = $deptStats['total_employees'];
                $stats['active_employees'] = $deptStats['active_employees'];

                $recentEmployees = $this->employeeModel->getRecentByDepartment($deptId, 5);
                $locations = $this->sanitizeLocations($this->employeeModel->getLocationsByDepartment($deptId));
            }

            $this->view('dashboard.index', [
                'title'           => 'Department Dashboard',
                'stats'           => $stats,
                'deptBreakdown'   => $deptBreakdown,
                'recentEmployees' => $recentEmployees,
                'locations'       => $locations,
                'deptName'        => $deptName,
                'employee'        => $employee,
                'workingList'     => $workingList,
                'lateList'        => $lateList,
                'absentList'      => $absentList,
                'onBreakList'     => $onBreakList,
                'halfDayList'     => $halfDayList,
                'outOfTownList'   => $outOfTownList,
                'dayOffList'      => $dayOffList,
                'onLeaveList'     => $onLeaveList,
                'overtimeList'    => $overtimeList,
            ]);
        } else {
            // Employee dashboard view
            $teamMembers = [];
            $locations   = [];
            $deptName    = 'No Department';

            if ($employee && $employee['department_id']) {
                $deptId = $employee['department_id'];
                $deptName = $employee['department_name'];

                $teamMembers = $this->employeeModel->getTeamMembers($deptId, $employee['id']);
                $locations = $this->sanitizeLocations($this->employeeModel->getLocationsByDepartment($deptId));
            }

            $this->view('dashboard.index', [
                'title'         => 'Employee Dashboard',
                'teamMembers'   => $teamMembers,
                'deptBreakdown' => $deptBreakdown,
                'locations'     => $locations,
                'deptName'      => $deptName,
                'employee'      => $employee,
                'workingList'     => $workingList,
                'lateList'        => $lateList,
                'absentList'      => $absentList,
                'onBreakList'     => $onBreakList,
                'halfDayList'     => $halfDayList,
                'outOfTownList'   => $outOfTownList,
                'dayOffList'      => $dayOffList,
                'onLeaveList'     => $onLeaveList,
                'overtimeList'    => $overtimeList,
            ]);
        }
    }

    // Helper to sanitize employee locations for privacy-restricted statuses
    private function sanitizeLocations(array $locations): array {
        $privacyStatuses = ['on-leave', 'vacation', 'holiday', 'day-off', 'absent'];
        foreach ($locations as $k => $loc) {
            if (in_array($loc['availability_status'], $privacyStatuses)) {
                $isOwner = ((int)($loc['user_id'] ?? 0) === Auth::id());
                // Only Admin or the employee themselves can see the location during leaves
                if (!Auth::isAdmin() && !$isOwner) {
                    unset($locations[$k]);
                }
            }
        }
        return array_values($locations);
    }
}