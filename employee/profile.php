<?php
session_start();
include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">View your personal and employment information</p>
</div>

<div class="card mb-3">
    <div class="card-header">Personal & Applicant Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <small class="text-muted">Full Name</small>
                <p class="mb-0 fw-semibold">
                    <?= htmlspecialchars(trim($employee['firstname'] . ' ' . $employee['middle_name'] . ' ' . $employee['lastname'] . ' ' . $employee['suffix'])) ?>
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Email</small>
                <p class="mb-0"><?= htmlspecialchars($employee['email'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Phone</small>
                <p class="mb-0"><?= htmlspecialchars($employee['phone'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Position</small>
                <p class="mb-0"><?= htmlspecialchars($employee['job_title'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Birthdate</small>
                <p class="mb-0"><?= htmlspecialchars($employee['birthdate'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Age</small>
                <p class="mb-0"><?= htmlspecialchars($employee['age'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Gender</small>
                <p class="mb-0"><?= htmlspecialchars($employee['gender'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Hire Date</small>
                <p class="mb-0"><?=!empty($employee['hired_at']) ? date('F, d, Y', strtotime($employee['hired_at'])) : '' ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Employment Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <small class="text-muted">Employee ID</small>
                <p class="mb-0 fw-semibold"><?= htmlspecialchars($employee['employee_id']) ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Username</small>
                <p class="mb-0"><?= htmlspecialchars($employee['username']) ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Employment Status</small>
                <p class="mb-0">
                    <span class="badge bg-<?= $employee['employment_status'] === 'Active' ? 'success' : 'warning' ?>">
                        <?= htmlspecialchars($employee['employment_status']) ?>
                    </span>
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Hired Date</small>
                <p class="mb-0"><?= date('F d, Y', strtotime($employee['hired_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<?php if ($onboarding): ?>
<div class="card">
    <div class="card-header">Onboarding Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <small class="text-muted">Personal Info</small>
                <p class="mb-0">
                    <?php if ($onboarding['personal_info_completed']): ?>
                        <span class="badge bg-success">✓ Completed</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted">Documents</small>
                <p class="mb-0">
                    <?php if ($onboarding['documents_submitted']): ?>
                        <span class="badge bg-success">✓ Completed</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted">Orientation</small>
                <p class="mb-0">
                    <?php if ($onboarding['orientation_completed']): ?>
                        <span class="badge bg-success">✓ Completed</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-12">
                <small class="text-muted">Overall Status</small>
                <p class="mb-0">
                    <span class="badge bg-<?= $onboarding['onboarding_status'] === 'Completed' ? 'success' : 'info' ?>">
                        <?= htmlspecialchars($onboarding['onboarding_status']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
