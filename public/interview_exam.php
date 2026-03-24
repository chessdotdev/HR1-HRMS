<?php
require_once '../config/Database.php';

$db   = new Database();
$conn = $db->connect();

$interview_id = (int)($_GET['interview_id'] ?? 0);
if (!$interview_id) die('Invalid exam link.');

// Get interview + applicant
$stmt = $conn->prepare("
    SELECT i.*, a.firstname, a.lastname, a.job_title
    FROM interviews i
    JOIN applicantss a ON a.apply_id = i.applicant_id
    WHERE i.interview_id = :id
");
$stmt->execute([':id' => $interview_id]);
$interview = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$interview) die('Interview not found.');

// Get or create exam session
$sess = $conn->prepare("SELECT * FROM exam_sessions WHERE interview_id = :id");
$sess->execute([':id' => $interview_id]);
$session = $sess->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    $conn->prepare("INSERT INTO exam_sessions (interview_id, applicant_id, status) VALUES (:iid, :aid, 'Not Started')")
         ->execute([':iid' => $interview_id, ':aid' => $interview['applicant_id']]);
    $session_id = $conn->lastInsertId();
    $session    = ['session_id' => $session_id, 'status' => 'Not Started'];
} else {
    $session_id = $session['session_id'];
}

// Already submitted — show thank you
if ($session['status'] === 'Submitted') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Exam Submitted</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>body { font-family:'Geist',sans-serif; background:#f4f4f5; }</style>
    </head>
    <body>
        <div style="max-width:480px;margin:100px auto;text-align:center;padding:0 16px;">
            <div style="font-size:3rem;">Done</div>
            <h4 class="mt-3 mb-2">Exam Submitted</h4>
            <p class="text-muted">Your answers have been recorded. Please return the laptop to HR.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load questions: job-specific + general (NULL)
$qstmt = $conn->prepare("
    SELECT * FROM exam_questions
    WHERE job_title = :jt OR job_title IS NULL
    ORDER BY question_id
");
$qstmt->execute([':jt' => $interview['job_title']]);
$questions = $qstmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($questions)) {
    die('No exam questions available for this position. Please inform HR.');
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    $total_points = 0;
    $total_score  = 0;

    $conn->prepare("UPDATE exam_sessions SET started_at = COALESCE(started_at, NOW()), submitted_at = NOW(), status = 'Submitted' WHERE session_id = :sid")
         ->execute([':sid' => $session_id]);

    foreach ($questions as $q) {
        $qid  = $q['question_id'];
        $pts  = (int)$q['points'];
        $total_points += $pts;

        if ($q['question_type'] === 'multiple_choice') {
            $choice     = $_POST["q_{$qid}"] ?? null;
            $is_correct = ($choice && $choice === $q['correct_answer']) ? 1 : 0;
            $earned     = $is_correct ? $pts : 0;
            $total_score += $earned;
            $conn->prepare("INSERT INTO exam_answers (session_id, question_id, answer_choice, is_correct, points_earned) VALUES (:sid,:qid,:ch,:ic,:pe)")
                 ->execute([':sid' => $session_id, ':qid' => $qid, ':ch' => $choice, ':ic' => $is_correct, ':pe' => $earned]);
        } else {
            $text = trim($_POST["q_{$qid}"] ?? '');
            $conn->prepare("INSERT INTO exam_answers (session_id, question_id, answer_text, is_correct, points_earned) VALUES (:sid,:qid,:txt,NULL,0)")
                 ->execute([':sid' => $session_id, ':qid' => $qid, ':txt' => $text]);
        }
    }

    $conn->prepare("UPDATE exam_sessions SET total_score = :ts, total_points = :tp WHERE session_id = :sid")
         ->execute([':ts' => $total_score, ':tp' => $total_points, ':sid' => $session_id]);

    header("Location: interview_exam.php?interview_id={$interview_id}");
    exit;
}

// Mark In Progress on first open
if ($session['status'] === 'Not Started') {
    $conn->prepare("UPDATE exam_sessions SET status = 'In Progress', started_at = NOW() WHERE session_id = :sid")
         ->execute([':sid' => $session_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interview Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Geist', sans-serif; background: #f4f4f5; color: #09090b; padding-bottom: 80px; }
        .exam-wrap { max-width: 720px; margin: 0 auto; padding: 32px 16px; }
        .exam-header { background: #09090b; color: #fff; border-radius: 12px; padding: 28px 32px; margin-bottom: 28px; }
        .q-card { background: #fff; border: 1px solid #e4e4e7; border-radius: 10px; padding: 24px; margin-bottom: 14px; }
        .q-num { font-size: 0.72rem; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
        .q-text { font-size: 0.95rem; font-weight: 500; margin-bottom: 18px; line-height: 1.5; }
        .opt-wrap { margin-bottom: 8px; }
        .opt-wrap input[type=radio] { display: none; }
        .opt-label { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1px solid #e4e4e7; border-radius: 8px; cursor: pointer; transition: border-color 0.15s, background 0.15s; user-select: none; }
        .opt-label:hover { border-color: #a1a1aa; background: #fafafa; }
        .opt-wrap input[type=radio]:checked ~ .opt-label { border-color: #09090b; background: #f4f4f5; font-weight: 500; }
        .opt-key { width: 28px; height: 28px; border-radius: 50%; background: #f4f4f5; border: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 700; flex-shrink: 0; }
        .submit-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e4e4e7; padding: 14px 24px; display: flex; justify-content: center; z-index: 100; }
    </style>
</head>
<body>
<div class="exam-wrap">

    <div class="exam-header">
        <div style="font-size:0.75rem;opacity:0.5;margin-bottom:6px;letter-spacing:0.08em;text-transform:uppercase;">Interview Exam</div>
        <h4 class="mb-1 fw-semibold"><?= htmlspecialchars($interview['firstname'] . ' ' . $interview['lastname']) ?></h4>
        <div style="font-size:0.88rem;opacity:0.7;"><?= htmlspecialchars($interview['job_title']) ?></div>
        <div class="mt-3 d-flex gap-3" style="font-size:0.8rem;opacity:0.6;">
            <span><i class="bi bi-question-circle me-1"></i><?= count($questions) ?> Question<?= count($questions) !== 1 ? 's' : '' ?></span>
            <span><i class="bi bi-clock me-1"></i><?= date('F d, Y') ?></span>
        </div>
    </div>

    <form method="POST" id="examForm">
        <?php foreach ($questions as $i => $q): ?>
        <div class="q-card">
            <div class="q-num">Question <?= $i + 1 ?> &bull; <?= $q['points'] ?> pt<?= $q['points'] > 1 ? 's' : '' ?></div>
            <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>

            <?php if ($q['question_type'] === 'multiple_choice'): ?>
                <?php foreach (['a','b','c','d'] as $opt): ?>
                    <?php if ($q["option_{$opt}"]): ?>
                    <div class="opt-wrap">
                        <input type="radio" name="q_<?= $q['question_id'] ?>" id="q<?= $q['question_id'] ?>_<?= $opt ?>" value="<?= $opt ?>">
                        <label for="q<?= $q['question_id'] ?>_<?= $opt ?>" class="opt-label">
                            <span class="opt-key"><?= strtoupper($opt) ?></span>
                            <span><?= htmlspecialchars($q["option_{$opt}"]) ?></span>
                        </label>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <textarea name="q_<?= $q['question_id'] ?>" class="form-control" rows="4"
                    placeholder="Write your answer here..." style="font-size:0.9rem;"></textarea>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <input type="hidden" name="submit_exam" value="1">
    </form>
</div>

<div class="submit-bar">
    <button type="button" class="btn btn-dark px-5" onclick="submitExam()">
        <i class="bi bi-send me-2"></i>Submit Exam
    </button>
</div>

<script>
function submitExam() {
    if (confirm('Are you sure you want to submit? You cannot change your answers after submitting.')) {
        document.getElementById('examForm').submit();
    }
}
</script>
</body>
</html>
