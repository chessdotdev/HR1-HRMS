<?php
session_start();
require_once '../config/Database.php';
require_once 'includes/verify_admin.php';

$db   = new Database();
$conn = $db->connect();

$admin_id = $_SESSION['admin_id'];
$success  = $error = '';

$jobTitles = $conn->query("SELECT DISTINCT title FROM job_openings ORDER BY title")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $job_title = trim($_POST['job_title'] ?? '') ?: null;
        $questions_post = $_POST['questions'] ?? [];
        $saved = 0;

        foreach ($questions_post as $q) {
            $question_text = trim($q['question_text'] ?? '');
            $question_type = $q['question_type'] ?? 'multiple_choice';
            $points        = max(1, (int)($q['points'] ?? 1));
            if (!$question_text) continue;

            if ($question_type === 'multiple_choice') {
                $option_a       = trim($q['option_a'] ?? '');
                $option_b       = trim($q['option_b'] ?? '');
                $option_c       = trim($q['option_c'] ?? '') ?: null;
                $option_d       = trim($q['option_d'] ?? '') ?: null;
                $correct_answer = $q['correct_answer'] ?? null;
                if (!$option_a || !$option_b || !$correct_answer) continue;
                $conn->prepare("INSERT INTO exam_questions (job_title, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, points, created_by)
                    VALUES (:jt,:qt,'multiple_choice',:a,:b,:c,:d,:ans,:pts,:by)")
                    ->execute([':jt'=>$job_title,':qt'=>$question_text,':a'=>$option_a,':b'=>$option_b,':c'=>$option_c,':d'=>$option_d,':ans'=>$correct_answer,':pts'=>$points,':by'=>$admin_id]);
            } else {
                $conn->prepare("INSERT INTO exam_questions (job_title, question_text, question_type, points, created_by) VALUES (:jt,:qt,'text',:pts,:by)")
                    ->execute([':jt'=>$job_title,':qt'=>$question_text,':pts'=>$points,':by'=>$admin_id]);
            }
            $saved++;
        }
        $success = $saved > 0 ? "{$saved} question" . ($saved > 1 ? 's' : '') . ' added.' : '';
        if (!$success) $error = 'No valid questions were saved. Please fill in all required fields.';

    } elseif ($action === 'delete') {
        $conn->prepare("DELETE FROM exam_questions WHERE question_id = :id")->execute([':id' => (int)$_POST['question_id']]);
        $success = 'Question deleted.';
    }
}

$filterJob = $_GET['job'] ?? '';
if ($filterJob) {
    $stmt = $conn->prepare("SELECT * FROM exam_questions WHERE job_title = :jt OR job_title IS NULL ORDER BY job_title, question_id");
    $stmt->execute([':jt' => $filterJob]);
} else {
    $stmt = $conn->query("SELECT * FROM exam_questions ORDER BY job_title, question_id");
}
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Exam Questions</h2>
            <p class="text-muted mb-0">Manage interview exam questions per job title</p>
        </div>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-1"></i>Add Question
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Filter by job title -->
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="exam_questions.php" class="btn btn-sm <?= !$filterJob ? 'btn-dark' : 'btn-outline-secondary' ?>">All</a>
        <?php foreach ($jobTitles as $jt): ?>
            <a href="?job=<?= urlencode($jt) ?>" class="btn btn-sm <?= $filterJob === $jt ? 'btn-dark' : 'btn-outline-secondary' ?>"><?= htmlspecialchars($jt) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="card" style="border:1px solid #e4e4e7;">
        <div class="card-header bg-white"><i class="bi bi-question-circle me-2"></i>Questions (<?= count($questions) ?>)</div>
        <?php if (!empty($questions)): ?>
        <div class="list-group list-group-flush">
            <?php foreach ($questions as $i => $q): ?>
            <div class="list-group-item py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 me-3">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-secondary" style="font-size:0.7rem;"><?= $q['job_title'] ?? 'All Jobs' ?></span>
                            <span class="badge <?= $q['question_type'] === 'multiple_choice' ? 'bg-info text-dark' : 'bg-warning text-dark' ?>" style="font-size:0.7rem;">
                                <?= $q['question_type'] === 'multiple_choice' ? 'Multiple Choice' : 'Essay' ?>
                            </span>
                            <span class="badge bg-light text-dark border" style="font-size:0.7rem;"><?= $q['points'] ?> pt<?= $q['points'] > 1 ? 's' : '' ?></span>
                        </div>
                        <div class="fw-medium" style="font-size:0.9rem;"><?= ($i+1) ?>. <?= htmlspecialchars($q['question_text']) ?></div>
                        <?php if ($q['question_type'] === 'multiple_choice'): ?>
                        <div class="mt-2 row g-1" style="font-size:0.82rem;">
                            <?php foreach (['a','b','c','d'] as $opt): ?>
                                <?php if ($q["option_{$opt}"]): ?>
                                <div class="col-md-6">
                                    <span class="<?= $q['correct_answer'] === $opt ? 'text-success fw-semibold' : 'text-muted' ?>">
                                        <?= $q['correct_answer'] === $opt ? '<i class="bi bi-check-circle-fill me-1"></i>' : '' ?>
                                        <?= strtoupper($opt) ?>. <?= htmlspecialchars($q["option_{$opt}"]) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('Delete this question?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="question_id" value="<?= $q['question_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-question-circle" style="font-size:2.5rem;color:#d4d4d8;"></i>
            <p class="mt-3 mb-0">No questions yet. Add your first question.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Questions Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Exam Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="bulkForm">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3 p-3 rounded" style="background:#f4f4f5;border:1px solid #e4e4e7;">
                        <label class="form-label fw-semibold mb-1">Job Title <small class="text-muted fw-normal">(applies to all questions below)</small></label>
                        <select name="job_title" class="form-select form-select-sm" style="max-width:300px;">
                            <option value="">All Jobs</option>
                            <?php foreach ($jobTitles as $jt): ?>
                                <option value="<?= htmlspecialchars($jt) ?>"><?= htmlspecialchars($jt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="questionsContainer"></div>
                    <button type="button" class="btn btn-outline-dark btn-sm mt-1" onclick="addQuestion()">
                        <i class="bi bi-plus-lg me-1"></i>Add Another Question
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="bulkForm" class="btn btn-dark"><i class="bi bi-save me-1"></i>Save All Questions</button>
            </div>
        </div>
    </div>
</div>

<script>
let qIndex = 0;

function addQuestion() {
    const idx = qIndex++;
    const container = document.getElementById('questionsContainer');
    const div = document.createElement('div');
    div.className = 'q-entry mb-3 p-3 rounded';
    div.style = 'border:1px solid #e4e4e7;background:#fff;position:relative;';
    div.id = 'qentry_' + idx;
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold" style="font-size:0.85rem;">Question <span class="q-num">${container.children.length + 1}</span></span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeQuestion('qentry_${idx}')" title="Remove"><i class="bi bi-trash"></i></button>
        </div>
        <div class="mb-2">
            <textarea name="questions[${idx}][question_text]" class="form-control" rows="2" placeholder="Enter question..." required></textarea>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-6">
                <select name="questions[${idx}][question_type]" class="form-select form-select-sm" onchange="toggleMC(this, ${idx})">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="text">Essay</option>
                </select>
            </div>
            <div class="col-6" id="pts_${idx}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">pts</span>
                    <input type="number" name="questions[${idx}][points]" class="form-control" value="1" min="1" max="10">
                </div>
            </div>
        </div>
        <div id="mc_${idx}">
            <div class="row g-2 mb-1">
                <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text">A</span><input type="text" name="questions[${idx}][option_a]" class="form-control" placeholder="Option A" required></div></div>
                <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text">B</span><input type="text" name="questions[${idx}][option_b]" class="form-control" placeholder="Option B" required></div></div>
                <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text">C</span><input type="text" name="questions[${idx}][option_c]" class="form-control" placeholder="Option C (optional)"></div></div>
                <div class="col-6"><div class="input-group input-group-sm"><span class="input-group-text">D</span><input type="text" name="questions[${idx}][option_d]" class="form-control" placeholder="Option D (optional)"></div></div>
            </div>
            <div class="col-6">
                <select name="questions[${idx}][correct_answer]" class="form-select form-select-sm" required>
                    <option value="">Correct answer</option>
                    <option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(div);
    renumberQuestions();
}

function removeQuestion(id) {
    const el = document.getElementById(id);
    if (el) { el.remove(); renumberQuestions(); }
}

function renumberQuestions() {
    document.querySelectorAll('#questionsContainer .q-num').forEach((el, i) => el.textContent = i + 1);
}

function toggleMC(select, idx) {
    const mc  = document.getElementById('mc_' + idx);
    const pts = document.getElementById('pts_' + idx);
    const isMC = select.value === 'multiple_choice';
    mc.style.display  = isMC ? '' : 'none';
    pts.style.display = isMC ? '' : 'none';
    mc.querySelectorAll('[required]').forEach(el => el.required = isMC);
}

// Start with 1 question on modal open
document.getElementById('addModal').addEventListener('show.bs.modal', function () {
    const container = document.getElementById('questionsContainer');
    if (container.children.length === 0) addQuestion();
});
</script>

<?php include 'includes/footer.php'; ?>
