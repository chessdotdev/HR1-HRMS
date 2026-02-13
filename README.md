## FILE STRUCTURE
```
HR1_Human_Resource_System/
│
├── admin/                       <-- Admin dashboard / HR side
│   ├── applicants/
│   │   └── index.php            <-- Admin applicant management (your code)
│   └── employees/
│       └── index.php
│
├── config/
│   ├── database.php              <-- PDO connection
│   └── config.php                <-- app settings
│
│
├── Auth/
│   └── User.php                  <-- Auth / user class
│
│
├── modules/
│   ├── Applicants.php            <-- Applicant class
│   ├── Employee.php              <-- Employee class
│                  
│
├── public/                       <-- Frontend / user UI
│   ├── index.php                 <-- Job listings page
│   ├── apply.php                 <-- Applicant fill-up form
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── uploads/
│       └── resumes/
│
├── includes/
│   ├── header.php
│   └── footer.php
│
└── .htaccess
```
### .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

### Job & Employee Flow
```
job_posts
   ↓
applicant_accounts
   ↓
applicants → applicant_documents → application_status
   ↓ (Hired)
employees → accounts → employee_job_info
   ↓
employee_onboarding → onboarding_documents → orientation_schedule
   ↓
employee_status = Active
```
---

## Sidebar Components
```
Dashboard

Recruitment
    Job Postings
    Applicants
    Interviews
    Hiring Status

Onboarding
    New Hires
    Tasks
    Orientation Schedule

Employees
    Employee List
    Departments
    Roles & Access

Performance
    Evaluation Forms
    Evaluation Results

Recognition
    Points & Rewards
    Leaderboard

Notifications

Settings
    Account Settings
    System Settings

Logout

```
---

# modules/.htaccess
Deny from all

# config/.htaccess
Deny from all


# Hotel & Restaurant Human Resource (HR1) System

## Goal
Allow HR users to post jobs, track applicants, and manage talent efficiently.  

---

## 1. Dashboard (Landing Page)

---

## Job Posting
- Create new job (role, department: Kitchen, Front Desk, Housekeeping; location; shift)

---

## Applicant Management
- View all applicants per job posting  
- Filter by status: Pending → Shortlisted → Interview → Hired/Rejected  
- Candidate profile: Personal info, resume  
- Schedule Interview, Send Email, Update Status  
- Schedule interviews  
- **Automatic account creation:**  
  - If a candidate is **hired**, the system automatically creates an employee account and sends credentials via email  
  - Rejected candidates remain in the system for tracking, but no account is created

---

## 2. New Hire Onboarding
**Goal:** Smooth transition from candidate to employee  
- Pending onboarding tasks  
- Orientation (Day 2-3)

---

## 3. Performance Management
- Evaluation performance review forms  
- HR monitors progress and flags areas needing attention

---

## 4. Social Recognition
- Recognition Dashboard  
- View leaderboard

---

## Admin Side

### Dashboard
- Hiring stats, Open Jobs, onboarding progress, Total Employees, Recognition

### Job Posting
- Create and publish job postings (role, department, shift, location)

### Applicant Management
- View all applicants
- Monitor their statuses (Pending → Interview → Hired/Rejected)

### Interview
- If they pass the interview, Account creation details for the portal will be sent via email

### New Hire Onboarding Management
- Tasks (form)
- Personal Info Completion
  - Employee must submit:
    - Emergency contact
    - TIN, SSS, Pag-IBIG, PhilHealth (if PH)
    - Address & contact details
    - Bank details
- Document Submission
  - Upload:
    - Government IDs
    - Diploma or TOR
- Orientation Day 1–3 onboarding status

### Performance Management
- Monitor employees
- Monitor evaluation progress

### Social Recognition
- Manage the monthly/weekly leaderboard
