<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

$db   = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT i.*, a.firstname, a.lastname, a.job_title, a.email
    FROM interviews i
    JOIN applicantss a ON a.apply_id = i.applicant_id
    WHERE i.result = 'Pending'
    ORDER BY i.date ASC, i.time ASC
");
$stmt->execute();
$interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="main p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-3">Pending Interviews</h2>
        </div>
        <span class="badge bg-info text-dark fs-6"><?= count($interviews) ?> Pending</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Candidate</th>
                            <th>Job Title</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($interviews)): $i = 1; ?>
                        <?php foreach ($interviews as $int): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($int['firstname'] . ' ' . $int['lastname']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($int['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($int['job_title'] ?? 'N/A') ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($int['type']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($int['date'])) ?></td>
                            <td><?= date('h:i A', strtotime($int['time'])) ?></td>
                            <td>
                                <form method="POST" action="update_interview_result.php" class="d-flex gap-1">
                                    <input type="hidden" name="interview_id" value="<?= $int['interview_id'] ?>">
                                    <button type="submit" name="result" value="Passed" class="btn btn-sm btn-success"
                                        onclick="return confirm('Mark <?= htmlspecialchars($int['firstname']) ?> as Passed? This will create an employee account and send login credentials.')">
                                        <i class="bi bi-check-lg"></i> Pass
                                    </button>
                                    <button type="submit" name="result" value="Failed" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Mark <?= htmlspecialchars($int['firstname']) ?> as Failed?')">
                                        <i class="bi bi-x-lg"></i> Fail
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-calendar-check" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No pending interviews</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
