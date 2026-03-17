<?php
session_start();
require_once '../modules/Recognition.php';
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';
$recog = new Recognition();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'post') {
        $nominee_id = (int)($_POST['nominee_id'] ?? 0);
        $award_type = $_POST['award_type'] ?? '';
        $message    = trim($_POST['message'] ?? '');

        if (!$nominee_id || !$award_type || !$message) {
            $error = 'All fields are required.';
        } else {
            $recog->createPost($nominee_id, $award_type, $message, $admin_id);
            $audit->log($admin_id, $admin_name, 'Post Recognition', 'Recognition', "Awarded '{$award_type}' to employee ID {$nominee_id}");
            $success = 'Recognition posted!';
        }
    } elseif ($action === 'delete') {
        $recog->deletePost((int)$_POST['post_id']);
        $audit->log($admin_id, $admin_name, 'Delete Recognition', 'Recognition', "Deleted recognition post ID {$_POST['post_id']}");
        $success = 'Post removed.';
    }
}

$employees  = $recog->getActiveEmployees();
$posts      = $recog->getAllPosts();
$stats      = $recog->getStats();
$awardTypes = Recognition::awardTypes();

$filterType = $_GET['type'] ?? '';
if ($filterType) {
    $posts = $recog->getPostsByType($filterType);
}
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Points & Rewards</h2>
            <p class="text-muted mb-0">Recognize and celebrate employee achievements</p>
        </div>
        <div class="d-flex gap-2">
            <a href="leaderboard.php" class="btn btn-outline-dark"><i class="bi bi-trophy me-1"></i>Leaderboard</a>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#postModal">
                <i class="bi bi-award me-1"></i> Give Recognition
            </button>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card"><div class="card-body">
            <small class="text-muted">Total Recognitions</small>
            <h3 class="mb-0"><?= $stats['total'] ?></h3>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <small class="text-muted">This Month</small>
            <h3 class="mb-0"><?= $stats['month'] ?></h3>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <small class="text-muted">Employee of the Month</small>
            <h3 class="mb-0"><?= $stats['empotm'] ?></h3>
        </div></div></div>
    </div>

    <!-- Filter by Award Type -->
    <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="points_rewards.php" class="btn btn-sm <?= !$filterType ? 'btn-dark' : 'btn-outline-secondary' ?>">All</a>
        <?php foreach ($awardTypes as $type): ?>
            <a href="?type=<?= urlencode($type) ?>" class="btn btn-sm <?= $filterType === $type ? 'btn-dark' : 'btn-outline-secondary' ?>">
                <i class="bi <?= Recognition::awardIcon($type) ?> me-1"></i><?= $type ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Recognition Feed -->
    <?php if (!empty($posts)): ?>
    <div class="row g-3">
        <?php foreach ($posts as $p): ?>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-dark">
                            <i class="bi <?= Recognition::awardIcon($p['award_type']) ?> me-1"></i>
                            <?= htmlspecialchars($p['award_type']) ?>
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted"><?= date('M d, Y', strtotime($p['created_at'])) ?></small>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Remove this recognition?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="post_id" value="<?= $p['post_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;font-size:1rem;flex-shrink:0;">
                            <?= strtoupper(substr($p['nominee_first'] ?? '?', 0, 1)) ?>
                        </div>
                        <div>
                            <strong><?= htmlspecialchars(($p['nominee_first'] ?? '') . ' ' . ($p['nominee_last'] ?? '')) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($p['nominee_job'] ?? '') ?></small>
                        </div>
                    </div>

                    <p class="mb-2" style="font-size:0.95rem;">"<?= htmlspecialchars($p['message']) ?>"</p>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>Awarded by HR
                        </small>
                        <span class="text-muted small"><i class="bi bi-heart me-1"></i><?= $p['reaction_count'] ?> reactions</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-award" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3">No recognitions yet. Start celebrating your team!</p>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#postModal">Give First Recognition</button>
    </div></div>
    <?php endif; ?>
</div>

<!-- Post Recognition Modal -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-award me-2"></i>Give Recognition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recognize Employee <span class="text-danger">*</span></label>
                        <select name="nominee_id" class="form-select" required>
                            <option value="">Select employee...</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>">
                                    <?= htmlspecialchars(($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? '')) ?>
                                    <?php if ($emp['job_title']): ?> — <?= htmlspecialchars($emp['job_title']) ?><?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Award Type <span class="text-danger">*</span></label>
                        <select name="award_type" class="form-select" required>
                            <option value="">Select award...</option>
                            <?php foreach ($awardTypes as $type): ?>
                                <option value="<?= $type ?>"><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="3"
                            placeholder="e.g. Outstanding guest service this week — multiple guests mentioned you by name!" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-award me-1"></i>Post Recognition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
