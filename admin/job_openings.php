<?php
require_once '../modules/Recruitment.php';

$current_page = basename($_SERVER['PHP_SELF']);

$recruitment = new Recruitment();
$message = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- CREATE NEW JOB ---
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'create_job') {
        $title = trim($_POST['title'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $qualifications = trim($_POST['qualifications'] ?? '');
        
        if (!$title || !$department  || !$role || !$qualifications) {
            $message = "All fields are required.";  
        } else {
            $result = $recruitment->createJob($title, $department, $role, $qualifications);
            $message = $result ? "Job posted successfully!" : "Failed to create job.";
        }
    }

    // --- UPDATE JOB STATUS ---
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'update_status') {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $new_status = $_POST['job_status'] ?? '';

        if ($job_id && $new_status) {
            $recruitment->updateJobsStatus($job_id, $new_status);
            header("Location: job_openings.php");
            exit();
        }
    }
}

$stmt = $recruitment->getOpenJobs();
$alljobs = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt1 = $recruitment->getCloseJobs();
$closedJobs = $stmt1->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="row">

        <!-- Post New Job -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Post New Job Opening</div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="form_type" value="create_job">
                        <div class="mb-3">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="title" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <textarea name="role" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualifications</label>
                            <textarea name="qualifications" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Job</button>
                    </form>
                </div>
            </div>
        </div>

       <div class="col-md-8">
    <div class="card mb-4">
        <div class="card-header">Active Job Openings</div>
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alljobs as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['department']) ?></td>
                        <td><?= date('Y-m-d', strtotime($job['created_at'])) ?></td>
                        <td>
                            <span class="badge bg-success"><?= htmlspecialchars($job['status']) ?></span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="form_type" value="update_status">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                <select name="job_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="open" selected>Open</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Closed Jobs Below -->
    <div class="card mt-5">
        <div class="card-header">Closed Jobs</div>
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($closedJobs as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['department']) ?></td>
                        <td><?= date('Y-m-d', strtotime($job['created_at'])) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($job['status']) ?></span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="form_type" value="update_status">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                <select name="job_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="open">Open</option>
                                    <option value="closed" selected>Closed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
