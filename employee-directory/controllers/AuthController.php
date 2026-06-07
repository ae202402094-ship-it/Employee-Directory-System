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

    // GET /login
    public function loginForm(): void {
        Middleware::guest(); // redirect away if already logged in
        $this->view('auth.login', ['layout' => 'auth', 'title' => 'Login']);
    }

    // POST /login
    public function login(): void {
        CSRF::protect();

        $email    = $this->input('email');
        $password = $_POST['password'] ?? ''; // don't sanitize password before verify

        // Validate
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

        // Find user
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
                'errors'  => ['auth' => ['Your account has been deactivated. Contact your administrator.']],
                'old'     => ['email' => $email],
            ]);
            return;
        }

        Auth::login($user);

        // Redirect to intended URL or dashboard
        $intended = '/dashboard';
        unset($_SESSION['intended']);
        $this->redirect($intended);
    }

    // GET /logout
    public function logout(): void {
        Auth::logout();
        $this->redirect('/login');
    }
}
