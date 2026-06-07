<?php
// ============================================================
// controllers/AuthController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/Auth.php';
require_once BASE_PATH . '/helpers/CSRF.php';

class AuthController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function loginForm(): void {
        Middleware::guest();
        $this->view('auth.login', ['layout' => 'auth', 'title' => 'Login']);
    }

    public function login(): void {
        CSRF::protect();

        $email    = $this->input('email');
        $password = $_POST['password'] ?? ''; 

        $validator = new Validator(['email' => $email, 'password' => $password]);
        $validator->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->view('auth.login', [
                'layout'  => 'auth',
                'title'   => 'Login',
                'errors'  => $validator->errors(),
                'old'     => ['email' => $email],
            ]);
            return;
        }

        $user = $this->userModel->findWithRole($email);

        if (!$user || !Auth::verify($password, $user['password'])) {
            $this->view('auth.login', [
                'layout'  => 'auth',
                'title'   => 'Login',
                'errors'  => ['auth' => ['Invalid email or password.']],
                'old'     => ['email' => $email],
            ]);
            return;
        }

        if (!$user['is_active']) {
            $this->view('auth.login', [
                'layout'  => 'auth',
                'title'   => 'Login',
                'errors'  => ['auth' => ['Your account has been deactivated.']],
                'old'     => ['email' => $email],
            ]);
            return;
        }

        Auth::login($user);
        $intended = $_SESSION['intended'] ?? '/dashboard';
        unset($_SESSION['intended']);
        $this->redirect($intended);
    }

    public function logout(): void {
        Auth::logout();
        $this->redirect('/login');
    }
}