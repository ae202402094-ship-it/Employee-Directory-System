-- ============================================================
-- Employee Directory System — Seed Data
-- Run AFTER schema.sql
-- ============================================================

USE employee_directory;

-- ─────────────────────────────────────────────
-- ROLES
-- ─────────────────────────────────────────────
INSERT INTO roles (name) VALUES
    ('admin'),
    ('hr_staff'),
    ('employee');

-- ─────────────────────────────────────────────
-- DEPARTMENTS
-- ─────────────────────────────────────────────
INSERT INTO departments (name, description) VALUES
    ('Engineering',      'Software and hardware engineering team'),
    ('Human Resources',  'People operations and talent management'),
    ('Finance',          'Accounting, budgeting, and financial operations'),
    ('Marketing',        'Brand strategy and marketing operations'),
    ('Operations',       'Internal operations and logistics');

-- ─────────────────────────────────────────────
-- USERS
-- Admin password: Admin@123
-- ─────────────────────────────────────────────
INSERT INTO users (role_id, username, email, password, is_active) VALUES
    (1, 'admin',   'admin@company.com',   '$2y$12$TzKHmMsHK5LkAr8bL0sFX.U0k.Bj0P1YqTJLxWhzVnJ6JNKf8Mmxi', 1),
    (2, 'hr_anna', 'anna.hr@company.com', '$2y$12$TzKHmMsHK5LkAr8bL0sFX.U0k.Bj0P1YqTJLxWhzVnJ6JNKf8Mmxi', 1),
    (3, 'jdoe',    'john.doe@company.com','$2y$12$TzKHmMsHK5LkAr8bL0sFX.U0k.Bj0P1YqTJLxWhzVnJ6JNKf8Mmxi', 1);

-- ─────────────────────────────────────────────
-- EMPLOYEES (sample data)
-- ─────────────────────────────────────────────
INSERT INTO employees
    (user_id, department_id, employee_number, first_name, last_name, email, phone, position, hire_date, status) VALUES
    (1, 1, 'EMP-0001', 'System',   'Admin',    'admin@company.com',      '09001234567', 'System Administrator', '2020-01-01', 'active'),
    (2, 2, 'EMP-0002', 'Anna',     'Reyes',    'anna.hr@company.com',    '09001234568', 'HR Manager',           '2021-03-15', 'active'),
    (3, 1, 'EMP-0003', 'John',     'Doe',      'john.doe@company.com',   '09001234569', 'Software Engineer',    '2022-06-01', 'active'),
    (NULL, 1, 'EMP-0004', 'Maria',  'Santos',   'maria.s@company.com',    '09001234570', 'Frontend Developer',   '2022-08-10', 'active'),
    (NULL, 3, 'EMP-0005', 'Carlos', 'Reyes',    'carlos.r@company.com',   '09001234571', 'Financial Analyst',    '2021-11-20', 'active'),
    (NULL, 4, 'EMP-0006', 'Lisa',   'Tan',      'lisa.t@company.com',     '09001234572', 'Marketing Specialist', '2023-01-05', 'active'),
    (NULL, 2, 'EMP-0007', 'Pedro',  'Garcia',   'pedro.g@company.com',    '09001234573', 'HR Coordinator',       '2020-07-22', 'active'),
    (NULL, 5, 'EMP-0008', 'Sophia', 'Cruz',     'sophia.c@company.com',   '09001234574', 'Operations Manager',   '2019-04-30', 'active'),
    (NULL, 1, 'EMP-0009', 'Marco',  'Villanueva','marco.v@company.com',   '09001234575', 'Backend Developer',    '2023-09-01', 'active'),
    (NULL, 3, 'EMP-0010', 'Lena',   'Bautista', 'lena.b@company.com',     '09001234576', 'Accountant',           '2022-02-14', 'inactive');

-- ─────────────────────────────────────────────
-- NOTE: Generate password hash for 'Admin@123' via PHP:
--   echo password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);
-- Then update all seed passwords above.
-- ─────────────────────────────────────────────
