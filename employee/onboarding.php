<?php
session_start();
include 'includes/header.php';

// Check if onboarding record exists, if not create one
if (!$onboarding) {
    $employeeObj->createOnboardingRecord($_SESSION['employee_id'], $employee['applicant_id']);
    $onboarding = $employeeObj->getOnboardingData($_SESSION['employee_id']);
    $progress = 0;
}
?>

<div class="page-header">
    <h1 class="page-title">Welcome, <?= htmlspecialchars($employee['firstname']) ?>!</h1>
    <p class="page-subtitle">Complete your onboarding to get started</p>
</div>

<div class="card">
    <div class="card-header">
        Onboarding Progress
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-size: 0.875rem; color: #71717a;">Overall Completion</span>
            <span style="font-size: 1.25rem; font-weight: 600;"><?= $progress ?>%</span>
        </div>
        <div class="progress" style="height: 12px;">
            <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%"></div>
        </div>
        <p class="mt-3 mb-0" style="font-size: 0.8rem; color: #71717a;">
            <?php if ($progress == 100): ?>
                🎉 Congratulations! You've completed all onboarding tasks.
            <?php else: ?>
                Complete all sections below to activate your employee account.
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="row g-3">
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1" style="font-size: 1rem;">Personal Information</h5>
                        <p class="mb-0" style="font-size: 0.8rem; color: #71717a;">Complete your personal details</p>
                    </div>
                    <?php
                    $piStatus = $onboarding['personal_info_status'] ?? 'Not Submitted';
                    $piBadge = match($piStatus) {
                        'Approved'       => '<span class="badge bg-success">✓ Approved</span>',
                        'Pending Review' => '<span class="badge bg-info text-dark">Pending Review</span>',
                        'Rejected'       => '<span class="badge bg-danger">✗ Rejected</span>',
                        default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                    };
                    echo $piBadge;
                    ?>
                </div>
                <ul style="font-size: 0.8rem; color: #71717a; padding-left: 1.25rem;">
                    <li>Emergency Contact</li>
                    <li>Government IDs (TIN, SSS, etc.)</li>
                    <li>Address & Contact</li>
                    <li>Bank Details</li>
                </ul>
                <a href="personal_info.php" class="btn btn-primary btn-sm w-100 mt-3">
                    <?php
                    echo match($piStatus) {
                        'Approved'       => 'View Details',
                        'Pending Review' => 'View Submitted',
                        'Rejected'       => 'Re-submit Info',
                        default          => 'Complete Now',
                    };
                    ?>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1" style="font-size: 1rem;">Document Submission</h5>
                        <p class="mb-0" tyle="font-size: 0.8rem; color: #71717a;">Upload required documents</p>
                    </div>
                    <?php
                    $docStatus = $onboarding['documents_status'] ?? 'Not Submitted';
                    $docBadge = match($docStatus) {
                        'Approved'       => '<span class="badge bg-success">✓ Approved</span>',
                        'Pending Review' => '<span class="badge bg-info text-dark">Pending Review</span>',
                        'Rejected'       => '<span class="badge bg-danger">✗ Rejected</span>',
                        default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                    };
                    echo $docBadge;
                    ?>
                </div>
                <ul style="font-size: 0.8rem; color: #71717a; padding-left: 1.25rem;">
                    <li>Government IDs</li>
                    <li>Diploma or TOR</li>
                    <li>NBI Clearance (Optional)</li>
                    <li>Medical Certificate (Optional)</li>
                </ul>
                <a href="documents.php" class="btn btn-primary btn-sm w-100 mt-3">
                    <?php
                    echo match($docStatus) {
                        'Approved'       => 'View Documents',
                        'Pending Review' => 'View Submitted',
                        'Rejected'       => 'Re-upload Documents',
                        default          => 'Upload Now',
                    };
                    ?>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1" style="font-size: 1rem;">Orientation Schedule</h5>
                        <p class="mb-0" style="font-size: 0.8rem; color: #71717a;">3-day orientation program</p>
                    </div>
                    <?php if ($onboarding['orientation_completed']): ?>
                        <span class="badge bg-success">✓ Done</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </div>
                <ul style="font-size: 0.8rem; color: #71717a; padding-left: 1.25rem;">
                    <li>Day 1: <?= $onboarding['orientation_day1_status'] ?? 'Pending' ?></li>
                    <li>Day 2: <?= $onboarding['orientation_day2_status'] ?? 'Pending' ?></li>
                    <li>Day 3: <?= $onboarding['orientation_day3_status'] ?? 'Pending' ?></li>
                </ul>
                <a href="orientation.php" class="btn btn-primary btn-sm w-100 mt-3">
                    View Schedule
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Important Notice -->
<?php if ($progress < 100): ?>
<div class="card mt-3" style="border-left: 4px solid #f59e0b;">
    <div class="card-body">
        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600;">
            <i class="bi bi-info-circle"></i> Important Notice
        </h6>
        <p class="mb-0" style="font-size: 0.8rem; color: #71717a;">
            Please complete all onboarding tasks within 7 days. Your employee account will be activated once all sections are completed.
        </p>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
