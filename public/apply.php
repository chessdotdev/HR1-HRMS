<?php
include '../includes/header.php';
include '../includes/verify_auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $suffix = $_POST['suffix'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $skills = trim($_POST['skills'] ?? '');
    
    // Validation
    if (!$firstName) { $errors['firstName'] = "First Name is required"; }
    if (!$lastName) { $errors['lastName'] = "Last Name is required"; }
    if (!$birthdate) { $errors['birthdate'] = "Birthdate is required"; }
    if (!$phone) { $errors['phone'] = "Phone is required"; }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = "Valid Email is required"; }
    if (!$gender) { $errors['gender'] = "Gender is required"; }
    if (!$skills) { $errors['skills'] = "Skills are required"; }
    
    // Calculate age
    $age = '';
    if ($birthdate && empty($errors['birthdate'])) {
        $birthDateObj = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDateObj)->y;
    }
    // echo $age->format('%y years %d days');    
    echo $age;
}

?>

<style>
.shadcn-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    background-color: #111827; /* dark neutral */
    color: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.shadcn-btn:hover {
    background-color: #1f2937;
}

.shadcn-btn:active {
    transform: scale(0.98);
}

.shadcn-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
}

.shadcn-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<div class="container py-4" style="max-width: 720px;">
    <div class="page-title text-center mb-4">
        <h1 class="apply-title">Apply Now</h1>
        <p class="page-subtitle mx-auto" style="max-width: 420px;">
           Fill out the form below to get started.
        </p>
    </div>

    <form action="apply.php" method="POST" id="applyForm" class="form-card p-4">
        <!-- Personal Info -->

        <div class="form-step" id="step-0">
            <h2 class="section-title heading-font">Personal Information</h2>
            <p class="section-desc">Please provide your basic personal details.</p>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="firstName">First Name <span class="required">*</span></label>
                    <input type="text" class="form-control <?=isset($errors['firstName']) ? 'is-invalid' : '';?>" name="firstName" placeholder="Juan" />
                    <div class="invalid-feedback">Firstname is required.</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="lastName">Last Name <span class="required">*</span></label>
                    <input type="text" class="form-control <?=isset($errors['lastName']) ? 'is-invalid' : '';?>" name="lastName" placeholder="Dela Cruz" />
                    <div class="invalid-feedback">Last name is required.</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-sm-6">
                <label class="form-label" for="middleName">Middle Name</label>
                <input type="text" class="form-control" name="middleName" placeholder="Santos" />
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
                <label class="form-label" for="birthdate">Birthdate <span class="required">*</span></label>
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
                    <label class="form-label" for="lastName">Phone <span class="required">*</span></label>
                    <input type="number" class="form-control <?=isset($errors['phone']) ? 'is-invalid' : '';?>" id="phone" placeholder="delacruz@gmail.com" />
                    <div class="invalid-feedback">Phone is required.</div>
                </div>

            <div class="col-sm-6">
                <label class="form-label" for="gender">Gender</label>
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
                <label class="form-label" for="skills">Describe your skills</label>
                <textarea class="form-control <?=isset($errors['skills']) ? 'is-invalid' : '';?>" name="skills" placeholder="Describe your skills here..." rows="4"></textarea>
                    <div class="invalid-feedback">Describe your skills is required.</div>
                </div>
        </div>


        <div class="d-flex justify-content-between mt-4">
            <button type="submit" id="submit" class="shadcn-btn">
                Submit
            </button>
        </div>
    </form>
</div>

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