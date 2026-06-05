<?php
// ============================================================
// controllers/UserController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/Auth.php';
require_once BASE_PATH . '/helpers/CSRF.php';

class UserController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // GET /users
    public function index(): void {
        $this->requireAuth();
        $this->requireRole('admin');

        $users = $this->userModel->allWithRoles();
        $roles = Database::getInstance()->query("SELECT * FROM roles ORDER BY id")->fetchAll();

        $this->view('users.index', [
            'title' => 'User Management',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    // GET /users/create
    public function create(): void {
        $this->requireAuth();
        $this->requireRole('admin');

        $roles = Database::getInstance()->query("SELECT * FROM roles ORDER BY id")->fetchAll();
        $this->view('users.create', [
            'title' => 'Create User',
            'roles' => $roles,
        ]);
    }

    // POST /users/store
    public function store(): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $data = [
            'role_id'  => (int)$this->input('role_id'),
            'username' => $this->input('username'),
            'email'    => $this->input('email'),
            'password' => $_POST['password'] ?? '',
        ];

        $validator = new Validator($data);
        $validator->validate([
            'role_id'  => 'required|numeric',
            'username' => 'required|min:3|max:100|alpha_dash',
            'email'    => 'required|email|max:150',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $roles = Database::getInstance()->query("SELECT * FROM roles ORDER BY id")->fetchAll();
            $this->view('users.create', [
                'title'  => 'Create User',
                'roles'  => $roles,
                'errors' => $validator->errors(),
                'old'    => $data,
            ]);
            return;
        }

        if ($this->userModel->emailExists($data['email'])) {
            $roles = Database::getInstance()->query("SELECT * FROM roles ORDER BY id")->fetchAll();
            $this->view('users.create', [
                'title'  => 'Create User',
                'roles'  => $roles,
                'errors' => ['email' => ['Email already in use.']],
                'old'    => $data,
            ]);
            return;
        }

        $this->userModel->create([
            'role_id'  => $data['role_id'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Auth::hash($data['password']),
        ]);

        $this->flash('success', "User '{$data['username']}' created.");
        $this->redirect('/users');
    }

    // GET /users/{id}/edit
    public function edit(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');

        $user  = $this->userModel->findWithRoleById((int)$id);
        $roles = Database::getInstance()->query("SELECT * FROM roles ORDER BY id")->fetchAll();

        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('/users');
        }

        $this->view('users.edit', [
            'title' => 'Edit User',
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    // POST /users/{id}/update
    public function update(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $userId = (int)$id;
        $user   = $this->userModel->find($userId);
        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('/users');
        }

        $update = [
            'role_id'   => (int)$this->input('role_id'),
            'username'  => $this->input('username'),
            'email'     => $this->input('email'),
            'is_active' => (int)(bool)$this->input('is_active'),
        ];

        $newPassword = $_POST['password'] ?? '';
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) {
                $this->flash('error', 'Password must be at least 8 characters.');
                $this->redirect('/users/' . $userId . '/edit');
                return;
            }
            $update['password'] = Auth::hash($newPassword);
        }

        // Prevent admin from deactivating themselves
        if ($userId === Auth::id() && !$update['is_active']) {
            $this->flash('error', 'You cannot deactivate your own account.');
            $this->redirect('/users/' . $userId . '/edit');
            return;
        }

        $this->userModel->update($userId, $update);
        $this->flash('success', 'User updated.');
        $this->redirect('/users');
    }

    // POST /users/{id}/delete
    public function delete(string $id): void {
        $this->requireAuth();
        $this->requireRole('admin');
        CSRF::protect();

        $userId = (int)$id;

        // Prevent self-deletion
        if ($userId === Auth::id()) {
            $this->flash('error', 'You cannot delete your own account.');
            $this->redirect('/users');
            return;
        }

        $this->userModel->delete($userId);
        $this->flash('success', 'User deleted.');
        $this->redirect('/users');
    }
}
