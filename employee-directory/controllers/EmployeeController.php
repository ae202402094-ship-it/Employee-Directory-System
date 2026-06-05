<?php
// ============================================================
// controllers/EmployeeController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/Department.php';
require_once BASE_PATH . '/helpers/CSRF.php';

class EmployeeController extends Controller {

    private Employee   $employeeModel;
    private Department $deptModel;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->deptModel     = new Department();
    }

    // GET /employees
    public function index(): void {
        $this->requireAuth();

        $search     = $this->query('search', '');
        $status     = $this->query('status', '');
        $deptId     = (int)$this->query('department', 0);

        if ($search || $status || $deptId) {
            $employees = $this->employeeModel->search(
                $search,
                $status ?: null,
                $deptId ?: null
            );
        } else {
            $employees = $this->employeeModel->allWithDepartment();
        }

        $departments = $this->deptModel->all('name', 'ASC');

        $this->view('employees.index', [
            'title'       => 'Employees',
            'employees'   => $employees,
            'departments' => $departments,
            'filters'     => ['search' => $search, 'status' => $status, 'department' => $deptId],
        ]);
    }

    // GET /employees/search (AJAX)
    public function search(): void {
        $this->requireAuth();

        $term   = $this->query('q', '');
        $results = $this->employeeModel->search($term);

        $output = array_map(fn($e) => [
            'id'              => $e['id'],
            'name'            => $e['first_name'] . ' ' . $e['last_name'],
            'employee_number' => $e['employee_number'],
            'position'        => $e['position'],
            'department_name' => $e['department_name'] ?? '—',
            'status'          => $e['status'],
        ], $results);

        $this->json(['data' => $output]);
    }

    // GET /employees/{id}
    public function show(string $id): void {
        $this->requireAuth();

        $employee = $this->employeeModel->findWithDepartment((int)$id);

        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->view('employees.show', [
            'title'    => $employee['first_name'] . ' ' . $employee['last_name'],
            'employee' => $employee,
        ]);
    }

    // GET /employees/create
    public function create(): void {
        $this->requireAuth();
        $this->requireRole('admin', 'hr_staff');

        $departments = $this->deptModel->all('name', 'ASC');
        $nextNumber  = $this->employeeModel->nextEmployeeNumber();

        $this->view('employees.create', [
            'title'       => 'Add Employee',
            'departments' => $departments,
            'nextNumber'  => $nextNumber,
        ]);
    }

    // POST /employees/store
    public function store(): void {
        $this->requireAuth();
        $this->requireRole('admin', 'hr_staff');
        CSRF::protect();

        $data = [
            'employee_number' => $this->input('employee_number'),
            'first_name'      => $this->input('first_name'),
            'last_name'       => $this->input('last_name'),
            'email'           => $this->input('email'),
            'phone'           => $this->input('phone'),
            'position'        => $this->input('position'),
            'department_id'   => (int)$this->input('department_id') ?: null,
            'hire_date'       => $this->input('hire_date') ?: null,
            'status'          => $this->input('status', 'active'),
            'address'         => $this->input('address'),
        ];

        $validator = new Validator($data);
        $validator->validate([
            'employee_number' => 'required|max:20',
            'first_name'      => 'required|max:100',
            'last_name'       => 'required|max:100',
            'email'           => 'required|email|max:150',
            'phone'           => 'phone',
            'position'        => 'max:100',
            'status'          => 'in:active,inactive,terminated',
            'hire_date'       => 'date',
        ]);

        if ($validator->fails()) {
            $this->view('employees.create', [
                'title'       => 'Add Employee',
                'departments' => $this->deptModel->all('name', 'ASC'),
                'nextNumber'  => $data['employee_number'],
                'errors'      => $validator->errors(),
                'old'         => $data,
            ]);
            return;
        }

        // Check email uniqueness
        if ($this->employeeModel->emailExists($data['email'])) {
            $this->view('employees.create', [
                'title'       => 'Add Employee',
                'departments' => $this->deptModel->all('name', 'ASC'),
                'nextNumber'  => $data['employee_number'],
                'errors'      => ['email' => ['This email is already in use.']],
                'old'         => $data,
            ]);
            return;
        }

        $this->employeeModel->create($data);
        $this->flash('success', 'Employee added successfully.');
        $this->redirect('/employees');
    }

    // GET /employees/{id}/edit
    public function edit(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin', 'hr_staff');

        $employee = $this->employeeModel->find((int)$id);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->view('employees.edit', [
            'title'       => 'Edit Employee',
            'employee'    => $employee,
            'departments' => $this->deptModel->all('name', 'ASC'),
        ]);
    }

    // POST /employees/{id}/update
    public function update(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin', 'hr_staff');
        CSRF::protect();

        $empId = (int)$id;
        $employee = $this->employeeModel->find($empId);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $data = [
            'first_name'    => $this->input('first_name'),
            'last_name'     => $this->input('last_name'),
            'email'         => $this->input('email'),
            'phone'         => $this->input('phone'),
            'position'      => $this->input('position'),
            'department_id' => (int)$this->input('department_id') ?: null,
            'hire_date'     => $this->input('hire_date') ?: null,
            'status'        => $this->input('status', 'active'),
            'address'       => $this->input('address'),
        ];

        $validator = new Validator($data);
        $validator->validate([
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|max:150',
            'phone'      => 'phone',
            'status'     => 'in:active,inactive,terminated',
            'hire_date'  => 'date',
        ]);

        if ($validator->fails()) {
            $this->view('employees.edit', [
                'title'       => 'Edit Employee',
                'employee'    => array_merge($employee, $data),
                'departments' => $this->deptModel->all('name', 'ASC'),
                'errors'      => $validator->errors(),
            ]);
            return;
        }

        if ($this->employeeModel->emailExists($data['email'], $empId)) {
            $this->view('employees.edit', [
                'title'       => 'Edit Employee',
                'employee'    => array_merge($employee, $data),
                'departments' => $this->deptModel->all('name', 'ASC'),
                'errors'      => ['email' => ['This email is already in use.']],
            ]);
            return;
        }

        $this->employeeModel->update($empId, $data);
        $this->flash('success', 'Employee updated successfully.');
        $this->redirect('/employees/' . $empId);
    }

    // POST /employees/{id}/delete
    public function delete(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $this->employeeModel->delete((int)$id);
        $this->flash('success', 'Employee deleted.');
        $this->redirect('/employees');
    }
}
