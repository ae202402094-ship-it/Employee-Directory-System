# Employee Directory System
## Complete SDLC Documentation
### PHP · MySQL · JavaScript | Academic Capstone / Project Defense

---

# PHASE 1 — REQUIREMENTS ANALYSIS

## 1.1 Problem Statement

Organizations that manage a growing workforce often rely on spreadsheets or disconnected tools to maintain employee records. This leads to **data inconsistency**, **poor searchability**, **access control gaps**, and **audit risks**. The Employee Directory System addresses these problems by providing a centralized, secure, role-based platform for managing employee data.

---

## 1.2 Functional Requirements

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-01 | User authentication (login/logout) | High |
| FR-02 | Role-based access control (Admin, HR Staff, Employee) | High |
| FR-03 | Employee registration with all required fields | High |
| FR-04 | Employee profile viewing | High |
| FR-05 | Employee search and filter (name, number, department, status) | High |
| FR-06 | Employee update (edit profile info) | High |
| FR-07 | Employee deletion (soft or hard) | High |
| FR-08 | Department management (CRUD) | Medium |
| FR-09 | User account management (Admin only) | Medium |
| FR-10 | Dashboard with KPI statistics | Medium |
| FR-11 | CSRF protection on all forms | High |
| FR-12 | Session management with regeneration on login | High |

---

## 1.3 Non-Functional Requirements

| Category | Requirement |
|----------|-------------|
| **Security** | Bcrypt password hashing (cost 12), PDO prepared statements, CSRF tokens, XSS output encoding, session hardening |
| **Performance** | Indexed DB columns, paginated queries, minimal JS (no heavy frameworks), query-efficient models |
| **Scalability** | MVC separation allows horizontal scaling; session store can be migrated to Redis; DB layer is swap-ready |
| **Maintainability** | PSR-12 PHP standards, SOLID principles, separation of concerns, documented code |
| **Reliability** | Validation on all inputs, graceful error handling, foreign key constraints |
| **Usability** | Responsive design (mobile-first), intuitive navigation, accessible forms, flash feedback |

---

## 1.4 User Stories

| ID | Role | Story | Acceptance Criteria |
|----|------|-------|---------------------|
| US-01 | Admin | Log in with email/password | Redirected to dashboard; invalid credentials rejected |
| US-02 | Admin | Add a new employee | Form validates all fields; success flash shown |
| US-03 | Admin | Edit any employee | Changes saved and reflected immediately |
| US-04 | Admin | Delete an employee | Record removed with confirmation dialog |
| US-05 | Admin | Manage departments | Create, edit, delete (delete blocked if employees assigned) |
| US-06 | Admin | Manage user accounts | Create users, assign roles, deactivate accounts |
| US-07 | HR Staff | Search employees | Filter by name, number, department, status |
| US-08 | HR Staff | View any employee profile | Full profile card with contact info |
| US-09 | Employee | View own profile via directory | Read-only access to employee list |
| US-10 | Any | See dashboard statistics | Counts, department breakdown, recent employees |

---

## 1.5 Use Cases

**UC-01: Login**
- Actor: Any user
- Pre: User is not authenticated
- Main Flow: Enter email + password → System validates → Session created → Redirect dashboard
- Alt: Invalid credentials → Error displayed, session NOT created

**UC-02: Add Employee**
- Actor: Admin, HR Staff
- Pre: User is authenticated and has appropriate role
- Main Flow: Fill form → Submit → Validate → Save to DB → Flash success → Redirect list
- Alt: Validation fails → Re-render form with errors and preserved input

**UC-03: Delete Employee**
- Actor: Admin only
- Pre: Employee exists in DB
- Main Flow: Click Delete → Confirm dialog → POST request → Delete from DB → Flash success
- Guard: Non-admins cannot see or access delete action

---

## 1.6 Constraints & Assumptions

**Constraints:**
- Single-organization deployment (one company per instance)
- LAMP stack only (Linux, Apache, MySQL, PHP 8.x)
- No third-party PHP framework (custom lightweight MVC)
- Browser support: Chrome, Firefox, Edge (latest 2 versions)

**Assumptions:**
- All users must authenticate to access any feature
- Employees may or may not have a linked user account (optional link)
- Admin role has unrestricted access
- One admin account must always exist (system protection applied)

---

# PHASE 2 — SDLC PLANNING

## 2.1 Development Methodology

**Waterfall with Iterative Reviews** — suitable for academic project with defined scope:

```
Planning → Analysis → Design → Development → Testing → Deployment → Maintenance
    ↑__________________________________________|
              Iterative review at each phase
```

## 2.2 Project Timeline (Estimated)

| Phase | Duration | Deliverable |
|-------|----------|-------------|
| Planning & Requirements | Week 1 | Requirements document, user stories |
| System Design | Week 2 | Architecture diagrams, DB schema, wireframes |
| Database Implementation | Week 3 | Working MySQL schema + seed data |
| Backend Development | Week 3–4 | Controllers, models, auth, middleware |
| Frontend Development | Week 4–5 | Views, CSS, JS interactivity |
| Integration & Testing | Week 5–6 | Full end-to-end testing, bug fixes |
| Documentation & Defense | Week 6–7 | Final documentation, presentation prep |

## 2.3 Technology Stack

| Layer | Technology | Justification |
|-------|-----------|---------------|
| Frontend | HTML5 / CSS3 / Vanilla JS | Lightweight, no build step required |
| Backend | PHP 8.2 | Widely available on LAMP, no framework overhead |
| Database | MySQL 8.0 | Relational, ACID-compliant, familiar |
| Architecture | Custom MVC | Clean separation, educational value |
| Auth | PHP Sessions + bcrypt | Secure, stateful, no external dependencies |
| Web Server | Apache + mod_rewrite | Standard for PHP deployments |

## 2.4 Coding Standards

- **PHP**: PSR-12 (braces, naming, file structure)
- **Naming**: `PascalCase` for classes, `camelCase` for methods, `snake_case` for DB columns
- **Database**: All queries use PDO prepared statements (zero raw interpolation)
- **Security**: All output via `htmlspecialchars()`, all input sanitized before use
- **Files**: One class per file, file name = class name

---

# PHASE 3 — SYSTEM ARCHITECTURE

## 3.1 High-Level Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                             │
│                 Browser (HTML / CSS / JS)                        │
│    Login   Dashboard   Employees   Departments   Users           │
└──────────────────────────┬───────────────────────────────────────┘
                           │ HTTP Request (URL)
                           ▼
┌──────────────────────────────────────────────────────────────────┐
│                    ENTRY POINT (public/index.php)                │
│        Router → Middleware (auth check) → Controller             │
└───────────────┬───────────────────────────────┬──────────────────┘
                │                               │
     ┌──────────▼──────────┐       ┌────────────▼────────────┐
     │   CONTROLLERS        │       │        HELPERS           │
     │  AuthController      │       │  Auth, Validator, CSRF   │
     │  EmployeeController  │       └─────────────────────────┘
     │  DepartmentController│
     │  UserController      │
     │  DashboardController │
     └──────────┬──────────┘
                │
     ┌──────────▼──────────┐
     │       MODELS         │
     │  User, Employee      │
     │  Department          │
     └──────────┬──────────┘
                │
     ┌──────────▼──────────┐
     │  Database (PDO)      │
     │  MySQL 8.0           │
     └─────────────────────┘
                │
     ┌──────────▼──────────┐
     │       VIEWS          │
     │  layouts/main.php    │
     │  layouts/auth.php    │
     │  employees/*.php     │
     │  departments/...     │
     └─────────────────────┘
```

## 3.2 Layer Responsibilities

| Layer | Files | Responsibility |
|-------|-------|----------------|
| **Presentation** | views/, public/assets/ | HTML rendering, CSS styling, JS interactivity |
| **Application** | controllers/ | Request handling, input validation, orchestration |
| **Domain** | models/, helpers/ | Business rules, data access, auth, validation |
| **Infrastructure** | config/database.php, .env | DB connection, environment config |

## 3.3 Authentication Flow

```
[Browser] POST /login
     → AuthController::login()
     → Validator::validate()
     → User::findWithRole($email)
     → Auth::verify($password, $hash)
     → Auth::login($user)  ← sets $_SESSION
     → session_regenerate_id()
     → redirect('/dashboard')

Every subsequent request:
     → Middleware::auth()
     → Check $_SESSION['user_id']
     → Authorized: proceed
     → Unauthorized: redirect('/login')
```

## 3.4 Request Lifecycle

```
1. Browser sends HTTP request to public/index.php
2. Router matches URI to Controller@method
3. Controller calls Middleware::auth() (and role check if needed)
4. Controller calls Model methods (PDO queries)
5. Controller calls $this->view() with data
6. View is rendered inside layout (main.php / auth.php)
7. HTML response sent to browser
```

## 3.5 Security Architecture

| Threat | Mitigation |
|--------|-----------|
| SQL Injection | PDO prepared statements on ALL queries |
| XSS | `htmlspecialchars()` on all output in views |
| CSRF | Token generated per session, verified on every POST |
| Session Fixation | `session_regenerate_id(true)` on login |
| Brute Force | (Phase 2: rate limiting) |
| Unauthorized Access | Middleware checks role before every protected action |
| Password Storage | bcrypt (cost=12, never plain text) |

---

# PHASE 4 — DATABASE DESIGN

## 4.1 Entity Relationship Diagram

```
┌──────────┐          ┌──────────────┐         ┌──────────────┐
│  roles   │──────────│    users     │         │ departments  │
│──────────│ 1      * │──────────────│         │──────────────│
│ id (PK)  │          │ id (PK)      │         │ id (PK)      │
│ name     │          │ role_id (FK) │         │ name         │
└──────────┘          │ username     │         │ description  │
                      │ email        │         └──────┬───────┘
                      │ password     │                │ 1
                      │ is_active    │                │
                      └──────┬───────┘                │ *
                             │ 0..1             ┌─────▼────────────┐
                             │                  │    employees     │
                             └──────────────────│──────────────────│
                                          0..1  │ id (PK)          │
                                                │ user_id (FK)     │
                                                │ department_id(FK)│
                                                │ employee_number  │
                                                │ first_name       │
                                                │ last_name        │
                                                │ email            │
                                                │ phone            │
                                                │ position         │
                                                │ hire_date        │
                                                │ status           │
                                                │ address          │
                                                └──────────────────┘
```

## 4.2 Table Definitions

### roles
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AI | Unique role ID |
| name | VARCHAR(50) | NOT NULL, UNIQUE | 'admin', 'hr_staff', 'employee' |
| created_at | TIMESTAMP | DEFAULT NOW | Record creation time |

### users
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AI | Unique user ID |
| role_id | INT UNSIGNED | FK → roles.id | Assigned role |
| username | VARCHAR(100) | NOT NULL, UNIQUE | Login username |
| email | VARCHAR(150) | NOT NULL, UNIQUE | Login email |
| password | VARCHAR(255) | NOT NULL | bcrypt hash |
| is_active | TINYINT(1) | DEFAULT 1 | Account status |
| created_at | TIMESTAMP | DEFAULT NOW | — |
| updated_at | TIMESTAMP | ON UPDATE NOW | — |

### departments
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AI | — |
| name | VARCHAR(100) | NOT NULL, UNIQUE | Department name |
| description | TEXT | NULL | Optional description |
| created_at | TIMESTAMP | DEFAULT NOW | — |
| updated_at | TIMESTAMP | ON UPDATE NOW | — |

### employees
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT UNSIGNED | PK, AI | — |
| user_id | INT UNSIGNED | FK → users.id, NULL | Optional linked user account |
| department_id | INT UNSIGNED | FK → departments.id, NULL | Assigned department |
| employee_number | VARCHAR(20) | NOT NULL, UNIQUE | e.g. EMP-0001 |
| first_name | VARCHAR(100) | NOT NULL | — |
| last_name | VARCHAR(100) | NOT NULL | — |
| email | VARCHAR(150) | NOT NULL, UNIQUE | Work email |
| phone | VARCHAR(20) | NULL | — |
| position | VARCHAR(100) | NULL | Job title |
| hire_date | DATE | NULL | — |
| status | ENUM | DEFAULT 'active' | active / inactive / terminated |
| address | TEXT | NULL | — |
| created_at | TIMESTAMP | DEFAULT NOW | — |
| updated_at | TIMESTAMP | ON UPDATE NOW | — |

## 4.3 Indexes

| Index Name | Table | Column(s) | Purpose |
|------------|-------|-----------|---------|
| idx_emp_status | employees | status | Filter by status |
| idx_emp_dept | employees | department_id | Filter by department |
| idx_emp_name | employees | last_name, first_name | Name search |
| idx_emp_number | employees | employee_number | Number lookup |
| idx_emp_email | employees | email | Email uniqueness |
| idx_users_email | users | email | Login lookup |
| idx_users_active | users | is_active | Filter active users |

---

# PHASE 5 — UI/UX DESIGN

## 5.1 Design System

**Font:** DM Sans (body) + DM Serif Display (brand/logo)
**Palette:**
- Primary: `#2563eb` (Blue)
- Success: `#16a34a` (Green)
- Danger: `#dc2626` (Red)
- Warning: `#d97706` (Amber)
- Purple: `#7c3aed`
- BG: `#f1f5f9`
- Surface: `#ffffff`
- Sidebar: `#0f172a` (Dark slate)

**Layout:** Fixed sidebar (240px) + scrollable main content area

## 5.2 Screen Inventory

| Screen | Route | Access | Key Components |
|--------|-------|--------|----------------|
| Login | /login | Public | Email/password form, CSRF, error alerts |
| Dashboard | /dashboard | All | KPI cards, bar chart, recent employees |
| Employee List | /employees | All | Search bar, filters, data table, action buttons |
| Employee Profile | /employees/{id} | All | Profile card, contact info, danger zone (admin) |
| Add Employee | /employees/create | Admin, HR | Multi-section form, department dropdown |
| Edit Employee | /employees/{id}/edit | Admin, HR | Pre-filled form |
| Departments | /departments | All (Admin CRUD) | Card grid, create modal, edit modal |
| User Management | /users | Admin | User table, role badges, status toggle |
| Create User | /users/create | Admin | Role, username, email, password |
| Edit User | /users/{id}/edit | Admin | Same fields + deactivate toggle |

## 5.3 User Flow

```
Guest → Login → Dashboard
                 ├── Employees → View / Edit / Delete
                 ├── Departments → Create / Edit / Delete
                 └── Users (Admin) → Create / Edit / Delete

Unauthorized route → 403 page
Not logged in → 302 redirect to /login
```

## 5.4 Access Control Matrix

| Action | Admin | HR Staff | Employee |
|--------|-------|----------|----------|
| View dashboard | ✅ Full | ✅ | ✅ |
| View employee list | ✅ | ✅ | ✅ |
| View employee profile | ✅ | ✅ | ✅ |
| Create employee | ✅ | ✅ | ❌ |
| Edit employee | ✅ | ✅ | ❌ |
| Delete employee | ✅ | ❌ | ❌ |
| Manage departments | ✅ | ❌ | ❌ |
| Manage users | ✅ | ❌ | ❌ |

---

# PHASE 6 — API / ROUTE DESIGN

## 6.1 Route Map

| Method | Route | Controller@Action | Auth | Role |
|--------|-------|------------------|------|------|
| GET | /login | AuthController@loginForm | Guest | — |
| POST | /login | AuthController@login | Guest | — |
| GET | /logout | AuthController@logout | Auth | Any |
| GET | /dashboard | DashboardController@index | Auth | Any |
| GET | /employees | EmployeeController@index | Auth | Any |
| GET | /employees/create | EmployeeController@create | Auth | admin, hr_staff |
| POST | /employees/store | EmployeeController@store | Auth | admin, hr_staff |
| GET | /employees/search | EmployeeController@search | Auth | Any |
| GET | /employees/{id} | EmployeeController@show | Auth | Any |
| GET | /employees/{id}/edit | EmployeeController@edit | Auth | admin, hr_staff |
| POST | /employees/{id}/update | EmployeeController@update | Auth | admin, hr_staff |
| POST | /employees/{id}/delete | EmployeeController@delete | Auth | admin |
| GET | /departments | DepartmentController@index | Auth | Any |
| POST | /departments/store | DepartmentController@store | Auth | admin |
| POST | /departments/{id}/update | DepartmentController@update | Auth | admin |
| POST | /departments/{id}/delete | DepartmentController@delete | Auth | admin |
| GET | /users | UserController@index | Auth | admin |
| GET | /users/create | UserController@create | Auth | admin |
| POST | /users/store | UserController@store | Auth | admin |
| GET | /users/{id}/edit | UserController@edit | Auth | admin |
| POST | /users/{id}/update | UserController@update | Auth | admin |
| POST | /users/{id}/delete | UserController@delete | Auth | admin |

## 6.2 Validation Rules

**Employee Create/Update:**
- `first_name`: required, max:100
- `last_name`: required, max:100
- `email`: required, valid email, max:150, unique
- `employee_number`: required, max:20, unique (on create)
- `phone`: optional, valid phone pattern
- `position`: optional, max:100
- `status`: must be one of: active, inactive, terminated
- `hire_date`: optional, valid date format
- `department_id`: optional, numeric

**User Create/Update:**
- `username`: required, min:3, max:100, alphanumeric + dash/underscore only
- `email`: required, valid email, unique
- `password`: required on create, min:8
- `role_id`: required, numeric

---

# PHASE 7 — FOLDER STRUCTURE

```
employee-directory/
├── public/                     ← Web root (Apache DocumentRoot)
│   ├── index.php               ← Front controller (single entry point)
│   ├── .htaccess               ← URL rewriting rules
│   └── assets/
│       ├── css/
│       │   └── app.css         ← Complete design system (1 file)
│       └── js/
│           └── app.js          ← Vanilla JS (sidebar, modals, validation)
│
├── config/
│   ├── app.php                 ← App constants (ENV, timezone, error display)
│   └── database.php            ← PDO singleton connection class
│
├── core/
│   ├── Router.php              ← URL router with named params ({id})
│   ├── Controller.php          ← Base controller (view, redirect, input, flash)
│   ├── Model.php               ← Base model (CRUD, pagination, raw query)
│   └── Middleware.php          ← Auth and role middleware
│
├── helpers/
│   ├── Auth.php                ← Session management, role checks
│   ├── Validator.php           ← Rule-based input validator
│   └── CSRF.php                ← Token generation and verification
│
├── models/
│   ├── User.php                ← User queries + email/username uniqueness
│   ├── Employee.php            ← Employee queries + search + stats
│   └── Department.php          ← Department queries + count + guard
│
├── controllers/
│   ├── AuthController.php      ← Login / logout
│   ├── DashboardController.php ← KPI stats aggregation
│   ├── EmployeeController.php  ← Full employee CRUD + search
│   ├── DepartmentController.php← Department CRUD with guards
│   └── UserController.php      ← User CRUD (Admin only)
│
├── views/
│   ├── layouts/
│   │   ├── main.php            ← Sidebar + topbar + flash + content slot
│   │   └── auth.php            ← Minimal layout for login page
│   ├── auth/
│   │   └── login.php           ← Login form
│   ├── dashboard/
│   │   └── index.php           ← KPI cards + bar chart + recent table
│   ├── employees/
│   │   ├── index.php           ← List with search/filter/table
│   │   ├── show.php            ← Profile card + contact info
│   │   ├── create.php          ← Wraps shared _form.php
│   │   ├── edit.php            ← Wraps shared _form.php (pre-filled)
│   │   └── _form.php           ← Shared form partial (DRY)
│   ├── departments/
│   │   └── index.php           ← Card grid + create/edit modals
│   ├── users/
│   │   ├── index.php           ← User table with role badges
│   │   ├── create.php          ← Create user form
│   │   └── edit.php            ← Edit user + status toggle
│   └── errors/
│       └── 403.php             ← Forbidden error page
│
├── database/
│   ├── schema.sql              ← Full MySQL schema (tables + indexes)
│   └── seed.sql                ← Sample data (roles, depts, users, employees)
│
├── .env.example                ← Template for environment config
└── .env                        ← Actual config (NOT committed to git)
```

---

# PHASE 8 — CLEAN ARCHITECTURE REVIEW

## 8.1 Separation of Concerns

| Concern | Where Handled | Correct? |
|---------|--------------|----------|
| Input handling | Controller (input(), query()) | ✅ |
| Validation | Validator helper | ✅ Decoupled |
| Business rules | Models + Controllers | ✅ |
| Data access | Models (PDO) | ✅ |
| Output rendering | Views only | ✅ |
| Auth state | Auth helper + Session | ✅ |
| Security checks | Middleware | ✅ Centralized |

## 8.2 SOLID Principles Applied

| Principle | Implementation |
|-----------|---------------|
| **S** – Single Responsibility | Each class has one job (User model ≠ UserController) |
| **O** – Open/Closed | Base Model can be extended without modification |
| **L** – Liskov Substitution | All controllers extend Controller and can substitute it |
| **I** – Interface Segregation | Helpers are standalone (not forced interfaces) |
| **D** – Dependency Inversion | Controllers depend on model abstractions, not PDO directly |

## 8.3 DRY Applied

- `_form.php` shared between `create.php` and `edit.php`
- `Base Model` provides all CRUD so subclass models don't repeat
- `Base Controller` provides `view()`, `redirect()`, `input()`, `flash()` for all controllers
- Auth checks via `$this->requireAuth()` and `$this->requireRole()` — one line per action

---

# PHASE 9 — QUALITY REVIEW

## 9.1 Code Quality Analysis

| Area | Finding | Severity | Status |
|------|---------|----------|--------|
| SQL injection | All queries use PDO prepared statements | ✅ Resolved | OK |
| XSS | All outputs use `htmlspecialchars()` | ✅ Resolved | OK |
| CSRF | All POST forms include token | ✅ Resolved | OK |
| Password storage | bcrypt cost=12 | ✅ Secure | OK |
| Session fixation | `session_regenerate_id(true)` on login | ✅ Resolved | OK |
| Self-deletion | Guard in UserController::delete() | ✅ Resolved | OK |
| Self-deactivation | Guard in UserController::update() | ✅ Resolved | OK |
| Dept delete guard | Blocked if employees assigned | ✅ Resolved | OK |

## 9.2 Architecture Quality

| Risk | Mitigation |
|------|-----------|
| No input sanitization | Implemented in `Controller::input()` |
| Raw GET params | Wrapped via `Controller::query()` |
| Direct DB access in views | Views receive data only; no DB calls allowed in views |
| God controllers | Each controller handles one entity |

## 9.3 UX Quality

| Issue | Solution |
|-------|----------|
| No flash feedback | Flash messages in session, displayed in layout |
| No form repopulation on error | `$old` array passed back to views |
| No loading indicators | Form submit disables button (JS) — Phase 2 enhancement |
| Mobile navigation | Collapsible sidebar with overlay on mobile |

---

# PHASE 10 — DEBUGGING REVIEW

## 10.1 Common Bug Scenarios & Fixes

### Bug 1: Route not matched for /employees/create (matched as /employees/{id})
**Root Cause:** Static routes registered after dynamic ones in Router; `create` captured as `{id}`.
**Fix:** In `index.php`, register `/employees/create` BEFORE `/employees/{id}`.
**Status:** ✅ Fixed — static routes are declared first in route list.

---

### Bug 2: CSRF token mismatch after session timeout
**Root Cause:** Session expires; new session has new CSRF token; old form token stale.
**Fix:** Show "Session expired, please refresh" message instead of plain 419 error.
**Recommendation:** Catch CSRF failure in middleware and redirect to form with error flash.

---

### Bug 3: Employee number auto-increment collision on concurrent inserts
**Root Cause:** `nextEmployeeNumber()` reads last insert, then another user inserts before this user submits.
**Fix:** Employee number field is editable and unique-constrained at DB level. If duplicate, PDO throws exception — catch it in controller.
```php
try {
    $this->employeeModel->create($data);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { // Integrity constraint violation
        // Re-render form with specific error
    }
}
```

---

### Bug 4: Redirect loop on Middleware::guest() when BASE_URL has trailing slash
**Root Cause:** Session check passes but redirect target includes double slash.
**Fix:** `rtrim(BASE_URL, '/')` applied consistently in Controller::redirect().

---

### Bug 5: Password field sanitized by Controller::input() breaking bcrypt
**Root Cause:** `htmlspecialchars()` on password before `password_verify()` corrupts the string if password contains `<`, `>`, `&`.
**Fix:** ✅ Already handled — AuthController reads `$_POST['password']` directly, bypassing `$this->input()`. Only use `input()` for non-sensitive fields.

---

### Bug 6: Department bar chart renders at 0px width on first load
**Root Cause:** `canvas.offsetWidth` returns 0 before layout paint completes.
**Fix:** Wrap chart rendering in `requestAnimationFrame()` or use `setTimeout(..., 0)`.

---

## 10.2 Edge Cases Covered

| Scenario | Handling |
|----------|----------|
| Empty employee list | Empty state UI shown |
| No departments | Department dropdown shows "— None —" |
| Deleting admin's own account | Blocked with flash error |
| Deactivating own account | Blocked with flash error + disabled checkbox |
| Employee with no linked user | `user_id` nullable, LEFT JOIN in queries |
| Delete dept with active employees | Blocked — flash error shown |
| Non-integer {id} in URL | Model::find() casts to int; returns null → redirect |

---

# PHASE 11 — PERFORMANCE OPTIMIZATION

## 11.1 Database Optimization

| Technique | Applied | Impact |
|-----------|---------|--------|
| Indexed columns | `status`, `department_id`, `last_name`, `email` | Fast WHERE/JOIN |
| Covering indexes | `idx_emp_name` covers `last_name, first_name` | Sort without table scan |
| LEFT JOIN instead of subqueries | Used in search and dashboard queries | Single query, better plan |
| `LIMIT` on recent queries | `recent(5)` uses `LIMIT ?` | Avoids full table scan |
| Count queries optimized | `COUNT(*)` + `GROUP BY` for dept breakdown | One query vs. N+1 |
| `PDO::FETCH_ASSOC` | Default fetch mode set | Avoids creating objects |

**N+1 Problem Prevention:**
- Employee list fetches department in same JOIN query (not separate call per employee)
- Dashboard aggregates all stats in 4 queries, not per-employee loops

## 11.2 PHP Optimization

| Technique | Applied |
|-----------|---------|
| Database singleton | One PDO connection per request |
| `ob_start() / ob_get_clean()` | View captured to buffer, then rendered in layout |
| Autoloading | `spl_autoload_register` avoids manual require chains |
| Early returns | Controllers return early on validation fail (avoid deep nesting) |
| No session data leakage | Only 5 session keys stored (id, username, email, role, role_id) |

## 11.3 Frontend Optimization

| Technique | Applied |
|-----------|---------|
| Single CSS file | `app.css` — no multiple network requests |
| Single JS file | `app.js` — no framework overhead |
| CSS Variables | Avoids repeated values, enables instant theming |
| No jQuery | Vanilla JS — smaller payload, no dependency |
| Debounced search | 350ms delay prevents per-keystroke form submits |
| Canvas bar chart | Native HTML5 Canvas — no Chart.js overhead |
| Google Fonts with `display=swap` | Avoids invisible text during font load |

## 11.4 Scalability Recommendations (Phase 2)

| Enhancement | Description |
|-------------|-------------|
| Pagination | Add LIMIT/OFFSET to employee list (currently shows all) |
| Search AJAX | Replace form submit search with fetch() AJAX calls |
| Redis sessions | Replace PHP file sessions with Redis for multi-server |
| Query caching | Cache dashboard stats (30s TTL) to reduce DB load |
| Image upload | Add employee photo upload with local/S3 storage |
| Soft deletes | Add `deleted_at` column instead of hard delete |
| Activity log | `activity_logs` table tracking who changed what |

---

# PHASE 12 — FINAL DELIVERABLES & DEPLOYMENT

## 12.1 Deployment Guide (XAMPP / LAMP)

### Step 1: Clone / Place Files
```bash
# Place project in your web server root
# XAMPP: C:/xampp/htdocs/employee-directory
# Linux: /var/www/html/employee-directory
```

### Step 2: Create Database
```bash
# Via phpMyAdmin: import database/schema.sql, then database/seed.sql
# Or via MySQL CLI:
mysql -u root -p < database/schema.sql
mysql -u root -p employee_directory < database/seed.sql
```

### Step 3: Configure Environment
```bash
cp .env.example .env
# Edit .env:
#   APP_ENV=development
#   BASE_URL=http://localhost/employee-directory/public
#   DB_HOST=localhost
#   DB_NAME=employee_directory
#   DB_USER=root
#   DB_PASS=your_password
```

### Step 4: Generate Admin Password Hash
```php
<?php echo password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);
```
Update the seed.sql with the generated hash and re-run, or update directly:
```sql
UPDATE users SET password = 'your_hash_here' WHERE email = 'admin@company.com';
```

### Step 5: Enable mod_rewrite (Apache)
```apache
# In httpd.conf, ensure:
AllowOverride All
# And mod_rewrite is enabled
```

### Step 6: Access the App
- URL: `http://localhost/employee-directory/public`
- Default credentials: `admin@company.com` / `Admin@123`

---

## 12.2 Maintenance Guide

| Task | Frequency | Action |
|------|-----------|--------|
| DB backup | Daily | `mysqldump employee_directory > backup_$(date).sql` |
| Password policy | Quarterly | Enforce password reset for all users |
| Log review | Weekly | Check Apache error logs for PHP warnings |
| Dependency review | Per semester | Update PHP version; check for security patches |
| User audit | Monthly | Deactivate accounts for departed employees |

---

## 12.3 Future Enhancements (Roadmap)

| Priority | Feature | Description |
|----------|---------|-------------|
| High | Pagination | LIMIT/OFFSET with page controls on employee list |
| High | Soft deletes | `deleted_at` column + restore capability |
| Medium | Employee photo upload | Avatar image with file size/type validation |
| Medium | Activity audit log | Track all create/edit/delete actions with user + timestamp |
| Medium | Export to CSV/PDF | Download employee list for HR reporting |
| Low | Email notifications | Notify HR when new employee added |
| Low | Dark mode toggle | CSS variable switch |
| Low | API mode | JSON API endpoints for mobile app integration |
| Low | 2FA | TOTP-based two-factor authentication for Admin accounts |

---

## 12.4 Executive Summary

The **Employee Directory System** is a complete, production-quality web application built on a **PHP 8.2 / MySQL 8.0 / Vanilla JavaScript** stack following the **Model-View-Controller (MVC)** architectural pattern.

The system delivers:
- **Secure authentication** with bcrypt password hashing, session management, and CSRF protection
- **Role-based access control** across three user roles (Admin, HR Staff, Employee)
- **Full employee lifecycle management** — create, read, update, delete with validation
- **Department management** with employee count guards
- **Administrative user management** with deactivation protection
- **Responsive, professional UI** with a dark sidebar design system, data tables, modals, and a native Canvas dashboard chart
- **Clean MVC architecture** with SOLID principles, DRY code reuse, and PSR-12 coding standards
- **Zero external framework dependencies** — entirely self-contained, academic-friendly, and deployment-ready on any LAMP server

The codebase is structured for **academic presentation** while meeting **industry standards** for security, maintainability, and scalability.

---

*Employee Directory System v1.0.0 — SDLC Documentation*
*Generated for academic project defense | PHP + MySQL + JavaScript*
