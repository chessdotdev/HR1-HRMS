<?php
session_start();
include 'includes/header.php';

if ($employee['employment_status'] === 'New Hire') {
    header("Location: onboarding.php");
    exit();
}
?>

<div class="page-header mb-3">
    <h1 class="page-title">Welcome, <?= htmlspecialchars($employee['firstname']) ?>!</h1>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Employee Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Full Name</small>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Job Title</small>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($employee['job_title'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Email</small>
                        <p class="mb-0"><?= htmlspecialchars($employee['email']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Phone</small>
                        <p class="mb-0"><?= htmlspecialchars($employee['phone']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Employment Status</small>
                        <p class="mb-0">
                            <span class="badge bg-success"><?= htmlspecialchars($employee['employment_status']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Hired Date</small>
                        <p class="mb-0"><?= date('F d, Y', strtotime($employee['hired_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="profile.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-person"></i> View Profile
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark-text"></i> Performance
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-trophy"></i> Recognition
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-megaphone"></i> Announcements
    </div>
    <div class="card-body">
        <p class="text-muted text-center py-3">No announcements at this time.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
