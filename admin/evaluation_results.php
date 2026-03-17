<?php
session_start();
require_once '../modules/Performance.php';
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';
$perf = new Performance();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];

$review_id = (int)($_GET['review_id'] ?? 0);
if (!$review_id) { header('Location: evaluation_forms.php'); exit; }

$review  = $perf->getReviewById($review_id);
if (!$review) { header('Location: evaluation_forms.php'); exit; }

$goals    = $perf->getGoals($review_id);
$feedback = $perf->getFeedback($review_id);
$ratings  = $perf->getRatings($review_id);
$avgRating = $perf->getAverageRating($review_id);
$ratingMap = array_column($ratings, 'score', 'category');
$categories = Performance::ratingCategories();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_goal') {
        $title = trim($_POST['goal_title'] ?? '');
        if (!$title) { $error = 'Goal title is required.'; }
        else {
            $perf->addGoal($review_id, $title, $_POST['goal_desc'] ?? '', $_POST['target_date'] ?? '');
            $audit->log($admin_id, $admin_name, 'Add Goal', 'Performance', "Added goal '{$title}' to review ID {$review_id}");
            $success = 'Goal added.';
        }
    } elseif ($action === 'update_goal') {
        $perf->updateGoalStatus((int)$_POST['goal_id'], $_POST['goal_status']);
        $audit->log($admin_id, $admin_name, 'Update Goal', 'Performance', "Goal ID {$_POST['goal_id']} set to {$_POST['goal_status']} (review {$review_id})");
        $success = 'Goal updated.';
    } elseif ($action === 'delete_goal') {
        $perf->deleteGoal((int)$_POST['goal_id']);
        $audit->log($admin_id, $admin_name, 'Delete Goal', 'Performance', "Deleted goal ID {$_POST['goal_id']} from review {$review_id}");
        $success = 'Goal removed.';
    } elseif ($action === 'add_feedback') {
        $strengths    = trim($_POST['strengths'] ?? '');
        $improvements = trim($_POST['improvements'] ?? '');
        $date         = $_POST['feedback_date'] ?? date('Y-m-d');
        if (!$strengths && !$improvements) { $error = 'Enter at least strengths or improvements.'; }
        else {
            $perf->addFeedback($review_id, $date, $strengths, $improvements, $admin_id);
            $audit->log($admin_id, $admin_name, 'Add Feedback', 'Performance', "Added feedback to review ID {$review_id}");
            $success = 'Feedback added.';
        }
    } elseif ($action === 'delete_feedback') {
        $perf->deleteFeedback((int)$_POST['feedback_id']);
        $audit->log($admin_id, $admin_name, 'Delete Feedback', 'Performance', "Deleted feedback ID {$_POST['feedback_id']} from review {$review_id}");
        $success = 'Feedback removed.';
    } elseif ($action === 'save_ratings') {
        $perf->saveRatings($review_id, $_POST['ratings'] ?? []);
        $audit->log($admin_id, $admin_name, 'Save Ratings', 'Performance', "Saved ratings for review ID {$review_id}");
        $success = 'Ratings saved.';
    } elseif ($action === 'extend') {
        $new_end = $_POST['new_end'] ?? '';
        if (!$new_end) {
            $error = 'New end date is required.';
        } elseif ($new_end <= date('Y-m-d')) {
            $error = 'New end date must be in the future.';
        } else {
            $perf->extendReview($review_id, $new_end);
            $audit->log($admin_id, $admin_name, 'Extend Review', 'Performance', "Extended review ID {$review_id} to {$new_end}");
            $success = 'Probation extended. Review is now active again.';
        }
    } elseif ($action === 'finalize') {
        $status  = $_POST['final_status'] ?? '';
        $remarks = trim($_POST['final_remarks'] ?? '');

        // Re-fetch fresh data for validation
        $currentGoals   = $perf->getGoals($review_id);
        $currentRatings = $perf->getRatings($review_id);
        $currentFeedback = $perf->getFeedback($review_id);

        $pendingGoals = array_filter($currentGoals, fn($g) => in_array($g['status'], ['Pending', 'In Progress']));
        $ratedCount   = count($currentRatings);
        $totalCats    = count(Performance::ratingCategories());

        if (!$status) {
            $error = 'Select a final decision.';
        } elseif (empty($currentGoals)) {
            $error = 'Add at least one goal before finalizing.';
        } elseif (!empty($pendingGoals)) {
            $names = implode(', ', array_column($pendingGoals, 'goal_title'));
            $error = 'All goals must be resolved before finalizing. Still pending: ' . $names;
        } elseif (empty($currentFeedback)) {
            $error = 'Add at least one feedback entry before finalizing.';
        } elseif ($ratedCount < $totalCats) {
            $error = 'Complete all performance ratings before finalizing.';
        } else {
            $perf->finalizeReview($review_id, $status, $remarks, $admin_id);
            $audit->log($admin_id, $admin_name, 'Finalize Review', 'Performance', "Review ID {$review_id} finalized as {$status}");
            $success = 'Review finalized.';
        }
    }

    // Reload after POST
    header("Location: evaluation_results.php?review_id={$review_id}" . ($success ? '&msg=1' : ''));
    exit;
}

$success = isset($_GET['msg']) ? 'Changes saved.' : '';

// Recalculate after reload
$goals     = $perf->getGoals($review_id);
$feedback  = $perf->getFeedback($review_id);
$ratings   = $perf->getRatings($review_id);
$avgRating = $perf->getAverageRating($review_id);
$ratingMap = array_column($ratings, 'score', 'category');
$ratingLabel = $avgRating ? $perf->getRatingLabel($avgRating) : null;

$goalCount    = count($goals);
$achievedCount = count(array_filter($goals, fn($g) => $g['status'] === 'Achieved'));
$goalPct      = $goalCount > 0 ? round(($achievedCount / $goalCount) * 100) : 0;

$daysLeft = (int)ceil((strtotime($review['probation_end']) - time()) / 86400);
$statusColors = ['Ongoing' => 'warning', 'Passed' => 'success', 'Failed' => 'danger', 'Extended' => 'info'];
$goalStatusColors = ['Pending' => 'secondary', 'In Progress' => 'warning', 'Achieved' => 'success', 'Not Achieved' => 'danger'];
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <a href="evaluation_forms.php" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to Reviews</a>
            <h2 class="mb-0 mt-1"><?= htmlspecialchars(($review['firstname'] ?? '') . ' ' . ($review['lastname'] ?? '')) ?>
            <?= (!empty($employee['suffix']) && strtolower($employee['suffix']) !== 'none') 
                ? ucfirst($employee['suffix']) . '.' 
                : null ?>
        </h2>
            <p class="text-muted mb-0"><?= htmlspecialchars($review['job_title'] ?? '') ?> &mdash; Probation Review</p>
        </div>
        <div class="text-end">
            <span class="badge bg-<?= $statusColors[$review['status']] ?? 'secondary' ?> fs-6"><?= $review['status'] ?></span>
            <?php if ($review['status'] === 'Ongoing'): ?>
                <br><small class="text-<?= $daysLeft <= 7 ? 'danger' : 'muted' ?> mt-1 d-block">
                    <?= $daysLeft > 0 ? "{$daysLeft} days remaining" : 'Period overdue' ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Probation Period</small>
            <div class="mt-1 fw-semibold"><?= date('M d', strtotime($review['probation_start'])) ?> – <?= date('M d, Y', strtotime($review['probation_end'])) ?></div>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Goals Progress</small>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="progress flex-grow-1" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:<?= $goalPct ?>%"></div>
                </div>
                <small><?= $goalPct ?>%</small>
            </div>
            <small class="text-muted"><?= $achievedCount ?>/<?= $goalCount ?> achieved</small>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Average Rating</small>
            <?php if ($avgRating): ?>
                <div class="mt-1"><strong><?= $avgRating ?>/5</strong>
                    <span class="badge bg-<?= $ratingLabel['color'] ?> ms-1"><?= $ratingLabel['label'] ?></span>
                </div>
            <?php else: ?>
                <div class="text-muted mt-1">Not rated yet</div>
            <?php endif; ?>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <small class="text-muted">Feedback Entries</small>
            <h3 class="mb-0 mt-1"><?= count($feedback) ?></h3>
        </div></div></div>
    </div>

    <div class="row g-4">
        <!-- LEFT: Goals + Feedback -->
        <div class="col-md-7">

            <!-- Goals -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bullseye me-2"></i>Goals</span>
                    <?php if ($review['status'] === 'Ongoing'): ?>
                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addGoalModal">
                        <i class="bi bi-plus"></i> Add Goal
                    </button>
                    <?php endif; ?>
                </div>
                <?php if (!empty($goals)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($goals as $g): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?= htmlspecialchars($g['goal_title']) ?></strong>
                                <?php if ($g['description']): ?>
                                    <p class="text-muted mb-1 small"><?= htmlspecialchars($g['description']) ?></p>
                                <?php endif; ?>
                                <?php if ($g['target_date']): ?>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>Target: <?= date('M d, Y', strtotime($g['target_date'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-1 align-items-center ms-2">
                                <span class="badge bg-<?= $goalStatusColors[$g['status']] ?>"><?= $g['status'] ?></span>
                                <?php if ($review['status'] === 'Ongoing'): ?>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php foreach (['Pending','In Progress','Achieved','Not Achieved'] as $gs): ?>
                                        <li>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="update_goal">
                                                <input type="hidden" name="goal_id" value="<?= $g['goal_id'] ?>">
                                                <input type="hidden" name="goal_status" value="<?= $gs ?>">
                                                <button type="submit" class="dropdown-item <?= $g['status'] === $gs ? 'active' : '' ?>"><?= $gs ?></button>
                                            </form>
                                        </li>
                                        <?php endforeach; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" onsubmit="return confirm('Remove this goal?')">
                                                <input type="hidden" name="action" value="delete_goal">
                                                <input type="hidden" name="goal_id" value="<?= $g['goal_id'] ?>">
                                                <button type="submit" class="dropdown-item text-danger">Remove</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="card-body text-center text-muted py-4">No goals set yet.</div>
                <?php endif; ?>
            </div>

            <!-- Feedback -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-chat-left-text me-2"></i>Feedback</span>
                    <?php if ($review['status'] === 'Ongoing'): ?>
                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addFeedbackModal">
                        <i class="bi bi-plus"></i> Add Feedback
                    </button>
                    <?php endif; ?>
                </div>
                <?php if (!empty($feedback)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($feedback as $fb): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($fb['feedback_date'])) ?></small>
                            <?php if ($review['status'] === 'Ongoing'): ?>
                            <form method="POST" onsubmit="return confirm('Remove this feedback?')">
                                <input type="hidden" name="action" value="delete_feedback">
                                <input type="hidden" name="feedback_id" value="<?= $fb['feedback_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php if ($fb['strengths']): ?>
                        <div class="mt-2">
                            <small class="fw-semibold text-success"><i class="bi bi-check-circle me-1"></i>Strengths</small>
                            <p class="mb-1 small"><?= nl2br(htmlspecialchars($fb['strengths'])) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if ($fb['improvements']): ?>
                        <div class="mt-1">
                            <small class="fw-semibold text-warning"><i class="bi bi-arrow-up-circle me-1"></i>Areas for Improvement</small>
                            <p class="mb-0 small"><?= nl2br(htmlspecialchars($fb['improvements'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="card-body text-center text-muted py-4">No feedback entries yet.</div>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT: Ratings + Finalize -->
        <div class="col-md-5">

            <!-- Ratings -->
            <div class="card mb-4">
                <div class="card-header bg-white"><i class="bi bi-star me-2"></i>Performance Ratings</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="save_ratings">
                        <?php foreach ($categories as $cat): ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold mb-1"><?= $cat ?></label>
                            <div class="d-flex gap-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="flex-fill text-center">
                                    <input type="radio" name="ratings[<?= $cat ?>]" value="<?= $i ?>"
                                           class="d-none rating-radio"
                                           <?= ($ratingMap[$cat] ?? 0) == $i ? 'checked' : '' ?>
                                           <?= $review['status'] !== 'Ongoing' ? 'disabled' : '' ?>>
                                    <span class="d-block border rounded py-1 rating-btn <?= ($ratingMap[$cat] ?? 0) == $i ? 'active' : '' ?>"
                                          style="cursor:pointer;font-size:0.8rem;"><?= $i ?></span>
                                </label>
                                <?php endfor; ?>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Poor</small>
                                <small class="text-muted">Excellent</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if ($review['status'] === 'Ongoing'): ?>
                        <button type="submit" class="btn btn-dark w-100 mt-2">Save Ratings</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Finalize -->
            <?php if ($review['status'] === 'Ongoing'): ?>
            <?php
                $pendingGoalsCount = count(array_filter($goals, fn($g) => in_array($g['status'], ['Pending', 'In Progress'])));
                $canFinalize = $goalCount > 0 && $pendingGoalsCount === 0 && count($feedback) > 0 && count($ratings) >= count($categories);
            ?>
            <div class="card border-dark">
                <div class="card-header bg-dark text-white"><i class="bi bi-flag me-2"></i>Finalize Review</div>
                <div class="card-body">
                    <!-- Checklist -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2 fw-semibold">Requirements</small>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-<?= $goalCount > 0 ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>"></i>
                            <small>At least one goal added</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-<?= $pendingGoalsCount === 0 && $goalCount > 0 ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>"></i>
                            <small>All goals resolved <?= $pendingGoalsCount > 0 ? "({$pendingGoalsCount} still pending)" : '' ?></small>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-<?= count($feedback) > 0 ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>"></i>
                            <small>At least one feedback entry</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-<?= count($ratings) >= count($categories) ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>"></i>
                            <small>All categories rated (<?= count($ratings) ?>/<?= count($categories) ?>)</small>
                        </div>
                    </div>
                    <?php if ($canFinalize): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="finalize">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Final Decision <span class="text-danger">*</span></label>
                            <select name="final_status" class="form-select" required>
                                <option value="">Select outcome...</option>
                                <option value="Passed">Passed — Regularize Employee</option>
                                <option value="Failed">Failed — End Employment</option>
                                <option value="Extended">Extended — Extend Probation</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Final Remarks</label>
                            <textarea name="final_remarks" class="form-control" rows="3" placeholder="Summary of performance during probation..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100" onclick="return confirm('Finalize this review? This cannot be undone.')">
                            <i class="bi bi-flag me-1"></i> Finalize Review
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning py-2 mb-0" style="font-size:0.8rem;">
                        <i class="bi bi-lock me-1"></i> Complete all requirements above to unlock finalization.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif ($review['status'] === 'Extended'): ?>
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark"><i class="bi bi-clock-history me-2"></i>Probation Extended</div>
                <div class="card-body">
                    <?php if ($review['final_remarks']): ?>
                        <p class="mb-3 small"><?= nl2br(htmlspecialchars($review['final_remarks'])) ?></p>
                    <?php endif; ?>
                    <p class="text-muted small mb-3">Set a new probation end date to re-open this review for continued evaluation.</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="extend">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New End Date <span class="text-danger">*</span></label>
                            <input type="date" name="new_end" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-semibold" onclick="return confirm('Re-open this review with the new end date?')">
                            <i class="bi bi-arrow-clockwise me-1"></i> Re-open Review
                        </button>
                    </form>
                </div>
            </div>
            <?php elseif ($review['final_remarks']): ?>
            <div class="card">
                <div class="card-header bg-white"><i class="bi bi-flag me-2"></i>Final Remarks</div>
                <div class="card-body">
                    <p class="mb-1"><?= nl2br(htmlspecialchars($review['final_remarks'])) ?></p>
                    <?php if ($review['reviewed_at']): ?>
                        <small class="text-muted">Finalized: <?= date('M d, Y', strtotime($review['reviewed_at'])) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Add Goal Modal -->
<div class="modal fade" id="addGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_goal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bullseye me-2"></i>Add Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Goal Title <span class="text-danger">*</span></label>
                        <input type="text" name="goal_title" class="form-control" placeholder="e.g. Complete onboarding training" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="goal_desc" class="form-control" rows="2" placeholder="Optional details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Date</label>
                        <input type="date" name="target_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Add Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Feedback Modal -->
<div class="modal fade" id="addFeedbackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_feedback">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Add Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Feedback Date</label>
                        <input type="date" name="feedback_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-success"><i class="bi bi-check-circle me-1"></i>Strengths</label>
                        <textarea name="strengths" class="form-control" rows="3" placeholder="What the employee is doing well..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-warning"><i class="bi bi-arrow-up-circle me-1"></i>Areas for Improvement</label>
                        <textarea name="improvements" class="form-control" rows="3" placeholder="What needs to improve..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.rating-radio:checked + .rating-btn,
.rating-btn.active {
    background: #09090b;
    color: #fff;
    border-color: #09090b;
}
.rating-btn:hover { background: #f3f4f6; }
</style>
<script>
document.querySelectorAll('.rating-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const group = this.closest('.d-flex');
        group.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('active'));
        this.nextElementSibling.classList.add('active');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
