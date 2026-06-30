<?php
// ============================================================
// controllers/EmployeeController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Employee.php';
require_once BASE_PATH . '/models/Department.php';
require_once BASE_PATH . '/helpers/CSRF.php';
require_once BASE_PATH . '/models/User.php';

class EmployeeController extends Controller {

    private Employee   $employeeModel;
    private Department $deptModel;
    private User       $userModel;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->deptModel     = new Department();
        $this->userModel     = new User();
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
        $empId = (int)$id;

        // Fetch all the different pieces of information
        $employee     = $this->employeeModel->findWithDepartment($empId);
        
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $education    = $this->employeeModel->getEducation($empId);
        $skills       = $this->employeeModel->getSkills($empId);
        $experiences  = $this->employeeModel->getExperiences($empId);
        $certificates = $this->employeeModel->getCertificates($empId);
        $family       = $this->employeeModel->getFamily($empId);
        $achievements = $this->employeeModel->getAchievements($empId);

        // Security check for who is viewing (Owner or HR/Admin)
      // Security check for who is viewing (Owner or HR/Admin)
        $isOwner = ((int)$employee['user_id'] === Auth::id());
        $canEdit = (Auth::isHR() || $isOwner);

        $this->view('employees.show', [
            'title'        => $employee['first_name'] . ' ' . $employee['last_name'],
    
            'employee'     => $employee,
            'education'    => $education,
            'skills'       => $skills,
            'experiences'  => $experiences,
            'certificates' => $certificates,
            'family'       => $family,
            'achievements' => $achievements,
            'canEdit'      => $canEdit,
            'isOwner'      => $isOwner   // <--- ADD THIS EXACT LINE
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
            'bio'             => $this->input('bio'),
            'quote'           => $this->input('quote'),
            'barangay'        => $this->input('barangay'),
            'city'            => $this->input('city'),
            'hobbies'         => $this->input('hobbies'),
            'strengths'       => $this->input('strengths'),
            'weaknesses'      => $this->input('weaknesses'),
            'availability_status' => $this->input('availability_status', 'available'),
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

        // Process profile picture upload if any
        $uploadResult = $this->handleImageUpload();
        if ($uploadResult === false) {
            $this->view('employees.create', [
                'title'       => 'Add Employee',
                'departments' => $this->deptModel->all('name', 'ASC'),
                'nextNumber'  => $data['employee_number'],
                'errors'      => ['profile_picture' => ['Invalid image. Must be JPG/PNG/WEBP under 2MB.']],
                'old'         => $data,
            ]);
            return;
        } elseif ($uploadResult !== null) {
            $data['profile_picture'] = $uploadResult;
        }

        // 1. Generate a secure QR Token and a username
        $loginToken = bin2hex(random_bytes(20));
        $username = strtolower(explode('@', $data['email'])[0]) . rand(10, 99);

        // 2. Automatically create the User account
        $userId = $this->userModel->create([
            'role_id'     => 3, // Role ID 3 is 'employee'
            'username'    => $username,
            'email'       => $data['email'],
            'password'    => Auth::hash('Employee@123'), // Default Password given to new employees
            'login_token' => $loginToken
        ]);

        // 3. Link the new user account to the employee profile, then save the employee
        $data['user_id'] = $userId;
        $this->employeeModel->create($data);
        $this->flash('success', 'Employee added successfully.');
        $this->redirect('/employees');
    }

    // GET /employees/{id}/edit
   // GET /employees/{id}/edit
    public function edit(string $id): void {
        $this->requireAuth();
        
        $empId = (int)$id;
        $employee = $this->employeeModel->find($empId);
        
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // ROLE-BASED ACCESS CONTROL (RBAC): 
        // Allow if user is HR/Admin OR if the logged-in user owns this employee profile.
        $isOwner = ((int)$employee['user_id'] === Auth::id());
        if (!Auth::isHR() && !$isOwner) {
            $this->flash('error', 'Access Denied: You can only edit your own profile.');
            $this->redirect('/employees/' . $empId);
            return;
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
        CSRF::protect();

        $empId = (int)$id;
        $employee = $this->employeeModel->find($empId);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // ROLE-BASED ACCESS CONTROL (RBAC) FOR SAVING
        $isOwner = ((int)$employee['user_id'] === Auth::id());
        if (!Auth::isHR() && !$isOwner) {
            $this->flash('error', 'Access Denied: You can only update your own profile.');
            $this->redirect('/employees');
            return;
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
            'address'       => $this->input('address') ?: $employee['address'],
            'bio'           => $this->input('bio'),
            'quote'         => $this->input('quote'),
            'barangay'      => $this->input('barangay') ?: $employee['barangay'],
            'city'          => $this->input('city') ?: $employee['city'],
            'hobbies'       => $this->input('hobbies'),
            'strengths'     => $this->input('strengths'),
            'weaknesses'    => $this->input('weaknesses'),
            'availability_status' => $this->input('availability_status') ?: $employee['availability_status'],
        ];

        $uploadResult = $this->handleImageUpload();
        if ($uploadResult === false) {
            $this->flash('error', 'Invalid image. Must be JPG/PNG/WEBP under 2MB.');
            $this->redirect('/employees/' . $empId . '/edit');
            return;
        } elseif ($uploadResult !== null) {
            $data['profile_picture'] = $uploadResult;
        }

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
    
   // GET /employees/export
    public function export(): void {
        $this->requireAuth();

        // Fetch all employees with their department names using your existing model method
        $employees = $this->employeeModel->allWithDepartment();

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="employees_export_' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, ['ID', 'Employee Number', 'First Name', 'Last Name', 'Email', 'Position', 'Department', 'Status', 'Hire Date']);

        // Add data rows
        foreach ($employees as $emp) {
            fputcsv($output, [
                $emp['id'],
                $emp['employee_number'],
                $emp['first_name'],
                $emp['last_name'],
                $emp['email'],
                $emp['position'] ?? '—',
                $emp['department_name'] ?? '—',
                $emp['status'],
                $emp['hire_date'] ?? '—'
            ]);
        }

        fclose($output);
        exit;
    }

    // Helper to process profile picture uploads
    private function handleImageUpload(): string|bool|null {
        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file uploaded
        }
        
        $file = $_FILES['profile_picture'];
        if ($file['error'] !== UPLOAD_ERR_OK) return false;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > 2 * 1024 * 1024) {
            return false; // Invalid type or too large
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profile_') . '.' . $ext;
        $destPath = BASE_PATH . '/public/assets/images/profiles';

        if (!is_dir($destPath)) mkdir($destPath, 0777, true);

        if (move_uploaded_file($file['tmp_name'], $destPath . '/' . $filename)) {
            return $filename;
        }
        return false;
    }

    // POST /employees/{id}/experience
    public function addExperience(string $id): void {
        $this->requireAuth();
        CSRF::protect();
        $empId = (int)$id;

        $employee = $this->employeeModel->find($empId);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // ROLE-BASED ACCESS CONTROL (RBAC)
        $isOwner = ((int)$employee['user_id'] === Auth::id());
        if (!Auth::isHR() && !$isOwner) {
            $this->flash('error', 'Access Denied: You cannot add experiences to this profile.');
            $this->redirect('/employees/' . $empId);
            return;
        }

        // Gather Data
        $companyName = $this->input('company_name');
        $position    = $this->input('position');
        $startDate   = $this->input('start_date');
        $endDate     = $this->input('end_date') ?: null; // Null if empty (Present)
        $description = $this->input('description');

        // Insert directly into the new table
      // Call the public method on the model
        $this->employeeModel->addExperienceRecord($empId, $companyName, $position, $startDate, $endDate, $description);

        $this->flash('success', 'Professional experience added successfully.');
        $this->redirect('/employees/' . $empId);
    }


    // POST /employees/{id}/education
    public function addEducation(string $id): void {
        $this->requireAuth();
        CSRF::protect();
        $empId = (int)$id;

        $employee = $this->employeeModel->find($empId);
        if (!$employee || (!Auth::isHR() && (int)$employee['user_id'] !== Auth::id())) {
            $this->flash('error', 'Access Denied or Employee not found.');
            $this->redirect('/employees/' . $empId);
            return;
        }

        $this->employeeModel->addEducationRecord(
            $empId, 
            $this->input('degree'), 
            $this->input('institution'), 
            $this->input('year_graduated')
        );

        $this->flash('success', 'Education record added.');
        $this->redirect('/employees/' . $empId);
    }

    // POST /employees/{id}/family
    public function addFamily(string $id): void {
        $this->requireAuth();
        CSRF::protect();
        $empId = (int)$id;

        $employee = $this->employeeModel->find($empId);
        if (!$employee || (!Auth::isHR() && (int)$employee['user_id'] !== Auth::id())) {
            $this->flash('error', 'Access Denied or Employee not found.');
            $this->redirect('/employees/' . $empId);
            return;
        }

        $this->employeeModel->addFamilyRecord(
            $empId, 
            $this->input('relation'), 
            $this->input('full_name'), 
            $this->input('contact_number') ?: null,
            $this->input('occupation') ?: null
        );

        $this->flash('success', 'Family record added.');
        $this->redirect('/employees/' . $empId);
    }

    // POST /employees/{id}/certificate
    public function addCertificate(string $id): void {
        $this->requireAuth();
        CSRF::protect();
        $empId = (int)$id;

        $employee = $this->employeeModel->find($empId);
        if (!$employee || (!Auth::isHR() && (int)$employee['user_id'] !== Auth::id())) {
            $this->flash('error', 'Access Denied or Employee not found.');
            $this->redirect('/employees/' . $empId);
            return;
        }

        $this->employeeModel->addCertificateRecord(
            $empId, 
            $this->input('certificate_name'), 
            $this->input('issuing_organization'), 
            $this->input('issue_date')
        );

        $this->flash('success', 'Certificate added.');
        $this->redirect('/employees/' . $empId);
    }

    // GET /employees/{id}/id-card
   // GET /employees/{id}/id-card
  // GET /employees/{id}/id-card
    public function idCard(string $id): void {
        $this->requireAuth();
        $empId = (int)$id;

        $employee = $this->employeeModel->findWithDepartment($empId);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // Fetch the linked user account
        $user = $employee['user_id'] ? $this->userModel->find($employee['user_id']) : null;
        
        // RETROACTIVE FIX: If employee has no account, or no token, generate it now!
        if (!$user || empty($user['login_token'])) {
            $loginToken = bin2hex(random_bytes(20));
            
            if (!$user) {
                // Auto-create missing user account for older employees
                $username = strtolower(explode('@', $employee['email'])[0]) . rand(10, 99);
                
                // Fallback if username somehow exists
                if ($this->userModel->usernameExists($username)) {
                    $username .= rand(100, 999);
                }

                $userId = $this->userModel->create([
                    'role_id'     => 3, // Employee role
                    'username'    => $username,
                    'email'       => $employee['email'],
                    'password'    => Auth::hash('Employee@123'),
                    'login_token' => $loginToken
                ]);
                
                // Link the newly created user back to the employee profile
                $this->employeeModel->update($empId, ['user_id' => $userId]);
            } else {
                // User exists, but they just need a token updated
                $this->userModel->update($user['id'], ['login_token' => $loginToken]);
            }
            $qrToken = $loginToken;
        } else {
            $qrToken = $user['login_token'];
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $qrLink = $qrToken ? $protocol . $host . BASE_URL . '/qr-login?token=' . $qrToken : '';

        $this->view('employees.id_card', [
            'layout'   => 'print',
            'title'    => 'ID Card - ' . $employee['first_name'],
            'employee' => $employee,
            'qrLink'   => $qrLink
        ]);
    }

    // GET /employees/print-bulk
    public function printBulk(): void {
        $this->requireAuth();
        $this->requireRole('admin', 'hr_staff');

        $idsString = $this->query('ids', '');
        if (empty($idsString)) {
            $this->flash('error', 'No employees selected for bulk print.');
            $this->redirect('/employees');
            return;
        }

        $ids = array_map('intval', explode(',', $idsString));
        $employees = [];
        
        foreach ($ids as $empId) {
            $employee = $this->employeeModel->findWithDepartment($empId);
            if ($employee) {
                // Fetch user account to get login token
                $user = $employee['user_id'] ? $this->userModel->find($employee['user_id']) : null;
                
                // RETROACTIVE FIX: If employee has no account, or no token, generate it now!
                if (!$user || empty($user['login_token'])) {
                    $loginToken = bin2hex(random_bytes(20));
                    
                    if (!$user) {
                        $username = strtolower(explode('@', $employee['email'])[0]) . rand(10, 99);
                        if ($this->userModel->usernameExists($username)) {
                            $username .= rand(100, 999);
                        }
                        $userId = $this->userModel->create([
                            'role_id'     => 3,
                            'username'    => $username,
                            'email'       => $employee['email'],
                            'password'    => Auth::hash('Employee@123'),
                            'login_token' => $loginToken
                        ]);
                        $this->employeeModel->update($empId, ['user_id' => $userId]);
                    } else {
                        $this->userModel->update($user['id'], ['login_token' => $loginToken]);
                    }
                    $qrToken = $loginToken;
                } else {
                    $qrToken = $user['login_token'];
                }

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                $employee['qrLink'] = $qrToken ? $protocol . $host . BASE_URL . '/qr-login?token=' . $qrToken : '';
                
                $employees[] = $employee;
            }
        }

        $this->view('employees.print_bulk', [
            'layout'    => 'print',
            'title'     => 'Bulk ID Cards Printing',
            'employees' => $employees,
        ]);
    }

    // POST /employees/{id}/skills
    public function addSkill(string $id): void {
        $this->requireAuth();
        CSRF::protect();
        $empId = (int)$id;

        $employee = $this->employeeModel->find($empId);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // Access check
        $isOwner = ((int)$employee['user_id'] === Auth::id());
        if (!Auth::isHR() && !$isOwner) {
            $this->flash('error', 'Access Denied: You cannot add skills to this profile.');
            $this->redirect('/employees/' . $empId);
            return;
        }

        $skillName   = $this->input('skill_name');
        $proficiency = $this->input('proficiency', 'intermediate');

        $validator = new Validator(['skill_name' => $skillName]);
        $validator->validate(['skill_name' => 'required|max:100']);

        if ($validator->fails()) {
            $this->flash('error', $validator->firstError('skill_name'));
            $this->redirect('/employees/' . $empId);
            return;
        }

        $this->employeeModel->addSkillRecord($empId, $skillName, $proficiency);
        $this->flash('success', 'Skill added successfully.');
        $this->redirect('/employees/' . $empId);
    }
}
