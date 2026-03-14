<?php
require_once '../modules/Employee.php';

$employeeObj = new Employee();

// Get all new hires
$query = "SELECT e.*, a.firstname, a.lastname, a.email, a.phone, a.job_title,
          eo.onboarding_status, eo.personal_info_status, eo.documents_status, eo.orientation_completed
          FROM employees e
          LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
          LEFT JOIN employee_onboarding eo ON e.employee_id = eo.employee_id
          WHERE e.employment_status = 'New Hire'
          ORDER BY e.hired_at DESC";

$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare($query);
$stmt->execute();
$newHires = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">New Hires</h2>
            <p class="text-muted mb-0">Monitor onboarding progress for newly hired employees</p>
        </div>
        <span class="badge bg-info fs-6"><?= count($newHires) ?> New Hires</span>
    </div>

    <?php if (!empty($newHires)): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Job Title</th>
                                <th>Hired Date</th>
                                <th>Personal Info</th>
                                <th>Documents</th>
                                <th>Orientation</th>
                                <th>Overall Status</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($newHires as $hire): ?>
                                <?php
                                // Calculate progress
                                $completed = 0;
                                if (($hire['personal_info_status'] ?? '') === 'Approved') $completed++;
                                if (($hire['documents_status'] ?? '') === 'Approved') $completed++;
                                if ($hire['orientation_completed']) $completed++;
                                $progress = round(($completed / 3) * 100);
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($hire['firstname'] . ' ' . $hire['lastname']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($hire['email']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($hire['job_title'] ?? 'N/A') ?></td>
                                    <td><?= date('M d, Y', strtotime($hire['hired_at'])) ?></td>
                                    <td>
                                        <?php
                                        $piStatus = $hire['personal_info_status'] ?? 'Not Submitted';
                                        echo match($piStatus) {
                                            'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
                                            'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
                                            'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
                                            default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                                        };
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $docStatus = $hire['documents_status'] ?? 'Not Submitted';
                                        echo match($docStatus) {
                                            'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
                                            'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
                                            'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
                                            default          => '<span class="badge bg-warning text-dark">Not Submitted</span>',
                                        };
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($hire['orientation_completed']): ?>
                                            <span class="badge bg-success">✓ Done</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $hire['onboarding_status'] === 'Completed' ? 'success' : ($hire['onboarding_status'] === 'In Progress' ? 'info' : 'secondary') ?>">
                                            <?= htmlspecialchars($hire['onboarding_status'] ?? 'Not Started') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; min-width: 80px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%">
                                                <?= $progress ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="view_new_hire.php?id=<?= $hire['employee_id'] ?>" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-person-check" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No new hires at the moment</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
