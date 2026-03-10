<?php
require_once '../modules/Applicants.php';

$applicantObj = new Applicants();

// Get status from URL filter
$status = $_GET['status'] ?? null;
$applicants = $applicantObj->getApplicants($status);
?>

<?php include 'includes/header.php'; ?>

<!-- content -->

<div class="main p-3">
    <h2>Applicant Status</h2>

    <!-- Status Filter -->
    <div class="mb-3">
        <form method="GET" action="applicant_status.php" class="d-flex align-items-center gap-3">
            <label for="statusFilter" class="form-label fw-semibold mb-0">
                Filter by Status:
            </label>
            <select name="status" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Interview" <?= ($status == 'Interview') ? 'selected' : '' ?>>Interview</option>
                <option value="Hired" <?= ($status == 'Hired') ? 'selected' : '' ?>>Hired</option>
                <option value="Rejected" <?= ($status == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
            <?php if($status): ?>
                <a href="applicant_status.php" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Applicants Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Applied at</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($applicants)): ?>
                <?php $i=1; foreach($applicants as $app): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($app['firstname'].' '.$app['lastname']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>
                    <td><?= htmlspecialchars($app['phone']) ?></td>
                    <td><?= htmlspecialchars($app['applied_at']) ?></td>
                    <td>
                        <span class="badge 
                            <?= $app['status']=='Pending'?'bg-warning text-dark':'' ?>
                            <?= $app['status']=='Interview'?'bg-info':'' ?>
                            <?= $app['status']=='Hired'?'bg-success':'' ?>
                            <?= $app['status']=='Rejected'?'bg-danger':'' ?>
                        "><?= $app['status'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No applicants found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

