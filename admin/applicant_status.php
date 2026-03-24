<?php
require_once '../config/Database.php';

$db   = new Database();
$conn = $db->connect();

$status = $_GET['status'] ?? null;

$sql = "
    SELECT a.*, i.interview_id, i.date AS interview_date, i.time AS interview_time,
           i.type AS interview_type, i.result AS interview_result,
           i.interviewer_name,
           es.total_score, es.total_points, es.status AS exam_status
    FROM applicantss a
    LEFT JOIN interviews i ON i.applicant_id = a.apply_id
    LEFT JOIN exam_sessions es ON es.interview_id = i.interview_id
";
$params = [];
if ($status) {
    $sql .= " WHERE a.status = :status";
    $params[':status'] = $status;
}
$sql .= " ORDER BY a.applied_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<!-- content -->
<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Hiring Status</h2>
            <p class="text-muted mb-0">Track applicant progress and exam results</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="mb-3">
        <form method="GET" action="applicant_status.php" class="d-flex align-items-center gap-3">
            <label class="form-label fw-semibold mb-0">Filter by Status:</label>
            <select name="status" class="form-select" style="max-width:200px;" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Pending"   <?= $status === 'Pending'   ? 'selected' : '' ?>>Pending</option>
                <option value="Interview" <?= $status === 'Interview' ? 'selected' : '' ?>>Interview</option>
                <option value="Hired"     <?= $status === 'Hired'     ? 'selected' : '' ?>>Hired</option>
                <option value="Rejected"  <?= $status === 'Rejected'  ? 'selected' : '' ?>>Rejected</option>
            </select>
            <?php if ($status): ?>
                <a href="applicant_status.php" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card" style="border:1px solid #e4e4e7;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Applicant</th>
                        <th>Position</th>
                        <th>Interviewer</th>
                        <th>Interview Date</th>
                        <th>Exam Score</th>
                        <th>Exam Result</th>
                        <th>Answers</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($applicants)): $i = 1; ?>
                    <?php foreach ($applicants as $app): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($app['firstname'] . ' ' . $app['lastname']) ?></div>
                            <div class="text-muted" style="font-size:0.8rem;"><?= htmlspecialchars($app['email']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($app['job_title']) ?></td>
                        <td>
                            <?php if ($app['interviewer_name']): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:26px;height:26px;border-radius:50%;background:#09090b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:600;flex-shrink:0;">
                                        <?= strtoupper(substr($app['interviewer_name'], 0, 1)) ?>
                                    </div>
                                    <span style="font-size:0.85rem;"><?= htmlspecialchars($app['interviewer_name']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['interview_date']): ?>
                                <div style="font-size:0.85rem;"><?= date('M d, Y', strtotime($app['interview_date'])) ?></div>
                                <div class="text-muted" style="font-size:0.78rem;"><?= date('h:i A', strtotime($app['interview_time'])) ?></div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['exam_status'] === 'Submitted'): ?>
                                <?php
                                    $score = (int)$app['total_score'];
                                    $total = (int)$app['total_points'];
                                    $pct   = $total > 0 ? round(($score / $total) * 100) : 0;
                                ?>
                                <div class="fw-semibold"><?= $score ?>/<?= $total ?> pts</div>
                                <div class="text-muted" style="font-size:0.78rem;"><?= $pct ?>%</div>
                            <?php elseif ($app['exam_status'] === 'In Progress'): ?>
                                <span class="badge" style="background:#fef9c3;color:#854d0e;font-size:0.7rem;">In Progress</span>
                            <?php elseif ($app['exam_status'] === 'Not Started'): ?>
                                <span class="text-muted" style="font-size:0.82rem;">Not Started</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['interview_result'] === 'Passed'): ?>
                                <span class="badge" style="background:#dcfce7;color:#166534;font-size:0.72rem;"><i class="bi bi-check-circle-fill me-1"></i>Passed</span>
                            <?php elseif ($app['interview_result'] === 'Failed'): ?>
                                <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:0.72rem;"><i class="bi bi-x-circle-fill me-1"></i>Failed</span>
                            <?php elseif ($app['exam_status'] === 'Submitted'): ?>
                                <a href="view_exam_results.php?interview_id=<?= $app['interview_id'] ?>" class="btn btn-sm btn-dark" style="font-size:0.75rem;">View & Decide</a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['exam_status'] === 'Submitted'): ?>
                                <a href="view_exam_results.php?interview_id=<?= $app['interview_id'] ?>" class="btn btn-sm btn-outline-dark" style="font-size:0.75rem;">
                                    <i class="bi bi-clipboard-data me-1"></i>View Answers
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge
                                <?= $app['status'] === 'Pending'   ? 'bg-warning text-dark' : '' ?>
                                <?= $app['status'] === 'Interview' ? 'bg-info text-dark'    : '' ?>
                                <?= $app['status'] === 'Hired'     ? 'bg-success'           : '' ?>
                                <?= $app['status'] === 'Rejected'  ? 'bg-danger'            : '' ?>
                            "><?= $app['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No applicants found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

