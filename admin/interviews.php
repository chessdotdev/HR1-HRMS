<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT i.*, a.firstname, a.lastname, a.status AS applicant_status
    FROM interviews i
    JOIN applicantss a ON a.apply_id = i.applicant_id
    ORDER BY i.date ASC, i.time ASC
");
$stmt->execute();
$interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="main p-3">
    <h2>Interviews Management</h2>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Candidate</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Applicant Status</th>
                    <th>Result</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($interviews)): $i = 1; ?>
                <?php foreach ($interviews as $int): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($int['firstname'] . ' ' . $int['lastname']) ?></td>
                        <td><?= htmlspecialchars($int['type']) ?></td>
                        <td><?= htmlspecialchars($int['date']) ?></td>
                        <td><?= htmlspecialchars($int['time']) ?></td>
                        <td><?= htmlspecialchars($int['applicant_status']) ?></td>
                        <td>
                            <?php
                                $badge = match($int['result']) {
                                    'Passed' => 'bg-success',
                                    'Failed' => 'bg-danger',
                                    default  => 'bg-warning text-dark',
                                };
                            ?>
                            <span class="badge <?= $badge ?>"><?= htmlspecialchars($int['result']) ?></span>
                        </td>
                        <td>
                            <?php if ($int['result'] === 'Pending'): ?>
                                <form method="POST" action="update_interview_result.php" class="d-flex gap-1">
                                    <input type="hidden" name="interview_id" value="<?= $int['interview_id'] ?>">
                                    <button type="submit" name="result" value="Passed" class="btn btn-sm btn-success"
                                    onclick="return confirm('Set result to Passed for <?= htmlspecialchars($int['firstname']) ?>?')"
                                    >✓ Pass</button>
                                    <button type="submit" name="result" value="Failed" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Set result to Failed for <?= htmlspecialchars($int['firstname']) ?>?')">
                                    ✗ Fail</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Result locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">No interviews scheduled.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>