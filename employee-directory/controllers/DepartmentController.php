<?php
// ============================================================
// controllers/DepartmentController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Department.php';
require_once BASE_PATH . '/helpers/CSRF.php';

class DepartmentController extends Controller {

    private Department $deptModel;

    public function __construct() {
        $this->deptModel = new Department();
    }

    // GET /departments
    public function index(): void {
        $this->requireAuth();

        $departments = $this->deptModel->allWithCount();
        $this->view('departments.index', [
            'title'       => 'Departments',
            'departments' => $departments,
        ]);
    }

    // POST /departments/store
    public function store(): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $name        = $this->input('name');
        $description = $this->input('description');

        $validator = new Validator(['name' => $name]);
        $validator->validate(['name' => 'required|max:100']);

        if ($validator->fails()) {
            $this->flash('error', $validator->firstError('name'));
            $this->redirect('/departments');
            return;
        }

        if ($this->deptModel->nameExists($name)) {
            $this->flash('error', 'A department with that name already exists.');
            $this->redirect('/departments');
            return;
        }

        $this->deptModel->create(['name' => $name, 'description' => $description]);
        $this->flash('success', "Department '{$name}' created.");
        $this->redirect('/departments');
    }

    // POST /departments/{id}/update
    public function update(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $deptId      = (int)$id;
        $name        = $this->input('name');
        $description = $this->input('description');

        if ($this->deptModel->nameExists($name, $deptId)) {
            $this->flash('error', 'Another department already uses that name.');
            $this->redirect('/departments');
            return;
        }

        $this->deptModel->update($deptId, ['name' => $name, 'description' => $description]);
        $this->flash('success', 'Department updated.');
        $this->redirect('/departments');
    }

    // POST /departments/{id}/delete
    public function delete(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $deptId = (int)$id;

        if ($this->deptModel->hasEmployees($deptId)) {
            $this->flash('error', 'Cannot delete a department that still has employees.');
            $this->redirect('/departments');
            return;
        }

        $this->deptModel->delete($deptId);
        $this->flash('success', 'Department deleted.');
        $this->redirect('/departments');
    }
}
