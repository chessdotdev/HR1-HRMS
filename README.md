# Hotel & Restaurant HR System (HR1)
> A web-based Human Resource Management System for hotel and restaurant operations — built with PHP, MySQL, and Bootstrap 5.

---

## Table of Contents
- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [File Structure](#file-structure)
- [System Flow](#system-flow)
- [Functions & Features](#functions--features)
- [Data Transfer Protocol](#data-transfer-protocol)
- [Security](#security)
- [Admin Roles](#admin-roles)
- [Installation](#installation)

---

## Overview

HR1 is a full-featured HR system designed for Luxor Grand Hotel. It covers the complete employee lifecycle — from job posting and applicant tracking, to onboarding, performance evaluation, and social recognition.

It has two portals:
- **Admin Portal** — for HR staff to manage recruitment, employees, performance, and recognition
- **Employee Portal** — for active employees to view their onboarding, performance review, and recognitions

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8 (procedural + OOP) |
| Database | MySQL (via PDO) |
| Frontend | Bootstrap 5, Bootstrap Icons, Geist Font |
| Email | PHPMailer + SMTP |
| Environment | phpdotenv (.env) |
| Server | Apache (XAMPP) |

---

## File Structure

```
Hotel_and_Restaurant_HR1/
│
├── admin/                        ← Admin portal pages
│   ├── includes/
│   │   ├── header.php            ← Session, RBAC check, sidebar
│   │   ├── footer.php
│   │   └── verify_admin.php      ← Auth + role access guard
│   ├── dashboard.php
│   ├── job_openings.php
│   ├── applicants.php
│   ├── interviews.php
│   ├── applicant_status.php
│   ├── hiring_status.php
│   ├── new_hires.php
│   ├── onboarding_tasks.php
│   ├── orientation_schedule.php
│   ├── employee_list.php
│   ├── departments.php
│   ├── evaluation_forms.php
│   ├── evaluation_results.php
│   ├── points_rewards.php
│   ├── leaderboard.php
│   ├── roles.php                 ← RBAC management (Super Admin only)
│   ├── account_settings.php
│   ├── system_settings.php       ← Super Admin only
│   ├── audit_logs.php            ← Super Admin only
│   └── login.php
│
├── employee/                     ← Employee portal pages
│   ├── includes/
│   │   ├── header.php
│   │   └── footer.php
│   ├── dashboard.php
│   ├── onboarding.php
│   ├── personal_info.php
│   ├── documents.php
│   ├── orientation.php
│   ├── my_performance.php
│   ├── recognition.php
│   ├── recognition_history.php
│   ├── profile.php
│   ├── toggle_reaction.php       ← AJAX endpoint
│   └── login.php
│
├── modules/                      ← Business logic classes
│   ├── Applicants.php
│   ├── Employee.php
│   ├── Recruitment.php
│   ├── Performance.php
│   ├── Recognition.php
│   ├── RBAC.php
│   └── AuditLog.php
│
├── auth/
│   ├── Admin.php                 ← Admin login/register
│   ├── User.php
│   └── Applicants_account.php
│
├── config/
│   └── Database.php              ← PDO connection
│
├── backup/                       ← SQL schema files
│   ├── performance_recognition_schema.sql
│   ├── employee_onboarding_schema.sql
│   ├── departments_schema.sql
│   ├── rbac_schema.sql
│   └── settings_audit_schema.sql
│
├── public/                       ← Applicant-facing pages
│   ├── index.php                 ← Job listings
│   ├── apply.php                 ← Application form
│   └── uploads/resumes/
│
├── MailService.php               ← Email sender wrapper
├── .env                          ← SMTP credentials (not committed)
├── .htaccess
└── README.md
```

---

## System Flow

```
Job Postings
     ↓
Applicants apply (public portal)
     ↓
HR reviews → Schedule Interview → Send email notification
     ↓
Interview Result: Passed
     ↓
Auto-create Employee Account → Send credentials via email
     ↓
Employee Portal: Onboarding (Personal Info → Documents → Orientation)
     ↓
Employment Status: Active
     ↓
Performance Review (Probation) → Goals → Feedback → Ratings → Finalize
     ↓
Social Recognition → Leaderboard
```

---

## Functions & Features

### Recruitment
- Create and publish job postings (role, department, shift, location)
- Applicants submit applications via public portal
- HR reviews applicant profiles and resumes
- Schedule interviews — sends automated email notification to applicant
- Update applicant status: `Pending → Interview → Hired / Rejected`
- **Special:** Hired applicants automatically get an employee account created and receive login credentials via email
- Rejected applicants remain in the system for tracking

### Onboarding
- Track new hire onboarding tasks and progress
- Personal information completion (emergency contact, government IDs, bank details)
- Document submission (Government IDs, Diploma/TOR)
- Orientation schedule (Day 1–3)
- **Special:** Onboarding progress bar shown in employee sidebar

### Employee Management
- View and manage all active employees
- Department management
- View individual employee profiles

### Performance Management
- Create probation reviews per employee
- Set and track goals with statuses (Pending / In Progress / Achieved / Not Achieved)
- Add periodic feedback entries (Strengths & Areas for Improvement)
- Rate employees across 5 categories on a 1–5 scale
- **Special:** Finalization checklist — all goals must be resolved, at least one feedback entry, all categories rated before finalizing
- Finalize with: `Passed` / `Failed` / `Extended`
- **Special:** Extended probation re-opens the review with a new start and end date
- **Special:** Default probation duration pulled from System Settings

### Social Recognition
- HR posts recognitions with 6 award types
- Employees react with heart reactions (AJAX — no page reload)
- Monthly leaderboard on employee dashboard (top 5)
- Recognition feed filterable by award type
- **Special:** Employee Recognition History — timeline of all awards received with breakdown by type

### Roles & Access Control (RBAC)
- 3 admin roles: `Super Admin`, `HR Manager`, `Recruiter`
- Page-level access enforced on every page load
- Super Admin can create, assign roles, and delete admin accounts
- **Special:** Sidebar filtered dynamically based on logged-in role
- Unauthorized access returns HTTP 403 Access Denied page
- Public admin registration blocked — accounts created by Super Admin only

### Settings
- **Account Settings** — change username, email, password (all roles)
- **System Settings** — company name, HR email, probation days, email signature (Super Admin only)
- **Special:** System settings dynamically applied to all outgoing emails and probation review date auto-fill

### Audit Logs
- Records all admin actions across all modules
- Filterable by module and admin user
- Tracks: profile updates, password changes, settings changes, recognition posts, review finalizations

---

## Data Transfer Protocol

The system uses **HTTP** for all client-server communication including page loads, form submissions (POST/GET), and AJAX calls. **SMTP** is used by PHPMailer to deliver transactional emails such as interview schedules, hired credentials, and rejection notices through a configured mail server.

---

## Security

**Password Hashing** — All passwords are hashed using `password_hash()` with `PASSWORD_DEFAULT` (bcrypt) before being stored in the database, ensuring plain-text passwords are never saved.

**Session Authentication** — Every admin and employee page verifies the session on every page load via `verify_admin.php` for the admin portal and `header.php` for the employee portal. Unauthenticated users are redirected to the login page.

**Role-Based Access Control** — Each admin page checks the logged-in user's role against a permission map on every request. If the role does not have access to that page, the system returns an HTTP 403 Access Denied page.

**PDO Prepared Statements** — All database queries use PDO prepared statements with bound parameters, which prevents SQL injection attacks by separating SQL logic from user-supplied data.

**Input Sanitization** — All user inputs are filtered using `htmlspecialchars()` on output and `FILTER_SANITIZE_*` functions on input to prevent cross-site scripting (XSS) attacks.

**Directory Protection** — The `modules/` and `config/` folders are protected with `.htaccess` using `Deny from all`, preventing direct browser access to sensitive business logic and database configuration files.

**Public Registration Blocked** — The admin `register.php` page redirects to login, making it impossible for anyone to self-register as an admin. Admin accounts can only be created by the Super Admin through the Roles & Access module.

**Environment Variables** — SMTP credentials and other sensitive configuration values are stored in a `.env` file via `phpdotenv` and are never hardcoded in the source code, reducing the risk of credential exposure.

**Audit Logs** — All admin actions across every module are recorded in the audit log, including profile updates, password changes, settings modifications, recognition posts, and review finalizations. This provides a full trail of who did what and when, allowing the Super Admin to monitor and investigate any suspicious activity.

---

## Admin Roles

| Role | Access |
|---|---|
| `Super Admin` | Full access — all modules including Roles & Access, System Settings, Audit Logs |
| `HR Manager` | All modules except Roles & Access and System Settings |
| `Recruiter` | Recruitment and Onboarding modules only |

---

## Installation

1. Clone or copy the project into `htdocs/`
2. Import all SQL files from `backup/` into your MySQL database
3. Configure `config/Database.php` with your DB credentials
4. Copy `.env.example` to `.env` and fill in your SMTP credentials
5. Run `admin/migrate_rbac.php` in the browser to apply RBAC migration
6. Delete `migrate_rbac.php` after running
7. Visit `http://localhost/Hotel_and_Restaurant_HR1/admin/login.php`
8. Log in with your Super Admin account

---

> Built for Luxor Grand Hotel — Hotel & Restaurant HR System (HR1)
