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
    <title>Interview Exam &mdash; <?= htmlspecialchars($interview['job_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Geist', sans-serif; background: #f0f0f2; color: #09090b; padding-bottom: 100px; }

        /* Nav */
        .exam-nav { background: #fff; border-bottom: 1px solid #e4e4e7; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .exam-nav-brand { font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; }
        .exam-nav-brand .dot { width: 9px; height: 9px; border-radius: 50%; background: #22c55e; display: inline-block; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
        .exam-badge { background: #f4f4f5; border: 1px solid #e4e4e7; border-radius: 20px; padding: 4px 14px; font-size: 0.78rem; color: #71717a; }

        /* Progress */
        .exam-progress-bar { background: #e4e4e7; height: 5px; }
        .exam-progress-fill { height: 5px; background: linear-gradient(90deg, #22c55e, #16a34a); transition: width 0.4s ease; width: 0%; }

        /* Hero */
        .exam-hero { background: linear-gradient(135deg, #09090b 0%, #27272a 100%); color: #fff; padding: 48px 24px 44px; text-align: center; }
        .exam-hero-label { font-size: 0.68rem; letter-spacing: 0.2em; text-transform: uppercase; opacity: 0.4; margin-bottom: 14px; }
        .exam-hero h1 { font-size: 2rem; font-weight: 600; margin-bottom: 6px; }
        .exam-hero-sub { font-size: 0.9rem; opacity: 0.55; margin-bottom: 28px; }
        .exam-stats { display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; }
        .exam-stat { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 14px 24px; min-width: 100px; }
        .exam-stat-val { font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .exam-stat-label { font-size: 0.68rem; opacity: 0.45; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.08em; }

        /* Content */
        .exam-wrap { max-width: 760px; margin: 0 auto; padding: 32px 20px; }

        /* Question cards */
        .q-card { background: #fff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 28px 32px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); transition: box-shadow 0.2s, border-color 0.2s; }
        .q-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .q-card.answered { border-left: 4px solid #22c55e; }
        .q-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .q-num-badge { background: #09090b; color: #fff; border-radius: 8px; padding: 4px 12px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em; }
        .q-pts { font-size: 0.72rem; color: #71717a; background: #fef9c3; border: 1px solid #fde047; border-radius: 20px; padding: 3px 12px; font-weight: 500; }
        .q-essay-badge { font-size: 0.72rem; color: #6366f1; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 20px; padding: 3px 12px; font-weight: 500; }
        .q-text { font-size: 1rem; font-weight: 500; line-height: 1.65; margin-bottom: 20px; color: #09090b; }

        /* Options */
        .opts-grid { display: flex; flex-direction: column; gap: 10px; }
        .opt-wrap input[type=radio] { display: none; }
        .opt-label { display: flex; align-items: center; gap: 14px; padding: 13px 18px; border: 1.5px solid #e4e4e7; border-radius: 12px; cursor: pointer; transition: all 0.15s; user-select: none; font-size: 0.92rem; background: #fafafa; }
        .opt-label:hover { border-color: #a1a1aa; background: #f4f4f5; transform: translateX(2px); }
        .opt-wrap input[type=radio]:checked ~ .opt-label { border-color: #09090b; background: #f4f4f5; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .opt-key { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1.5px solid #e4e4e7; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; transition: all 0.15s; }
        .opt-wrap input[type=radio]:checked ~ .opt-label .opt-key { background: #09090b; color: #fff; border-color: #09090b; }

        /* Essay textarea */
        .essay-area { width: 100%; border: 1.5px solid #e4e4e7; border-radius: 12px; padding: 14px 16px; font-size: 0.92rem; font-family: 'Geist', sans-serif; resize: vertical; min-height: 120px; outline: none; transition: border-color 0.15s; background: #fafafa; }
        .essay-area:focus { border-color: #09090b; background: #fff; }

        /* Submit bar */
        .submit-bar { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); border-top: 1px solid #e4e4e7; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; z-index: 100; box-shadow: 0 -4px 16px rgba(0,0,0,0.06); }
        .submit-bar-info { font-size: 0.85rem; color: #71717a; }
        .submit-bar-info strong { color: #09090b; }
        .submit-btn { background: #09090b; color: #fff; border: none; border-radius: 10px; padding: 12px 28px; font-size: 0.9rem; font-weight: 600; font-family: 'Geist', sans-serif; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.15s, transform 0.1s; }
        .submit-btn:hover { background: #27272a; transform: translateY(-1px); }
        .submit-btn:active { transform: translateY(0); }
    </style>
</head>
<body>

<!-- Top Nav -->
<div class="exam-nav">
    <div class="exam-nav-brand">
        <span class="dot"></span>
        Interview Exam
    </div>
    <div class="exam-badge"><i class="bi bi-calendar3 me-1"></i><?= date('F d, Y') ?></div>
</div>

<!-- Progress bar -->
<div class="exam-progress-bar">
    <div class="exam-progress-fill" id="progressFill" style="width:0%"></div>
</div>

<!-- Hero -->
<div class="exam-hero">
    <div class="exam-hero-label">Applicant Examination</div>
    <h1><?= htmlspecialchars($interview['firstname'] . ' ' . $interview['lastname']) ?></h1>
    <div class="exam-hero-sub"><?= htmlspecialchars($interview['job_title']) ?></div>
    <div class="exam-stats">
        <div class="exam-stat">
            <div class="exam-stat-val"><?= count($questions) ?></div>
            <div class="exam-stat-label">Questions</div>
        </div>
        <div class="exam-stat">
            <div class="exam-stat-val" id="answeredCount">0</div>
            <div class="exam-stat-label">Answered</div>
        </div>
        <div class="exam-stat">
            <div class="exam-stat-val"><?= array_sum(array_column($questions, 'points')) ?></div>
            <div class="exam-stat-label">Total Points</div>
        </div>
    </div>
</div>

<div class="exam-wrap">
    <form method="POST" id="examForm">
        <?php foreach ($questions as $i => $q): ?>
        <div class="q-card" id="qcard_<?= $q['question_id'] ?>">
            <div class="q-header">
                <span class="q-num-badge">Q<?= $i + 1 ?></span>
                <?php if ($q['question_type'] === 'multiple_choice'): ?>
                <span class="q-pts"><i class="bi bi-star-fill me-1" style="color:#ca8a04;font-size:0.65rem;"></i><?= $q['points'] ?> pt<?= $q['points'] > 1 ? 's' : '' ?></span>
                <?php else: ?>
                <span class="q-essay-badge"><i class="bi bi-pencil me-1"></i>Essay</span>
                <?php endif; ?>
            </div>
            <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>

            <?php if ($q['question_type'] === 'multiple_choice'): ?>
                <div class="opts-grid">
                <?php foreach (['a','b','c','d'] as $opt): ?>
                    <?php if ($q["option_{$opt}"]): ?>
                    <div class="opt-wrap">
                        <input type="radio" name="q_<?= $q['question_id'] ?>" id="q<?= $q['question_id'] ?>_<?= $opt ?>" value="<?= $opt ?>" onchange="onAnswer(<?= $q['question_id'] ?>)">
                        <label for="q<?= $q['question_id'] ?>_<?= $opt ?>" class="opt-label">
                            <span class="opt-key"><?= strtoupper($opt) ?></span>
                            <span><?= htmlspecialchars($q["option_{$opt}"]) ?></span>
                        </label>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <textarea name="q_<?= $q['question_id'] ?>" class="essay-area"
                    placeholder="Write your answer here..."
                    oninput="onAnswer(<?= $q['question_id'] ?>)"></textarea>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <input type="hidden" name="submit_exam" value="1">
    </form>
</div>

<div class="submit-bar">
    <div class="submit-bar-info">
        <strong id="answeredCountBar">0</strong> of <?= count($questions) ?> answered
    </div>
    <button type="button" class="submit-btn" onclick="submitExam()">
        <i class="bi bi-send-fill"></i> Submit Exam
    </button>
</div>

<script>
const totalQ = <?= count($questions) ?>;
const answered = new Set();

function onAnswer(qid) {
    answered.add(qid);
    document.getElementById('qcard_' + qid)?.classList.add('answered');
    const count = answered.size;
    document.getElementById('answeredCount').textContent = count;
    document.getElementById('answeredCountBar').textContent = count;
    document.getElementById('progressFill').style.width = (count / totalQ * 100) + '%';
}

function submitExam() {
    if (answered.size < totalQ) {
        if (!confirm(`You have answered ${answered.size} of ${totalQ} questions. Submit anyway?`)) return;
    } else {
        if (!confirm('Submit your exam? You cannot change your answers after submitting.')) return;
    }
    document.getElementById('examForm').submit();
}
</script>
</body>
</html>
