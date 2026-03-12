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
```
Job Openings – Luxor Grand Hotel
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

2. Chef

Department: Kitchen
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Prepare and cook menu items according to recipes

Maintain kitchen hygiene and safety standards

Manage inventory and order supplies

Train and supervise kitchen staff

Collaborate with management on menu planning
Qualifications:

Culinary degree or equivalent experience

Minimum 3 years working in a professional kitchen

Knowledge of food safety regulations

Strong leadership and teamwork skills
Benefits:

Competitive salary

Staff meals provided

Health and accident insurance

Opportunity for promotion

Professional development workshops

3. Waitstaff

Department: Food & Beverage
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Take customer orders accurately

Serve food and beverages efficiently

Ensure guest satisfaction

Maintain cleanliness of tables and dining area

Collaborate with kitchen and bar staff
Qualifications:

Previous restaurant service experience preferred

Excellent communication and customer service skills

Ability to work in a fast-paced environment

Flexible with shifts
Benefits:

Competitive hourly wage

Tips and bonuses

Staff meals

Health insurance options

Training and career advancement

4. Housekeeping Supervisor

Department: Housekeeping
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Supervise housekeeping staff

Maintain cleanliness standards across guest rooms and public areas

Schedule daily cleaning tasks

Inspect rooms for quality assurance

Report maintenance issues to management
Qualifications:

Previous housekeeping experience required

Leadership and organizational skills

Attention to detail

Ability to work flexible hours
Benefits:

Competitive salary

Staff accommodation options

Health insurance

Training programs

5. Concierge

Department: Front Office
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Assist guests with inquiries and special requests

Provide information about local attractions and services

Arrange transportation and reservations

Ensure guest satisfaction and loyalty
Qualifications:

Excellent communication and interpersonal skills

Previous concierge or customer service experience preferred

Knowledge of local area
Benefits:

Competitive salary

Staff meals

Health insurance

Training programs

6. Bartender

Department: Food & Beverage / Bar
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Prepare and serve beverages to guests

Maintain cleanliness and organization of bar area

Monitor inventory and order supplies

Ensure compliance with alcohol regulations
Qualifications:

Previous bartending experience

Knowledge of cocktails and beverages

Good communication and customer service skills
Benefits:

Competitive hourly wage

Tips

Staff meals

Health insurance options

7. Event Coordinator

Department: Banquet / Events
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Plan and coordinate events and banquets

Communicate with clients regarding event requirements

Oversee setup and execution of events

Coordinate with catering and housekeeping teams
Qualifications:

Previous event planning experience preferred

Excellent organizational and communication skills

Ability to multitask
Benefits:

Competitive salary

Staff meals

Health insurance

Training programs

8. Maintenance Technician

Department: Engineering / Maintenance
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Perform routine maintenance on hotel facilities and equipment

Respond to urgent repair requests

Maintain maintenance logs and reports

Ensure compliance with safety regulations
Qualifications:

Technical or engineering background

Previous maintenance experience

Ability to troubleshoot and solve problems
Benefits:

Competitive salary

Health insurance

Training programs

Accommodation options (if required)

9. Spa Therapist

Department: Spa & Wellness
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Provide massages and wellness treatments to guests

Maintain cleanliness of spa facilities

Promote spa services to guests

Ensure guest comfort and satisfaction
Qualifications:

Certified in massage therapy or wellness services

Previous spa experience preferred

Good communication and interpersonal skills
Benefits:

Competitive salary

Tips

Health insurance

Training programs

10. Sales & Marketing Executive

Department: Sales & Marketing
Location: Luxor Grand Hotel, Downtown City
Responsibilities:

Promote hotel services to potential clients

Develop marketing campaigns and sales strategies

Build relationships with corporate and individual clients
Monitor market trends and competitor activities
Qualifications:
Degree in marketing, business, or related field
Previous sales experience preferred
Strong communication and negotiation skills

Benefits:

Competitive salary

Commission or performance bonuses

Staff meals

Health insurance

Professional development programs
```