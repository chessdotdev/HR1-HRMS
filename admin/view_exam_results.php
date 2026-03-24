<?php
session_start();
require_once '../modules/Applicants.php';
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';

$db   = new Database();
$conn = $db->connect();

$interview_id = (int)($_GET['interview_id'] ?? 0);
if (!$interview_id) { header("Location: interviews.php"); exit; }

$stmt = $conn->prepare("
    SELECT i.*, a.firstname, a.lastname, a.job_title, a.email, a.phone, a.gender
    FROM interviews i
    JOIN applicantss a ON a.apply_id = i.applicant_id
    WHERE i.interview_id = :id
");
$stmt->execute([':id' => $interview_id]);
$interview = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$interview) { header("Location: interviews.php"); exit; }

$sess = $conn->prepare("SELECT * FROM exam_sessions WHERE interview_id = :id");
$sess->execute([':id' => $interview_id]);
$session = $sess->fetch(PDO::FETCH_ASSOC);
if (!$session || $session['status'] !== 'Submitted') { header("Location: interviews.php"); exit; }

$astmt = $conn->prepare("
    SELECT ea.*, eq.question_text, eq.question_type,
           eq.option_a, eq.option_b, eq.option_c, eq.option_d,
           eq.correct_answer, eq.points
    FROM exam_answers ea
    JOIN exam_questions eq ON eq.question_id = ea.question_id
    WHERE ea.session_id = :sid
    ORDER BY eq.question_id
");
$astmt->execute([':sid' => $session['session_id']]);
$answers = $astmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['result'])) {
    $result = $_POST['result'];
    if (in_array($result, ['Passed', 'Failed'])) {
        $applicantObj = new Applicants();
        if ($applicantObj->updateInterviewResult($interview_id, $result, $_SESSION['admin_id'], $_SESSION['admin_username'])) {
            $audit = new AuditLog();
            $audit->log($_SESSION['admin_id'], $_SESSION['admin_username'], 'Interview Result', 'Recruitment',
                "Interview ID {$interview_id} marked as {$result} (Exam: {$session['total_score']}/{$session['total_points']})");
            header("Location: interviews.php");
            exit;
        }
        $error = 'Failed to update result.';
    }
}

$mc_ans   = array_filter($answers, fn($a) => $a['question_type'] === 'multiple_choice');
$txt_ans  = array_filter($answers, fn($a) => $a['question_type'] === 'text');
$mc_score = array_sum(array_column($mc_ans, 'points_earned'));
$mc_total = array_sum(array_column($mc_ans, 'points'));
$pct      = $mc_total > 0 ? round(($mc_score / $mc_total) * 100) : 0;

include 'includes/header.php';
?>

<div class="main p-3">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="interviews.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h2 class="mb-0">Exam Results</h2>
            <p class="text-muted mb-0"><?= htmlspecialchars($interview['firstname'] . ' ' . $interview['lastname']) ?> &mdash; <?= htmlspecialchars($interview['job_title']) ?></p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Interviewee + Interviewer Info -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100" style="border:1px solid #e4e4e7;">
                <div class="card-body">
                    <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:8px;">Interviewee</div>
                    <div class="fw-semibold"><?= htmlspecialchars($interview['firstname'] . ' ' . $interview['lastname']) ?></div>
                    <div class="text-muted" style="font-size:0.85rem;"><?= htmlspecialchars($interview['email']) ?></div>
                    <?php if ($interview['phone']): ?>
                    <div class="text-muted" style="font-size:0.85rem;"><?= htmlspecialchars($interview['phone']) ?></div>
                    <?php endif; ?>
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($interview['job_title']) ?></span>
                        <?php if ($interview['gender']): ?>
                        <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($interview['gender']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100" style="border:1px solid #e4e4e7;">
                <div class="card-body">
                    <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#71717a;margin-bottom:8px;">Interviewer</div>
                    <?php if ($interview['interviewer_name']): ?>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:50%;background:#09090b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:600;">
                            <?= strtoupper(substr($interview['interviewer_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($interview['interviewer_name']) ?></div>
                            <div class="text-muted" style="font-size:0.82rem;"><?= date('F d, Y', strtotime($interview['date'])) ?> &bull; <?= date('h:i A', strtotime($interview['time'])) ?></div>
                        </div>
                    </div>
                    <?php else: ?>
                    <span class="text-muted">—</span>
                    <?php endif; ?>
                    <div class="mt-2">
                        <span class="badge bg-secondary" style="font-size:0.72rem;"><?= htmlspecialchars($interview['type']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Summary -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center" style="border:1px solid #e4e4e7;">
                <div class="card-body py-3">
                    <div style="font-size:2rem;font-weight:700;"><?= $pct ?>%</div>
                    <div class="text-muted" style="font-size:0.78rem;">MC Score</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center" style="border:1px solid #e4e4e7;">
                <div class="card-body py-3">
                    <div style="font-size:2rem;font-weight:700;"><?= $mc_score ?>/<?= $mc_total ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">MC Points</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center" style="border:1px solid #e4e4e7;">
                <div class="card-body py-3">
                    <div style="font-size:2rem;font-weight:700;"><?= count($mc_ans) ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">MC Questions</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center" style="border:1px solid #e4e4e7;">
                <div class="card-body py-3">
                    <div style="font-size:2rem;font-weight:700;"><?= count($txt_ans) ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">Essay Questions</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Answers -->
        <div class="col-lg-8">
            <div class="card" style="border:1px solid #e4e4e7;">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-list-check me-2"></i>Answers</div>
                <div class="list-group list-group-flush">
                    <?php foreach ($answers as $i => $a): ?>
                    <div class="list-group-item py-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-muted" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;">
                                Q<?= $i+1 ?><?= $a['question_type'] === 'multiple_choice' ? ' &bull; ' . $a['points'] . ' pt' . ($a['points'] > 1 ? 's' : '') : '' ?>
                            </span>
                            <?php if ($a['question_type'] === 'multiple_choice'): ?>
                                <?php if ($a['is_correct']): ?>
                                    <span class="badge" style="background:#dcfce7;color:#166534;font-size:0.7rem;"><i class="bi bi-check-circle-fill me-1"></i>Correct +<?= $a['points_earned'] ?>pt</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:0.7rem;"><i class="bi bi-x-circle-fill me-1"></i>Wrong</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark" style="font-size:0.7rem;">Essay</span>
                            <?php endif; ?>
                        </div>
                        <div class="fw-medium mb-2" style="font-size:0.9rem;"><?= htmlspecialchars($a['question_text']) ?></div>

                        <?php if ($a['question_type'] === 'multiple_choice'): ?>
                            <div class="row g-1" style="font-size:0.82rem;">
                                <?php foreach (['a','b','c','d'] as $opt): ?>
                                    <?php if ($a["option_{$opt}"]): ?>
                                    <?php
                                        $isCorrect  = $a['correct_answer'] === $opt;
                                        $isSelected = $a['answer_choice'] === $opt;
                                        if ($isCorrect && $isSelected)      $bg = 'background:#dcfce7;border-color:#86efac;';
                                        elseif ($isCorrect)                 $bg = 'background:#dcfce7;border-color:#86efac;';
                                        elseif ($isSelected && !$isCorrect) $bg = 'background:#fee2e2;border-color:#fca5a5;';
                                        else                                $bg = 'background:#f9f9f9;';
                                    ?>
                                    <div class="col-md-6">
                                        <div class="px-2 py-1 rounded border d-flex align-items-center gap-2" style="<?= $bg ?>">
                                            <span class="fw-semibold"><?= strtoupper($opt) ?>.</span>
                                            <?= htmlspecialchars($a["option_{$opt}"]) ?>
                                            <?php if ($isCorrect): ?><i class="bi bi-check-circle-fill text-success ms-auto"></i><?php endif; ?>
                                            <?php if ($isSelected && !$isCorrect): ?><i class="bi bi-x-circle-fill text-danger ms-auto"></i><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php if (!$a['answer_choice']): ?>
                                <div class="text-muted mt-1" style="font-size:0.8rem;"><i class="bi bi-dash-circle me-1"></i>No answer selected</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="p-2 rounded" style="background:#f9f9f9;border:1px solid #e4e4e7;font-size:0.88rem;white-space:pre-wrap;min-height:48px;">
                                <?= $a['answer_text'] ? htmlspecialchars($a['answer_text']) : '<span class="text-muted fst-italic">No answer provided</span>' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Decision Panel -->
        <div class="col-lg-4">
            <div class="card" style="border:1px solid #e4e4e7;position:sticky;top:20px;">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-clipboard-check me-2"></i>Interview Decision</div>
                <div class="card-body">
                    <?php if ($interview['result'] === 'Pending'): ?>
                        <p class="text-muted mb-3" style="font-size:0.85rem;">Review the exam results, then record your final decision.</p>
                        <?php if (!empty($txt_ans)): ?>
                        <div class="alert py-2 mb-3" style="background:#fefce8;border:1px solid #fde047;font-size:0.82rem;">
                            <i class="bi bi-info-circle me-1"></i>This exam has essay questions. Review them manually before deciding.
                        </div>
                        <?php endif; ?>
                        <div class="d-grid gap-2">
                            <form method="POST">
                                <input type="hidden" name="result" value="Passed">
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Mark <?= htmlspecialchars($interview['firstname']) ?> as Passed? This will create an employee account and send login credentials.')">
                                    <i class="bi bi-check-lg me-1"></i>Pass
                                </button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="result" value="Failed">
                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Mark <?= htmlspecialchars($interview['firstname']) ?> as Failed?')">
                                    <i class="bi bi-x-lg me-1"></i>Fail
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <?php if ($interview['result'] === 'Passed'): ?>
                                <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                                <p class="mt-2 mb-0 fw-semibold text-success">Marked as Passed</p>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-danger" style="font-size:2.5rem;"></i>
                                <p class="mt-2 mb-0 fw-semibold text-danger">Marked as Failed</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
