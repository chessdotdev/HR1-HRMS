<?php
session_start();
require_once '../modules/Performance.php';
require_once '../modules/AuditLog.php';
require_once '../config/Database.php';
require_once 'includes/verify_admin.php';
$perf = new Performance();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];

// Load probation_days from system_settings
$_db = new Database();
$_conn = $_db->connect();
$_s = $_conn->query("SELECT `key`, `value` FROM system_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$probationDays = (int)($_s['probation_days'] ?? 90);

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $employee_id = (int)($_POST['employee_id'] ?? 0);
        $start       = $_POST['probation_start'] ?? '';
        $end         = $_POST['probation_end'] ?? '';

        if (!$employee_id || !$start || !$end) {
            $error = 'All fields are required.';
        } elseif ($perf->getReviewByEmployee($employee_id)) {
            $error = 'This employee already has a probation review.';
        } else {
            $perf->createReview($employee_id, $start, $end);
            $audit->log($admin_id, $admin_name, 'Create Review', 'Performance', "Created probation review for employee ID {$employee_id} ({$start} to {$end})");
            $success = 'Probation review created.';
        }   
    }
}

$reviews   = $perf->getAllReviews();
$employees = $perf->getActiveEmployees();

// IDs that already have a review
$reviewedIds = array_column($reviews, 'employee_id');
$withoutReview = array_filter($employees, fn($e) => !in_array($e['employee_id'], $reviewedIds));

$statusColors = ['Ongoing' => 'warning', 'Passed' => 'success', 'Failed' => 'danger', 'Extended' => 'info'];
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Evaluation Forms</h2>
            <p class="text-muted mb-0">Manage probation period performance reviews</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($withoutReview)): ?>
                <span class="badge bg-warning text-dark" style="font-size:0.8rem;">
                    <i class="bi bi-clock me-1"></i><?= count($withoutReview) ?> Pending evaluation
                </span>
            <?php endif; ?>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i> New Review
            </button>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Total Reviews</small>
            <h3 class="mb-0"><?= count($reviews) ?></h3>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Ongoing</small>
            <h3 class="mb-0"><?= count(array_filter($reviews, fn($r) => $r['status'] === 'Ongoing')) ?></h3>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Passed</small>
            <h3 class="mb-0 text-success"><?= count(array_filter($reviews, fn($r) => $r['status'] === 'Passed')) ?></h3>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Failed / Extended</small>
            <h3 class="mb-0 text-danger"><?= count(array_filter($reviews, fn($r) => in_array($r['status'], ['Failed','Extended']))) ?></h3>
        </div></div></div>
    </div>

    <?php if (!empty($reviews)): ?>
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span>All Probation Reviews</span>
            <div class="input-group" style="max-width:260px;">
                <input type="text" class="form-control form-control-sm" id="searchReview" placeholder="Search...">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reviewTable">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Probation Period</th>
                            <th>Goals</th>
                            <th>Feedback</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $r): ?>
                        <?php
                            $daysLeft = (int)ceil((strtotime($r['probation_end']) - time()) / 86400);
                            $achieved = $r['goal_count'] > 0 ? round(($r['achieved_count'] / $r['goal_count']) * 100) : 0;
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars(($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? '')) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($r['job_title'] ?? '') ?></small>
                            </td>
                            <td>
                                <small><?= date('M d, Y', strtotime($r['probation_start'])) ?> – <?= date('M d, Y', strtotime($r['probation_end'])) ?></small>
                                <?php if ($r['status'] === 'Ongoing'): ?>
                                    <br><small class="text-<?= $daysLeft <= 7 ? 'danger' : 'muted' ?>">
                                        <?= $daysLeft > 0 ? "{$daysLeft} days left" : 'Overdue' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['goal_count'] > 0): ?>
                                    <div class="progress mb-1" style="height:5px;width:80px;">
                                        <div class="progress-bar bg-success" style="width:<?= $achieved ?>%"></div>
                                    </div>
                                    <small><?= $r['achieved_count'] ?>/<?= $r['goal_count'] ?> achieved</small>
                                <?php else: ?>
                                    <small class="text-muted">No goals</small>
                                <?php endif; ?>
                            </td>
                            <td><small><?= $r['feedback_count'] ?> entries</small></td>
                            <td><span class="badge bg-<?= $statusColors[$r['status']] ?? 'secondary' ?>"><?= $r['status'] ?></span></td>
                            <td>
                                <a href="evaluation_results.php?review_id=<?= $r['review_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Manage
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
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-clipboard-data" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3">No probation reviews yet.</p>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createModal">Create First Review</button>
    </div></div>
    <?php endif; ?>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-clipboard-plus me-2"></i>New Probation Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select employee...</option>
                            <?php foreach ($withoutReview as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>">
                                    <?= htmlspecialchars(($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? '')) ?>
                                    <?php if ($emp['job_title']): ?> — <?= htmlspecialchars($emp['job_title']) ?><?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if (empty($withoutReview)): ?>
                                <option disabled>All employees already have a review</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="probation_start" id="probation_start" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="probation_end" id="probation_end" class="form-control" required>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Default probation period: <?= $probationDays ?> days. End date auto-filled.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Create Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const probationDays = <?= $probationDays ?>;

// Auto-fill dates when modal opens
document.getElementById('createModal').addEventListener('show.bs.modal', function() {
    const today = new Date();
    const end   = new Date();
    end.setDate(today.getDate() + probationDays);
    const fmt = d => d.toISOString().split('T')[0];
    document.getElementById('probation_start').value = fmt(today);
    document.getElementById('probation_end').value   = fmt(end);
});

// Recalculate end date when start date changes
document.getElementById('probation_start').addEventListener('change', function() {
    const start = new Date(this.value);
    start.setDate(start.getDate() + probationDays);
    document.getElementById('probation_end').value = start.toISOString().split('T')[0];
});

document.getElementById('searchReview')?.addEventListener('keyup', function() {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#reviewTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
