
<?php
include '../includes/header.php';
include '../includes/verify_auth.php';

?>

<div class="card mx-auto" style="max-width: 600px;">
    <div class="card-header text-center">
        <h4>Job Application Form</h4>
    </div>
    <div class="card-body">
        <!-- <?php if ($messageErr): ?>
            <div class="alert alert-danger"><?php echo $messageErr; ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo $successMsg; ?></div>
        <?php endif; ?> -->

        <form method="POST" enctype="multipart/form-data">
            <!-- Personal Info -->
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Job Details -->
            <div class="mb-3">
                <label class="form-label">Position Applying For</label>
                <select name="position" class="form-control" required>
                    <option value="">Select Position</option>
                    <option value="HR Assistant">HR Assistant</option>
                    <option value="Recruiter">Recruiter</option>
                    <option value="HR Manager">HR Manager</option>
                    <option value="Onboarding Specialist">Onboarding Specialist</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Resume (PDF)</label>
                <input type="file" name="resume" class="form-control" accept=".pdf" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Application</button>
        </form>
    </div>
</div>


<?php include '../includes/footer.php'; ?>