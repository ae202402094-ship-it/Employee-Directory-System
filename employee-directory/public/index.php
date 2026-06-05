<?php
// ============================================================
// public/index.php — Front Controller (Single Entry Point)
// ============================================================

define('BASE_PATH', dirname(__DIR__));

// Load configuration
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

// Load core
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Middleware.php';

// Load helpers
require_once BASE_PATH . '/helpers/Auth.php';
require_once BASE_PATH . '/helpers/Validator.php';
require_once BASE_PATH . '/helpers/CSRF.php';

// Load env variables from .env if exists
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $val] = array_pad(explode('=', $line, 2), 2, '');
        putenv(trim($key) . '=' . trim($val));
    }
}

// Start session
session_start();

// ── Route Definitions ────────────────────────────────────────
$router = new Router();

// Auth
$router->get('/login',  'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Root → Dashboard
$router->get('/',          'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// Employees
$router->get('/employees',               'EmployeeController@index');
$router->get('/employees/create',        'EmployeeController@create');
$router->post('/employees/store',        'EmployeeController@store');
$router->get('/employees/search',        'EmployeeController@search');
$router->get('/employees/{id}',          'EmployeeController@show');
$router->get('/employees/{id}/edit',     'EmployeeController@edit');
$router->post('/employees/{id}/update',  'EmployeeController@update');
$router->post('/employees/{id}/delete',  'EmployeeController@delete');

// Departments
$router->get('/departments',                  'DepartmentController@index');
$router->post('/departments/store',           'DepartmentController@store');
$router->post('/departments/{id}/update',     'DepartmentController@update');
$router->post('/departments/{id}/delete',     'DepartmentController@delete');

// Users (Admin only)
$router->get('/users',               'UserController@index');
$router->get('/users/create',        'UserController@create');
$router->post('/users/store',        'UserController@store');
$router->get('/users/{id}/edit',     'UserController@edit');
$router->post('/users/{id}/update',  'UserController@update');
$router->post('/users/{id}/delete',  'UserController@delete');

// ── Dispatch ─────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip subfolder prefix if app is not at server root
// e.g. if hosted at /employee-directory/public, strip that prefix
$scriptDir = dirname($_SERVER['SCRIPT_NAME']); // e.g. /employee-directory/public
if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}

$uri = '/' . ltrim($uri, '/');

$router->dispatch($method, $uri);
