<?php
include '../includes/header.php';
include '../includes/verify_applicant.php';
require_once '../modules/Applicants.php';
  
$job_application = new Applicants();

// Get job ID and title from URL or POST (available before form submission)
$job_id = $_GET['job_id'] ?? '';
$job_title = $_GET['title'] ?? '';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName  = trim(filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_SPECIAL_CHARS));
    $lastName   = trim(filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_SPECIAL_CHARS));
    $middleName = trim(filter_input(INPUT_POST, 'middleName', FILTER_SANITIZE_SPECIAL_CHARS));
    $suffix     = trim(filter_input(INPUT_POST, 'suffix', FILTER_SANITIZE_SPECIAL_CHARS));
    $birthdate  = trim(filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_SPECIAL_CHARS));
    $phone      = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $email      = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $gender     = trim(filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_SPECIAL_CHARS));
    $skills = $_POST['skills'] ?? '';
    $skills = trim($skills);
    $applicant_id = $_SESSION['applicant_id'];

    /* overwrite get method */
    $job_id = $_POST['job_id'] ?? $_GET['job_id'] ?? '';   
    $job_title = $_POST['title'] ?? $_GET['title'] ?? ''; 
    
    // Validation
    if (!$firstName) { $errors['firstName'] = "First Name is required"; }
    if (!$lastName) { $errors['lastName'] = "Last Name is required"; }
    if (!$middleName) { $errors['middlename'] = "Middle Name is required"; }
    if (!$birthdate) { $errors['birthdate'] = "Birthdate is required"; }
    if (!$phone) { $errors['phone'] = "Phone is required"; }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = "Valid Email is required"; }
    if (!$gender) { $errors['gender'] = "Gender is required"; }
    if (!$skills) { $errors['skills'] = "Skills are required"; }
    
    // Calculate age
    $age = null;
    if ($birthdate && empty($errors['birthdate'])) {
        $birthDateObj = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDateObj)->y;
    }
    // echo $age->format('%y years %d days');    
    // echo $age;
    $allowed_suffixes = ['none', 'jr', 'sr', 'ii', 'iii', 'iv'];
    if(!in_array(strtolower($suffix), $allowed_suffixes)){
        $suffix = 'none';
    }

    $allowed_genders = ['Male', 'Female'];
    if(!in_array($gender, $allowed_genders)){
        $errors['gender'] = "Gender is required";
    }

    if(empty($errors)){
        
        $applyJob = $job_application->applyJob(
            $applicant_id,
            $job_id,
            $job_title,
            $firstName,
            $lastName,
            $middleName,
            $suffix,
            $birthdate,
            $age,
            $phone,
            $gender,
            $email,
            $skills
        );
        if ($applyJob['success']) {
            $message = "<div class='alert alert-success'>{$applyJob['message']}</div>";
        } else {
            $message ="<div class='alert alert-danger'>{$applyJob['message']}</div>";
        }

    }
}

?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Lora:wght@400;600&display=swap');
     body {
    background-color: #fafaf8;
    font-family: 'Inter', sans-serif;
    color: #1a1a1a;
    }

    .navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e8e8e4;
    padding: 1rem 0;
}
.navbar-brand {
    font-family: 'Lora', serif;
    font-size: 1.1rem;
    color: #1a1a1a !important;
    letter-spacing: 0.01em;
}
.nav-link { color: #555 !important; font-size: 0.875rem; }
.nav-link:hover { color: #1a1a1a !important; }
</style>

<div class="container py-4" style="max-width: 720px;">
    <div class="page-title text-center mb-4">
        <h1 class="apply-title">Apply Now</h1>
        <p class="page-subtitle mx-auto" style="max-width: 420px;">
           Fill out the form below to get started.
        </p>
        <div class="card mt-3" style="border-left: 4px solid #f59e0b;">
    <div class="card-body">
        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600;">
            <i class="bi bi-info-circle"></i> Important Notice
        </h6>
        <p class="mb-0" style="font-size: 0.7rem; color: #71717a;">
        Please review your answers carefully before submitting this form. Once the form is submitted, you will not be able to edit or change your responses. Make sure all information provided is accurate and complete.        </p>
    </div>
</div>
    </div>
    <?=$message ?? '' ?>

    <form action="apply.php" method="POST" id="applyForm" class="form-card p-4">
        <!-- Personal Info -->
        <input type="hidden" name="job_id" value="<?=htmlspecialchars($job_id ?? '') ?>">
        <input type="hidden" name="title" value="<?=htmlspecialchars($job_title ?? '') ?>">
        <div class="form-step" id="step-0">
            <h2 class="section-title heading-font">Personal Information</h2>
            <p class="section-desc">Please provide your basic personal details.</p>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="firstName">First Name <span class="required text-danger">*</span></label>
                    <input type="text" class="form-control <?=isset($errors['firstName']) ? 'is-invalid' : '';?>" name="firstName" placeholder="Juan" />
                    <div class="invalid-feedback">Firstname is required.</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="lastName">Last Name <span class="required text-danger">*</span></label>
                    <input type="text" class="form-control <?=isset($errors['lastName']) ? 'is-invalid' : '';?>" name="lastName" placeholder="Dela Cruz" />
                    <div class="invalid-feedback">Last name is required.</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-sm-6">
                <label class="form-label" for="middleName">Middle Name <span class="required text-danger">*</span></label>
                <input type="text" class="form-control <?=isset($errors['middlename']) ? 'is-invalid' : '';?>" name="middleName" placeholder="Santos" />
                <div class="invalid-feedback">Middle name is required.</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="suffix">Suffix</label>
                <select class="form-select" name="suffix">
                    <option value="">Select suffix</option>
                    <option value="none">None</option>
                    <option value="jr">Jr.</option>
                    <option value="sr">Sr.</option>
                    <option value="ii">II</option>
                    <option value="iii">III</option>
                    <option value="iv">IV</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-sm-6">
                <label class="form-label" for="birthdate">Birthdate <span class="required text-danger">*</span></label>
                <input type="date" id="birthdate" class="form-control <?=isset($errors['birthdate']) ? 'is-invalid' : '';?>" name="birthdate" />
                <div class="invalid-feedback">Birthdate is required.</div>
            </div>
            
            <div class="col-sm-6">
            <label class="form-label" for="age">Age</label>
            <input type="number" id="age" class="form-control" name="age" readonly />
        </div>
         
        </div>



        <div class="row g-3 mt-1">

        <div class="col-sm-6">
                    <label class="form-label" for="Phone">Phone <span class="required text-danger">*</span></label>
                    <input type="number" class="form-control <?=isset($errors['phone']) ? 'is-invalid' : '';?>" name="phone" placeholder="09123456789" />
                    <div class="invalid-feedback">Phone is required.</div>
                </div>

            <div class="col-sm-6">
                <label class="form-label" for="gender">Gender <span class="required text-danger">*</span></label>
                <select class="form-select <?=isset($errors['gender']) ? 'is-invalid' : '';?>" name="gender">
                    <option value=""></option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div class="invalid-feedback">Gender is required.</div>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-sm-12">
                        <label class="form-label" for="lastName">Email <span class="required text-danger">*</span></label>
                        <input type="text" class="form-control <?=isset($errors['email']) ? 'is-invalid' : '';?>" name="email" placeholder="delacruz@gmail.com" />
                        <div class="invalid-feedback">Email is required.</div>
                    </div>
                </div>



        <div class="row g-3 mt-1">
            <div class="col-sm-12">
                <label class="form-label" for="skills">Describe your skills <span class="required text-danger">*</span> </label>
                <textarea class="form-control <?=isset($errors['skills']) ? 'is-invalid' : '';?>" name="skills" placeholder="Describe your skills here..." rows="4"></textarea>
                    <div class="invalid-feedback">Describe your skills is required.</div>
                </div>
        </div>


        <div class="d-flex justify-content-between mt-4">
            <button type="submit" id="submit" class="apply-btn">
                Submit
            </button>
        </div>
</form>

<script>
    const birthdateInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');

    birthdateInput.addEventListener('change', () => {
    const birthdate = new Date(birthdateInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    const m = today.getMonth() - birthdate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) age--;
    ageInput.value = age >= 0 ? age : '';
});

</script>



<?php include '../includes/footer.php'; ?>