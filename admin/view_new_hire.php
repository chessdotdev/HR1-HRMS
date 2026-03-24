<?php
session_start();
require_once '../modules/Employee.php';
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';

$employeeObj = new Employee();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$employee_id) { header("Location: new_hires.php"); exit(); }

$employee   = $employeeObj->getEmployeeById($employee_id);
$onboarding = $employeeObj->getOnboardingData($employee_id);

if (!$employee || $employee['employment_status'] !== 'New Hire') {
    header("Location: new_hires.php"); exit();
}

// Ensure onboarding record exists
if (!$onboarding) {
    $employeeObj->createOnboardingRecord($employee_id, $employee['applicant_id']);
    $onboarding = $employeeObj->getOnboardingData($employee_id);
}

$actionMsg  = '';
$actionType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'approve_personal_info':
            $employeeObj->approvePersonalInfo($employee_id);
            $audit->log($admin_id, $admin_name, 'Approve Personal Info', 'Onboarding', "Approved personal info for employee ID {$employee_id}");
            $actionMsg = 'Personal information approved.'; $actionType = 'success'; break;
        case 'reject_personal_info':
            $employeeObj->rejectPersonalInfo($employee_id);
            $audit->log($admin_id, $admin_name, 'Reject Personal Info', 'Onboarding', "Rejected personal info for employee ID {$employee_id}");
            $actionMsg = 'Personal information rejected. Employee will be notified to re-submit.'; $actionType = 'danger'; break;
        case 'approve_documents':
            $employeeObj->approveDocuments($employee_id);
            $audit->log($admin_id, $admin_name, 'Approve Documents', 'Onboarding', "Approved documents for employee ID {$employee_id}");
            $actionMsg = 'Documents approved.'; $actionType = 'success'; break;
        case 'reject_documents':
            $employeeObj->rejectDocuments($employee_id);
            $audit->log($admin_id, $admin_name, 'Reject Documents', 'Onboarding', "Rejected documents for employee ID {$employee_id}");
            $actionMsg = 'Documents rejected. Employee will be notified to re-upload.'; $actionType = 'danger'; break;
    }
    $onboarding = $employeeObj->getOnboardingData($employee_id);
    $employee   = $employeeObj->getEmployeeById($employee_id);
}

$progress = $employeeObj->getOnboardingProgress($employee_id);
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <?php if ($actionMsg): ?>
        <div class="alert alert-<?= $actionType ?> alert-dismissible fade show" role="alert">
            <?= $actionMsg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">
            <?= htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']) . ' ' ?>
                <?= (!empty($employee['suffix']) && strtolower($employee['suffix']) !== 'none') 
                    ? ucfirst($employee['suffix']) . '.' 
        : null ?>
            </h2>
            <p class="text-muted mb-0">New Hire Details & Onboarding Progress</p>
        </div>
        <a href="new_hires.php" class="btn btn-outline-secondary">← Back to New Hires</a>
    </div>

    <div class="card mb-3">
        <div class="card-header">Onboarding Progress</div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Overall Completion</span>
                <span class="fs-4 fw-bold"><?= $progress ?>%</span>
            </div>
            <div class="progress" style="height: 15px;">
                <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%">
                    <?= $progress ?>%
                </div>
            </div>
            <div class="row mt-0">
                <div class="col-md-4">
                    <small class="text-muted">Personal Information</small>
                    <p class="mb-0">
                        <?php
                        $piStatus = $onboarding['personal_info_status'] ?? 'Not Submitted';
                        echo match($piStatus) {
                            'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
                            'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
                            'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
                            default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                        };
                        ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Documents</small>
                    <p class="mb-0">
                        <?php
                        $docStatus = $onboarding['documents_status'] ?? 'Not Submitted';
                        echo match($docStatus) {
                            'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
                            'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
                            'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
                            default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                        };
                        ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Orientation</small>
                    <p class="mb-0">
                        <?php if (!empty($onboarding['orientation_completed'])): ?>
                            <span class="badge bg-success">✓ Completed</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Employee Information</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Employee ID:</td>
                            <td><strong><?= $employee['employee_id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username:</td>
                            <td><?= htmlspecialchars($employee['username']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email:</td>
                            <td><?= htmlspecialchars($employee['email']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td><?= htmlspecialchars($employee['phone']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Job Title:</td>
                            <td><?= htmlspecialchars($employee['job_title'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Hired Date:</td>
                            <td><?= date('F d, Y', strtotime($employee['hired_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                    Personal Information
                    <?php if ($piStatus === 'Pending Review'): ?>
                        <div class="d-flex gap-1">
                            <form method="POST">
                                <input type="hidden" name="action" value="approve_personal_info">
                                <button class="btn btn-sm btn-success" onclick="return confirm('Approve personal information for this employee?')"><i class="bi bi-check-lg"></i> Approve</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="action" value="reject_personal_info">
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Reject personal information? The employee will need to re-submit.')"><i class="bi bi-x-lg"></i> Reject</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (in_array($piStatus, ['Pending Review', 'Approved', 'Rejected'])): ?>
                        <table class="table table-sm mb-3">
                            <tr><td class="text-muted">Emergency Contact:</td><td><?= htmlspecialchars($onboarding['emergency_contact']) ?></td></tr>
                            <tr><td class="text-muted">Emergency Phone:</td><td><?= htmlspecialchars($onboarding['emergency_phone']) ?></td></tr>
                            <tr><td class="text-muted">Relationship:</td><td><?= htmlspecialchars($onboarding['emergency_relationship']) ?></td></tr>
                            <tr><td class="text-muted">Address:</td><td><?= htmlspecialchars($onboarding['address'] . ', ' . $onboarding['city'] . ', ' . $onboarding['province']) ?></td></tr>
                            <tr><td class="text-muted">Bank:</td><td><?= htmlspecialchars($onboarding['bank_name']) ?> &mdash; <?= htmlspecialchars($onboarding['bank_account_number']) ?></td></tr>
                        </table>

                        <!-- Government IDs with photos -->
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:8px;">Government IDs</div>
                        <?php
                        $idRows = [
                            'TIN'       => ['number' => $onboarding['tin_number'],         'path' => $onboarding['tin_photo_path'] ?? ''],
                            'SSS'       => ['number' => $onboarding['sss_number'],         'path' => $onboarding['sss_photo_path'] ?? ''],
                            'Pag-IBIG'  => ['number' => $onboarding['pagibig_number'],     'path' => $onboarding['pagibig_photo_path'] ?? ''],
                            'PhilHealth'=> ['number' => $onboarding['philhealth_number'],  'path' => $onboarding['philhealth_photo_path'] ?? ''],
                        ];
                        foreach ($idRows as $label => $val): ?>
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f4f4f5;">
                            <div>
                                <div style="font-size:0.72rem;color:#71717a;"><?= $label ?></div>
                                <div style="font-size:0.88rem;font-weight:500;"><?= htmlspecialchars($val['number'] ?? '—') ?></div>
                            </div>
                            <?php if (!empty($val['path'])): ?>
                                <a href="../public/<?= htmlspecialchars($val['path']) ?>" target="_blank"
                                   style="display:flex;align-items:center;gap:6px;text-decoration:none;">
                                    <img src="../public/<?= htmlspecialchars($val['path']) ?>" style="height:40px;width:64px;object-fit:cover;border-radius:6px;border:1px solid #e4e4e7;">
                                    <span style="font-size:0.72rem;color:#71717a;"><i class="bi bi-eye"></i> View</span>
                                </a>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:0.75rem;"><i class="bi bi-image me-1"></i>No photo</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <!-- Bank photo -->
                        <?php if (!empty($onboarding['bank_photo_path'])): ?>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <div>
                                <div style="font-size:0.72rem;color:#71717a;">Bank Photo</div>
                                <div style="font-size:0.88rem;font-weight:500;"><?= htmlspecialchars($onboarding['bank_name']) ?></div>
                            </div>
                            <a href="../public/<?= htmlspecialchars($onboarding['bank_photo_path']) ?>" target="_blank"
                               style="display:flex;align-items:center;gap:6px;text-decoration:none;">
                                <img src="../public/<?= htmlspecialchars($onboarding['bank_photo_path']) ?>" style="height:40px;width:64px;object-fit:cover;border-radius:6px;border:1px solid #e4e4e7;">
                                <span style="font-size:0.72rem;color:#71717a;"><i class="bi bi-eye"></i> View</span>
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Not yet submitted</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Submitted Documents
                    <?php if ($docStatus === 'Pending Review'): ?>
                        <div class="d-flex gap-1">
                            <form method="POST">
                                <input type="hidden" name="action" value="approve_documents">
                                <button class="btn btn-sm btn-success" onclick="return confirm('Approve submitted documents for this employee?')"><i class="bi bi-check-lg"></i> Approve</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="action" value="reject_documents">
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Reject documents? The employee will need to re-upload.')"><i class="bi bi-x-lg"></i> Reject</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (in_array($docStatus, ['Pending Review', 'Approved', 'Rejected'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php if ($onboarding['government_id_path']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Government ID
                                    <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['government_id_path']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($onboarding['diploma_tor_path']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Diploma/TOR
                                    <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['diploma_tor_path']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($onboarding['nbi_clearance_path']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    NBI Clearance
                                    <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['nbi_clearance_path']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($onboarding['medical_certificate_path']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Medical Certificate
                                    <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['medical_certificate_path']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No documents uploaded yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Orientation Schedule</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Day 1</strong></td>
                            <td>
                                <?php if (!empty($onboarding['orientation_day1_date'])): ?>
                                    <?= date('M d, Y', strtotime($onboarding['orientation_day1_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not scheduled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $s1 = $onboarding['orientation_day1_status'] ?? 'Pending'; ?>
                                <span class="badge bg-<?= $s1 === 'Completed' ? 'success' : ($s1 === 'Missed' ? 'danger' : 'warning text-dark') ?>">
                                    <?= $s1 ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Day 2</strong></td>
                            <td>
                                <?php if (!empty($onboarding['orientation_day2_date'])): ?>
                                    <?= date('M d, Y', strtotime($onboarding['orientation_day2_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not scheduled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $s2 = $onboarding['orientation_day2_status'] ?? 'Pending'; ?>
                                <span class="badge bg-<?= $s2 === 'Completed' ? 'success' : ($s2 === 'Missed' ? 'danger' : 'warning text-dark') ?>">
                                    <?= $s2 ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Day 3</strong></td>
                            <td>
                                <?php if (!empty($onboarding['orientation_day3_date'])): ?>
                                    <?= date('M d, Y', strtotime($onboarding['orientation_day3_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not scheduled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $s3 = $onboarding['orientation_day3_status'] ?? 'Pending'; ?>
                                <span class="badge bg-<?= $s3 === 'Completed' ? 'success' : ($s3 === 'Missed' ? 'danger' : 'warning text-dark') ?>">
                                    <?= $s3 ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    <a href="orientation_schedule.php?id=<?= $employee_id ?>" class="btn btn-sm btn-primary w-100 mt-2">
                        Manage Orientation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
