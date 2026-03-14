<?php
require_once '../modules/Employee.php';

$db   = new Database();
$conn = $db->connect();

// Fetch all new hires with their onboarding task columns
$stmt = $conn->prepare("
    SELECT e.employee_id, e.hired_at,
           a.firstname, a.lastname, a.job_title,
           eo.personal_info_status, eo.documents_status,
           eo.orientation_completed, eo.onboarding_status,
           eo.orientation_day1_status, eo.orientation_day2_status, eo.orientation_day3_status
    FROM employees e
    LEFT JOIN applicantss a  ON e.applicant_id = a.apply_id
    LEFT JOIN employee_onboarding eo ON e.employee_id = eo.employee_id
    WHERE e.employment_status = 'New Hire'
    ORDER BY e.hired_at DESC
");
$stmt->execute();
$hires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$total       = count($hires);
$notStarted  = 0;
$inProgress  = 0;
$completed   = 0;

foreach ($hires as $h) {
    $s = $h['onboarding_status'] ?? 'Not Started';
    if ($s === 'Completed')   $completed++;
    elseif ($s === 'In Progress') $inProgress++;
    else $notStarted++;
}

include 'includes/header.php';
?>

<div class="main p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Onboarding Tasks</h2>
            <p class="text-muted mb-0">Track task completion for all new hires</p>
        </div>
        <span class="badge bg-info text-dark fs-6"><?= $total ?> New Hires</span>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="fs-3 fw-bold"><?= $total ?></div>
                    <div class="small text-muted">Total</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-secondary">
                <div class="card-body py-2">
                    <div class="fs-3 fw-bold text-secondary"><?= $notStarted ?></div>
                    <div class="small text-muted">Not Started</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body py-2">
                    <div class="fs-3 fw-bold text-warning"><?= $inProgress ?></div>
                    <div class="small text-muted">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body py-2">
                    <div class="fs-3 fw-bold text-success"><?= $completed ?></div>
                    <div class="small text-muted">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Job Title</th>
                            <th>Hired Date</th>
                            <th class="text-center">Personal Info</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Orientation</th>
                            <th class="text-center">Overall</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($hires)): ?>
                        <?php foreach ($hires as $h): ?>
                        <?php
                        $piStatus  = $h['personal_info_status'] ?? 'Not Submitted';
                        $docStatus = $h['documents_status'] ?? 'Not Submitted';

                        // Orientation: check all 3 days
                        $oriDone = $h['orientation_completed'] ? true : false;
                        $oriPending = !$oriDone && (
                            $h['orientation_day1_status'] === 'Pending' &&
                            $h['orientation_day2_status'] === 'Pending' &&
                            $h['orientation_day3_status'] === 'Pending'
                        );

                        $overallStatus = $h['onboarding_status'] ?? 'Not Started';
                        $overallBadge  = match($overallStatus) {
                            'Completed'   => 'bg-success',
                            'In Progress' => 'bg-warning text-dark',
                            default       => 'bg-secondary',
                        };

                        // Progress %
                        $done = 0;
                        if ($piStatus === 'Approved')  $done++;
                        if ($docStatus === 'Approved') $done++;
                        if ($oriDone) $done++;
                        $pct = round(($done / 3) * 100);
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($h['firstname'] . ' ' . $h['lastname']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($h['job_title'] ?? 'N/A') ?></td>
                            <td><?= date('M d, Y', strtotime($h['hired_at'])) ?></td>

                            <!-- Personal Info -->
                            <td class="text-center">
                                <?php
                                echo match($piStatus) {
                                    'Approved'       => '<i class="bi bi-check-circle-fill text-success fs-5" title="Approved"></i>',
                                    'Pending Review' => '<i class="bi bi-hourglass-split text-info fs-5" title="Pending Review"></i>',
                                    'Rejected'       => '<i class="bi bi-x-circle-fill text-danger fs-5" title="Rejected"></i>',
                                    default          => '<i class="bi bi-circle text-secondary fs-5" title="Not Submitted"></i>',
                                };
                                ?>
                            </td>

                            <td class="text-center">
                                <?php
                                echo match($docStatus) {
                                    'Approved'       => '<i class="bi bi-check-circle-fill text-success fs-5" title="Approved"></i>',
                                    'Pending Review' => '<i class="bi bi-hourglass-split text-info fs-5" title="Pending Review"></i>',
                                    'Rejected'       => '<i class="bi bi-x-circle-fill text-danger fs-5" title="Rejected"></i>',
                                    default          => '<i class="bi bi-circle text-secondary fs-5" title="Not Submitted"></i>',
                                };
                                ?>
                            </td>

                            <td class="text-center">
                                <?php if ($oriDone): ?>
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="Completed"></i>
                                <?php elseif (!$oriPending): ?>
                                    <i class="bi bi-hourglass-split text-warning fs-5" title="In Progress"></i>
                                <?php else: ?>
                                    <i class="bi bi-circle text-secondary fs-5" title="Pending"></i>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                    <div class="progress" style="width:70px; height:8px;">
                                        <div class="progress-bar" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small><?= $pct ?>%</small>
                                </div>
                                <span class="badge <?= $overallBadge ?> mt-1" style="font-size:0.7rem;"><?= $overallStatus ?></span>
                            </td>

                            <td>
                                <a href="view_new_hire.php?id=<?= $h['employee_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-person-check" style="font-size:2rem;color:#ccc;"></i>
                                <p class="mt-2 mb-0">No new hires at the moment</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>


            <div class="d-flex gap-3 mt-2" style="font-size:0.78rem; color:#71717a;">
                <span><i class="bi bi-check-circle-fill text-success"></i> Approved</span>
                <span><i class="bi bi-hourglass-split text-info"></i> Pending Review</span>
                <span><i class="bi bi-x-circle-fill text-danger"></i> Rejected</span>
                <span><i class="bi bi-circle text-secondary"></i> Not Submitted</span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
