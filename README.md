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
- Filter by status: Pending → Interview → Hired/Rejected  
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


## Sample email for hired applicants
```

Subject: Welcome to [Company Name]

Dear [Applicant Name],

Congratulations! You’ve successfully joined [Company Name].

Your portal account is ready:

Username: [username]

Temporary Password: [password]

Please log in to the portal to complete your onboarding tasks, review documents, and get started with your role.

Welcome aboard!

Best regards,
[Company Name] HR Team

```
## Sample Opening Jobs
```
1. Front Desk Executive
Department: Front Office
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Greet and welcome guests with a friendly attitude
Manage check-ins and check-outs efficiently
Handle reservations and guest inquiries
Coordinate with housekeeping and other departments
Maintain accurate records of guest information

Qualifications:

High school diploma or equivalent
Previous hospitality or customer service experience preferred
Excellent communication and interpersonal skills
Ability to work flexible shifts, including weekends and holidays

Benefits:

Competitive salary
Staff meals provided
Health insurance
Accommodation options (if required)
Training and career development programs


2. Head Chef
Department: Kitchen
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Lead and manage the kitchen team daily operations
Plan, develop, and update menus seasonally
Ensure food quality, consistency, and presentation standards
Monitor kitchen inventory and coordinate with suppliers
Enforce food safety and sanitation regulations

Qualifications:

Culinary degree or equivalent professional training
Minimum 3 years experience in a similar role
Strong leadership and team management skills
Knowledge of local and international cuisine

Benefits:

Competitive salary
Staff meals provided
Health insurance
Performance bonuses
Training and career development programs


3. Restaurant Supervisor
Department: Food & Beverage
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Oversee daily dining room and bar operations
Manage and schedule F&B staff
Ensure high standards of guest service and satisfaction
Handle guest complaints professionally and promptly
Monitor stock levels and coordinate with the kitchen team

Qualifications:

Minimum 2 years F&B supervisory experience
Strong interpersonal and leadership skills
Knowledge of food and beverage service standards
Ability to work under pressure in a fast-paced environment

Benefits:

Competitive salary
Staff meals provided
Health insurance
Service charge incentives
Training and career development programs


4. Room Attendant
Department: Housekeeping
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Clean and service guest rooms and bathrooms to hotel standards
Replenish room supplies and amenities
Report maintenance issues or damage to supervisors
Maintain cleanliness of hallways and public areas
Handle guest requests and lost-and-found items properly

Qualifications:

Prior housekeeping experience preferred
Physically fit and able to work on feet for extended hours
Detail-oriented and reliable
Ability to work flexible shifts including weekends

Benefits:

Competitive salary
Staff meals provided
Health insurance
Uniform provided
Training and career development programs


5. Maintenance Technician
Department: Maintenance
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Perform routine and preventive maintenance on hotel facilities
Respond promptly to repair requests from all departments
Maintain plumbing, electrical, HVAC, and mechanical systems
Keep maintenance logs and report major issues to management
Ensure compliance with safety and building regulations

Qualifications:

Vocational or technical course in electrical, mechanical, or plumbing
At least 2 years experience in a similar role
Ability to troubleshoot and resolve technical issues independently
Willing to be on call for emergency repairs

Benefits:

Competitive salary
Staff meals provided
Health insurance
Tools and uniform provided
Training and career development programs


6. Sales Executive
Department: Sales & Marketing
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Identify and pursue new business leads and corporate accounts
Promote hotel packages, events, and services to clients
Prepare proposals, presentations, and sales reports
Build and maintain long-term client relationships
Coordinate with operations teams to deliver on client commitments

Qualifications:

Degree in Business, Marketing, or a related field
At least 2 years sales experience preferably in hospitality
Excellent communication and negotiation skills
Self-motivated with a proven track record of meeting targets

Benefits:

Competitive base salary plus commission
Staff meals provided
Health insurance
Travel allowance
Training and career development programs


7. HR Officer
Department: Human Resources
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Manage end-to-end recruitment and onboarding processes
Maintain employee records and HR documentation
Coordinate training and development programs
Handle employee relations, grievances, and disciplinary actions
Ensure compliance with labor laws and hotel HR policies

Qualifications:

Degree in Human Resources, Psychology, or a related field
At least 2 years HR experience preferably in hospitality
Strong knowledge of labor laws and HR best practices
Excellent organizational and communication skills

Benefits:

Competitive salary
Staff meals provided
Health insurance
HMO coverage
Training and career development programs


8. Accounting Officer
Department: Accounting
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Prepare and process payroll accurately and on time
Record and reconcile daily financial transactions
Assist in budget preparation and financial reporting
Monitor accounts payable and receivable
Ensure compliance with tax regulations and hotel financial policies

Qualifications:

Degree in Accounting, Finance, or a related field
CPA license is an advantage
At least 2 years accounting experience preferably in hospitality
Proficient in accounting software and MS Excel
High attention to detail and strong analytical skills

Benefits:

Competitive salary
Staff meals provided
Health insurance
HMO coverage
Training and career development programs
```
