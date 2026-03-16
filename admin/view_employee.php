<?php
require_once '../modules/Employee.php';

$employeeObj = new Employee();
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$employee_id) {
    header("Location: employee_list.php");
    exit();
}

$employee = $employeeObj->getEmployeeById($employee_id);
$onboarding = $employeeObj->getOnboardingData($employee_id);

if (!$employee) {
    header("Location: employee_list.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0"><?= htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']) ?></h2>
            <p class="text-muted mb-0">Employee Details</p>
        </div>
        <a href="employee_list.php" class="btn btn-outline-secondary">← Back to Employee List</a>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Employee Information</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Employee ID:</td>
                            <td><strong><?= $employee['employee_id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username:</td>
                            <td><?= htmlspecialchars($employee['username'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Full Name:</td>
                            <td><?= htmlspecialchars(($employee['firstname'] ?? '') . ' ' . ($employee['lastname'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email:</td>
                            <td><?= htmlspecialchars($employee['email'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td><?= htmlspecialchars($employee['phone'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Job Title:</td>
                            <td><?= htmlspecialchars($employee['job_title'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Employment Status:</td>
                            <td>
                                <span class="badge bg-<?= $employee['employment_status'] === 'Active' ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($employee['employment_status'] ?? '') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Hired Date:</td>
                            <td><?= !empty($employee['hired_at']) ? date('F d, Y', strtotime($employee['hired_at'])) : '—' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <?php if ($onboarding && $onboarding['personal_info_completed']): ?>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Emergency Contact:</td>
                                <td><?= htmlspecialchars($onboarding['emergency_contact'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Emergency Phone:</td>
                                <td><?= htmlspecialchars($onboarding['emergency_phone'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Relationship:</td>
                                <td><?= htmlspecialchars($onboarding['emergency_relationship'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Address:</td>
                                <td><?= htmlspecialchars($onboarding['address'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">City:</td>
                                <td><?= htmlspecialchars($onboarding['city'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Province:</td>
                                <td><?= htmlspecialchars($onboarding['province'] ?? '') ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Personal information not available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Government IDs</div>
                <div class="card-body">
                    <?php if ($onboarding && $onboarding['personal_info_completed']): ?>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" style="width: 40%;">TIN:</td>
                                <td><?= htmlspecialchars($onboarding['tin_number'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">SSS:</td>
                                <td><?= htmlspecialchars($onboarding['sss_number'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pag-IBIG:</td>
                                <td><?= htmlspecialchars($onboarding['pagibig_number'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">PhilHealth:</td>
                                <td><?= htmlspecialchars($onboarding['philhealth_number'] ?? '') ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Government IDs not available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Bank Details</div>
                <div class="card-body">
                    <?php if ($onboarding && $onboarding['personal_info_completed']): ?>
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Bank Name:</td>
                                <td><?= htmlspecialchars($onboarding['bank_name'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Account Number:</td>
                                <td><?= htmlspecialchars($onboarding['bank_account_number'] ?? '') ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Bank details not available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Submitted Documents</div>
                <div class="card-body">
                    <?php if ($onboarding && $onboarding['documents_submitted']): ?>
                        <div class="row">
                            <?php if ($onboarding['government_id_path']): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                                            <p class="mb-2 mt-2"><small>Government ID</small></p>
                                            <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['government_id_path']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($onboarding['diploma_tor_path']): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                                            <p class="mb-2 mt-2"><small>Diploma/TOR</small></p>
                                            <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['diploma_tor_path']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($onboarding['nbi_clearance_path']): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                                            <p class="mb-2 mt-2"><small>NBI Clearance</small></p>
                                            <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['nbi_clearance_path']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($onboarding['medical_certificate_path']): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                                            <p class="mb-2 mt-2"><small>Medical Certificate</small></p>
                                            <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['medical_certificate_path']) ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No documents submitted</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
