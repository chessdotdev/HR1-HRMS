<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

$db   = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT i.*, a.firstname, a.lastname, a.job_title, a.email, a.phone, a.gender,
           es.status AS exam_status, es.total_score, es.total_points, es.session_id
    FROM interviews i
    JOIN applicantss a ON a.apply_id = i.applicant_id
    LEFT JOIN exam_sessions es ON es.interview_id = i.interview_id
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
            <h2 class="mb-0">Pending Interviews</h2>
            <p class="text-muted mb-0">Manage scheduled interviews and conduct exams</p>
        </div>
        <span class="badge bg-info text-dark fs-6"><?= count($interviews) ?> Pending</span>
    </div>

    <div class="mb-3">
        <input type="text" id="searchBar" class="form-control" placeholder="Search by name, applicant ID, or job title..." style="max-width:400px;" oninput="filterCards()">
    </div>

    <?php if (!empty($interviews)): ?>
    <div class="d-flex flex-column gap-3">
        <?php $i = 1; foreach ($interviews as $int): ?>
        <?php
            $examStatus = $int['exam_status'] ?? null;
            $hasExam    = !is_null($examStatus);
        ?>
        <div class="card" style="border:1px solid #e4e4e7;" data-search="<?= strtolower($int['applicant_id'] . ' APP-' . $int['applicant_id'] . ' ' . $int['firstname'] . ' ' . $int['lastname'] . ' ' . ($int['job_title'] ?? '')) ?>">
            <div class="card-body p-4">
                <div class="row g-3 align-items-start">

                    <!-- Interviewee Info -->
                    <div class="col-md-4">
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:6px;">Interviewee</div>
                        <div class="text-dark fw-bold" style="font-size:0.72rem;color:#71717a;margin-bottom:2px;">Applicant ID : APP-<?= $int['applicant_id'] ?></div>
                        <div class="fw-semibold" style="font-size:1rem;"><?= htmlspecialchars($int['firstname'] . ' ' . $int['lastname']) ?></div>
                        <div class="text-muted" style="font-size:0.82rem;"><?= htmlspecialchars($int['email']) ?></div>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($int['job_title'] ?? 'N/A') ?></span>
                            <?php if ($int['gender']): ?>
                            <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($int['gender']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Interview Details -->
                    <div class="col-md-3">
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:6px;">Interview Details</div>
                        <div style="font-size:0.88rem;"><i class="bi bi-calendar3 me-1 text-muted"></i><?= date('F d, Y', strtotime($int['date'])) ?></div>
                        <div style="font-size:0.88rem;"><i class="bi bi-clock me-1 text-muted"></i><?= date('h:i A', strtotime($int['time'])) ?></div>
                        <div style="font-size:0.88rem;"><i class="bi bi-camera-video me-1 text-muted"></i><?= htmlspecialchars($int['type']) ?></div>
                    </div>

                    <!-- Interviewer -->
                    <div class="col-md-2">
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:6px;">Interviewer</div>
                        <?php if ($int['interviewer_name']): ?>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:50%;background:#09090b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:600;flex-shrink:0;">
                                <?= strtoupper(substr($int['interviewer_name'], 0, 1)) ?>
                            </div>
                            <span style="font-size:0.88rem;"><?= htmlspecialchars($int['interviewer_name']) ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:0.82rem;">—</span>
                        <?php endif; ?>
                    </div>

                    <!-- Exam Status + Actions -->
                    <div class="col-md-3">
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:6px;">Exam & Decision</div>

                        <?php if (!$hasExam): ?>
                            <!-- No exam started yet -->
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i>No Exam Yet</span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <a href="../public/interview_exam.php?interview_id=<?= $int['interview_id'] ?>" target="_blank"
                                   class="btn btn-sm btn-dark">
                                    <i class="bi bi-pencil-square me-1"></i>Start Exam
                                </a>
                                <div class="d-flex gap-1">
                                    <!-- <form method="POST" action="update_interview_result.php" class="flex-fill">
                                        <input type="hidden" name="interview_id" value="<?= $int['interview_id'] ?>">
                                        <button type="submit" name="result" value="Passed" class="btn btn-sm btn-success w-100"
                                            onclick="return confirm('Mark <?= htmlspecialchars($int['firstname']) ?> as Passed? This will create an employee account and send login credentials.')">
                                            <i class="bi bi-check-lg"></i> Pass
                                        </button>
                                    </form>
                                    <form method="POST" action="update_interview_result.php" class="flex-fill">
                                        <input type="hidden" name="interview_id" value="<?= $int['interview_id'] ?>">
                                        <button type="submit" name="result" value="Failed" class="btn btn-sm btn-danger w-100"
                                            onclick="return confirm('Mark <?= htmlspecialchars($int['firstname']) ?> as Failed?')">
                                            <i class="bi bi-x-lg"></i> Fail
                                        </button>
                                    </form> -->
                                </div>
                            </div>

                        <?php elseif ($examStatus === 'In Progress'): ?>
                            <div class="mb-2">
                                <span class="badge" style="background:#fef9c3;color:#854d0e;font-size:0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Exam In Progress</span>
                            </div>
                            <a href="../public/interview_exam.php?interview_id=<?= $int['interview_id'] ?>" target="_blank"
                               class="btn btn-sm btn-warning w-100">
                                <i class="bi bi-arrow-right-circle me-1"></i>Continue Exam
                            </a>

                        <?php elseif ($examStatus === 'Submitted'): ?>
                            <div class="mb-2">
                                <span class="badge" style="background:#dcfce7;color:#166534;font-size:0.72rem;"><i class="bi bi-check-circle me-1"></i>Exam Submitted</span>
                                <?php if ($int['total_points'] > 0): ?>
                                <span class="badge bg-light text-dark border ms-1" style="font-size:0.72rem;">
                                    <?= $int['total_score'] ?>/<?= $int['total_points'] ?> pts
                                    (<?= round(($int['total_score'] / $int['total_points']) * 100) ?>%)
                                </span>
                                <?php endif; ?>
                            </div>
                            <a href="view_exam_results.php?interview_id=<?= $int['interview_id'] ?>"
                               class="btn btn-sm btn-dark w-100">
                                <i class="bi bi-clipboard-data me-1"></i>View Results & Decide
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <div class="card" style="border:1px solid #e4e4e7;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar-check" style="font-size:2.5rem;color:#d4d4d8;"></i>
            <p class="mt-3 mb-0">No pending interviews</p>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function filterCards() {
    const q = document.getElementById('searchBar').value.toLowerCase();
    document.querySelectorAll('[data-search]').forEach(card => {
        card.closest('.card') ? card.style.display = card.dataset.search.includes(q) ? '' : 'none' : null;
    });
}
</script>

<?php include 'includes/footer.php'; ?>
