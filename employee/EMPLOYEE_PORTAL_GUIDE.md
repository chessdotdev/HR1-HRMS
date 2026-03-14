# Employee Portal Implementation Guide

## Overview
Complete employee portal with **onboarding flow** for Hotel & Restaurant HR System.

---

## Setup Instructions

### 1. Database Setup
Run the SQL schema file to create the `employee_onboarding` table:
```sql
-- Import the file: employee_onboarding_schema.sql
```

### 2. Create Upload Directory
Create the directory for employee documents:
```
/public/uploads/employee_documents/
```
Make sure it has write permissions (chmod 777 or appropriate permissions).

### 3. File Structure
```
employee/
├── login.php                 # Employee login page
├── dashboard.php             # Active employee dashboard
├── onboarding.php            # Onboarding progress page
├── personal_info.php         # Personal information form
├── documents.php             # Document upload page
├── orientation.php           # Orientation schedule
├── profile.php               # Employee profile
└── includes/
    ├── header.php            # Portal header with navigation
    └── footer.php            # Portal footer
```

---

## Employee Flow

### **For New Hires (Just Hired):**
```
Login → Onboarding Dashboard → Complete 3 Tasks:
  1. Personal Information (Emergency contact, IDs, Address, Bank)
  2. Document Upload (Gov ID, Diploma, NBI, Medical)
  3. Orientation Schedule (View 3-day schedule)
→ All Complete → Status changes to "Active"
```

### **For Active Employees:**
```
Login → Employee Dashboard → View Profile, Performance, Recognition
```

---

## Login Credentials

### How Employees Get Login Credentials:
1. Applicant passes interview
2. Admin marks interview result as "Passed"
3. System automatically:
   - Creates employee account
   - Generates username and password
   - Sends email with credentials
4. Employee logs in at: `/employee/login.php`

### Login Redirect Logic:
- **New Hire** → Redirects to `onboarding.php`
- **Active Employee** → Redirects to `dashboard.php`

---

## Onboarding Progress Tracking

### Progress Calculation:
- **Personal Info Completed** = 33.33%
- **Documents Submitted** = 33.33%
- **Orientation Completed** = 33.33%
- **Total** = 100%

### Status Updates:
- When employee completes any section → `onboarding_status` = "In Progress"
- When all 3 sections complete → `onboarding_status` = "Completed"
- When onboarding complete → `employment_status` = "Active"

---

## Admin Side Integration

### Admin needs to:
1. **Schedule Orientation Dates** (in admin panel - to be implemented)
   - Set `orientation_day1_date`, `orientation_day2_date`, `orientation_day3_date`
   
2. **Mark Orientation Attendance** (in admin panel - to be implemented)
   - Update `orientation_day1_status`, `orientation_day2_status`, `orientation_day3_status`
   - Options: Pending, Completed, Missed

3. **View New Hires** (in admin panel - to be implemented)
   - List all employees with `employment_status = 'New Hire'`
   - Show onboarding progress for each

---

## Database Tables

### `employees` table:
- `employee_id` - Primary key
- `applicant_id` - Foreign key to applicantss
- `username` - Login username
- `password` - Hashed password
- `employment_status` - New Hire, Active, Inactive, Terminated
- `onboarding_status` - Not Started, In Progress, Completed
- `hired_at` - Hire date

### `employee_onboarding` table:
- `onboarding_id` - Primary key
- `employee_id` - Foreign key to employees
- `applicant_id` - Foreign key to applicantss
- Personal info fields (emergency contact, IDs, address, bank)
- Document paths (government_id, diploma, nbi, medical)
- Orientation fields (dates and status for 3 days)
- `onboarding_status` - Overall status
- Timestamps

---

## Features Implemented

### Employee Portal:
 Login page with authentication
 Onboarding dashboard with progress tracking
 Personal information form (Emergency, IDs, Address, Bank)
 Document upload (Gov ID, Diploma, NBI, Medical)
 Orientation schedule viewer
 Employee dashboard (for active employees)
 Profile page

### Employee Class (modules/Employee.php):
 Login authentication
 Get employee data
 Get/Create onboarding record
 Update personal information
 Update documents
 Update orientation status
 Calculate onboarding progress Auto-update employment status when complete

---

## Next Steps (To Be Implemented)

### Admin Panel Features:
1. **New Hires Management** (`admin/new_hires.php`)
   - List all new hires
   - View onboarding progress
   - Schedule orientation dates

2. **Onboarding Tasks** (`admin/onboarding_tasks.php`)
   - View submitted personal info
   - Review uploaded documents
   - Approve/reject submissions

3. **Orientation Schedule** (`admin/orientation_schedule.php`)
   - Set orientation dates for new hires
   - Mark attendance (Completed/Missed)
   - Send reminders

---

## Testing

### Test the Flow:
1. Create an applicant
2. Schedule interview
3. Mark interview as "Passed"
4. Check email for employee credentials
5. Login at `/employee/login.php`
6. Complete onboarding tasks
7. Verify status changes to "Active"

---

## Security Notes

- All passwords are hashed using `password_hash()`
- File uploads are validated (type and size)
- Session-based authentication
- SQL injection prevention with prepared statements
- XSS prevention with `htmlspecialchars()`

---

## Support

For issues or questions, refer to the main README.md file.
