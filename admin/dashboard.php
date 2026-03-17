<?php
require_once '../modules/Recruitment.php';
require_once '../modules/Applicants.php';

$recruitment = new Recruitment();
$applicantObj = new Applicants();

// Get statistics
$openJobsStmt = $recruitment->getOpenJobs();
$openJobsCount = $openJobsStmt->rowCount();

$pendingApplicants = $applicantObj->getApplicants('Pending');
$pendingCount = count($pendingApplicants);

$interviewApplicants = $applicantObj->getApplicants('Interview');
$interviewCount = count($interviewApplicants);

$hiredApplicants = $applicantObj->getApplicants('Hired');
$hiredCount = count($hiredApplicants);

// Get recent applicants
$recentApplicants = array_slice($applicantObj->getApplicants(), 0, 3);

// Get all jobs for recent postings
$allJobsStmt = $recruitment->getAllJobs();
$recentJobs = array_slice($allJobsStmt->fetchAll(PDO::FETCH_ASSOC), 0, 3);

?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="p-3 mb-4 bg-light rounded-3">
        <div class="container-fluid py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h1 class="display-6 fw-bold mb-0">Hotel &amp; Restaurant Management System</h1>
                <span style="font-size: 12px; font-family: monospace; color: #888; background: #fff; border: 0.5px solid #ddd; padding: 5px 12px; border-radius: 20px; white-space: nowrap;">
                    <?= date('D, M d Y') ?>
                </span>
            </div>
            <p class="text-muted mb-0 mt-1">Welcome back! Here's what's happening today.</p>
        </div>
    </div>
 

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Open Jobs</h6>
                            <h2 class="mt-2 mb-0"><?= $openJobsCount ?></h2>
                        </div>
                        <i class="bi bi-briefcase" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                    <a href="job_openings.php" class="btn btn-light fw-bold btn-sm mt-3">Manage Jobs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Pending Applicants</h6>
                            <h2 class="mt-2 mb-0"><?= $pendingCount ?></h2>
                        </div>
                        <i class="bi bi-person-lines-fill" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                    <a href="applicants.php" class="btn btn-light fw-bold btn-sm mt-3">View Applicants</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Interviews Scheduled</h6>
                            <h2 class="mt-2 mb-0"><?= $interviewCount ?></h2>
                        </div>
                        <i class="bi bi-calendar-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                    <a href="interviews.php" class="btn btn-light fw-bold btn-sm mt-3">View Interviews</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Hired</h6>
                            <h2 class="mt-2 mb-0"><?= $hiredCount ?></h2>
                        </div>
                        <i class="bi bi-person-check-fill" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                    <a href="employee_list.php" class="btn btn-light fw-bold btn-sm mt-3">View Employees</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Recent Applicants</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($recentApplicants)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($recentApplicants as $app): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($app['firstname'] . ' ' . $app['lastname']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($app['job_title']) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $app['status'] === 'Pending' ? 'warning' : ($app['status'] === 'Interview' ? 'info' : ($app['status'] === 'Rejected' ? 'danger' : 'success')) ?>">
                                        <?= htmlspecialchars($app['status']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="applicants.php" class="btn btn-sm btn-outline-dark fw-bold mt-3 w-100">View All Applicants</a>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No recent applicants</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-briefcase"></i> Recent Job Postings</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($recentJobs)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($recentJobs as $job): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($job['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($job['department']) ?> • <?= date('M d, Y', strtotime($job['created_at'])) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $job['status'] === 'open' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(htmlspecialchars($job['status'])) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="job_openings.php" class="btn btn-sm btn-outline-dark fw-bold mt-3 w-100">View All Jobs</a>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No job postings yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="job_openings.php" class="btn btn-sm btn-outline-dark fw-bold w-100">
                                <i class="bi bi-plus-circle"></i> Post New Job
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="applicants.php" class="btn btn-sm btn-outline-dark fw-bold w-100">
                                <i class="bi bi-eye"></i> Review Applicants
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="interviews.php" class="btn btn-sm btn-outline-dark fw-bold w-100">
                                <i class="bi bi-calendar-plus"></i> Schedule Interview
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="employee_list.php" class="btn btn-sm btn-outline-dark fw-bold w-100">
                                <i class="bi bi-people"></i> Manage Employees
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
