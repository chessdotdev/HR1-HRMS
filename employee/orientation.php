<?php
session_start();
include 'includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Orientation Schedule</h1>
            <p class="page-subtitle">Your 3-day orientation program</p>
        </div>
        <a href="onboarding.php" class="btn btn-outline-secondary btn-sm">← Back to Onboarding</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Day 1</span>
                <?php
                $status = $onboarding['orientation_day1_status'] ?? 'Pending';
                $badgeClass = $status === 'Completed' ? 'bg-success' : ($status === 'Missed' ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
            </div>
            <div class="card-body">
                <?php if ($onboarding['orientation_day1_date']): ?>
                    <p class="mb-2"><strong>Date:</strong> <?= date('F d, Y', strtotime($onboarding['orientation_day1_date'])) ?></p>
                <?php else: ?>
                    <p class="mb-2 text-muted">Date: To be announced</p>
                <?php endif; ?>
                <hr>
                <h6 style="font-size: 0.9rem; font-weight: 600;">Topics:</h6>
                <ul style="font-size: 0.8rem; color: #71717a;">
                    <li>Company Overview & Culture</li>
                    <li>HR Policies & Procedures</li>
                    <li>Employee Benefits</li>
                    <li>Workplace Safety</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Day 2</span>
                <?php
                $status = $onboarding['orientation_day2_status'] ?? 'Pending';
                $badgeClass = $status === 'Completed' ? 'bg-success' : ($status === 'Missed' ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
            </div>
            <div class="card-body">
                <?php if ($onboarding['orientation_day2_date']): ?>
                    <p class="mb-2"><strong>Date:</strong> <?= date('F d, Y', strtotime($onboarding['orientation_day2_date'])) ?></p>
                <?php else: ?>
                    <p class="mb-2 text-muted">Date: To be announced</p>
                <?php endif; ?>
                <hr>
                <h6 style="font-size: 0.9rem; font-weight: 600;">Topics:</h6>
                <ul style="font-size: 0.8rem; color: #71717a;">
                    <li>Department Introduction</li>
                    <li>Job Role & Responsibilities</li>
                    <li>Systems & Tools Training</li>
                    <li>Team Meet & Greet</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Day 3</span>
                <?php
                $status = $onboarding['orientation_day3_status'] ?? 'Pending';
                $badgeClass = $status === 'Completed' ? 'bg-success' : ($status === 'Missed' ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
            </div>
            <div class="card-body">
                <?php if ($onboarding['orientation_day3_date']): ?>
                    <p class="mb-2"><strong>Date:</strong> <?= date('F d, Y', strtotime($onboarding['orientation_day3_date'])) ?></p>
                <?php else: ?>
                    <p class="mb-2 text-muted">Date: To be announced</p>
                <?php endif; ?>
                <hr>
                <h6 style="font-size: 0.9rem; font-weight: 600;">Topics:</h6>
                <ul style="font-size: 0.8rem; color: #71717a;">
                    <li>Hands-on Training</li>
                    <li>Performance Expectations</li>
                    <li>Q&A Session</li>
                    <li>Onboarding Completion</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Important Information -->
<div class="card mt-3" style="border-left: 4px solid #3b82f6;">
    <div class="card-body">
        <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600;">
            <i class="bi bi-info-circle"></i> Important Information
        </h6>
        <ul style="font-size: 0.8rem; color: #71717a; margin-bottom: 0;">
            <!-- <li>Orientation dates will be scheduled by HR and also you will be notified via email</li> -->
            <li>Please arrive 15 minutes before the scheduled time</li>
            <li>Bring a valid ID and notebook for taking notes</li>
            <li>Attendance is mandatory for all 3 days</li>
            <li>Contact HR if you need to reschedule due to emergency</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
