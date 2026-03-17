<?php
session_start();
include 'includes/header.php';
require_once '../modules/Performance.php';

$perf   = new Performance();
$review = $perf->getReviewByEmployee($_SESSION['employee_id']);

$statusColors = ['Ongoing' => 'warning', 'Passed' => 'success', 'Failed' => 'danger', 'Extended' => 'info'];
$goalStatusColors = ['Pending' => 'secondary', 'In Progress' => 'warning', 'Achieved' => 'success', 'Not Achieved' => 'danger'];
$categories = Performance::ratingCategories();
?>

<div class="page-header mb-3">
    <h1 class="page-title">My Performance</h1>
    <p class="text-muted" style="font-size:0.875rem;">Your probation period review and progress</p>
</div>

<?php if (!$review): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-graph-up" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3">No performance review has been assigned yet.</p>
        <small class="text-muted">Your HR manager will set up your probation review.</small>
    </div>
</div>

<?php else:
    $review_id  = $review['review_id'];
    $goals      = $perf->getGoals($review_id);
    $feedback   = $perf->getFeedback($review_id);
    $ratings    = $perf->getRatings($review_id);
    $avgRating  = $perf->getAverageRating($review_id);
    $ratingMap  = array_column($ratings, 'score', 'category');
    $ratingLabel = $avgRating ? $perf->getRatingLabel($avgRating) : null;

    $goalCount     = count($goals);
    $achievedCount = count(array_filter($goals, fn($g) => $g['status'] === 'Achieved'));
    $goalPct       = $goalCount > 0 ? round(($achievedCount / $goalCount) * 100) : 0;
    $daysLeft      = (int)ceil((strtotime($review['probation_end']) - time()) / 86400);
?>

<!-- Status Banner -->
<div class="alert alert-<?= $statusColors[$review['status']] ?? 'secondary' ?> d-flex justify-content-between align-items-center mb-4" style="border-radius:8px;">
    <div>
        <strong>Probation Status: <?= $review['status'] ?></strong>
        <span class="ms-3 text-muted" style="font-size:0.85rem;">
            <?= date('M d, Y', strtotime($review['probation_start'])) ?> — <?= date('M d, Y', strtotime($review['probation_end'])) ?>
        </span>
    </div>
    <?php if ($review['status'] === 'Ongoing'): ?>
        <span class="badge bg-<?= $daysLeft <= 7 ? 'danger' : 'dark' ?>">
            <?= $daysLeft > 0 ? "{$daysLeft} days left" : 'Period overdue' ?>
        </span>
    <?php endif; ?>
</div>

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Goals Progress</small>
                <div class="d-flex align-items-center gap-2 mt-2">
                    <div class="progress flex-grow-1" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:<?= $goalPct ?>%"></div>
                    </div>
                    <strong><?= $goalPct ?>%</strong>
                </div>
                <small class="text-muted"><?= $achievedCount ?>/<?= $goalCount ?> goals achieved</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Performance Rating</small>
                <?php if ($avgRating): ?>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <h3 class="mb-0"><?= $avgRating ?><small class="text-muted fs-6">/5</small></h3>
                        <span class="badge bg-<?= $ratingLabel['color'] ?>"><?= $ratingLabel['label'] ?></span>
                    </div>
                <?php else: ?>
                    <p class="text-muted mt-2 mb-0">Not rated yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <small class="text-muted">Feedback Entries</small>
                <h3 class="mb-0 mt-2"><?= count($feedback) ?></h3>
                <small class="text-muted">from your manager</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Goals -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bullseye me-2"></i>My Goals</div>
            <?php if (!empty($goals)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($goals as $g): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong style="font-size:0.875rem;"><?= htmlspecialchars($g['goal_title']) ?></strong>
                            <?php if ($g['description']): ?>
                                <p class="text-muted mb-1" style="font-size:0.8rem;"><?= htmlspecialchars($g['description']) ?></p>
                            <?php endif; ?>
                            <?php if ($g['target_date']): ?>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($g['target_date'])) ?></small>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-<?= $goalStatusColors[$g['status']] ?> ms-2"><?= $g['status'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card-body text-center text-muted py-4">No goals assigned yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ratings -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-star me-2"></i>Performance Ratings</div>
            <div class="card-body">
                <?php if (!empty($ratings)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <?php $score = $ratingMap[$cat] ?? 0; $pct = ($score / 5) * 100; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-semibold"><?= $cat ?></small>
                            <small class="text-muted"><?= $score ?>/5</small>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-<?= $score >= 4 ? 'success' : ($score >= 3 ? 'primary' : ($score >= 2 ? 'warning' : 'danger')) ?>"
                                 style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($avgRating): ?>
                    <div class="mt-3 p-2 bg-light rounded d-flex justify-content-between align-items-center">
                        <small class="fw-semibold">Overall Average</small>
                        <span class="badge bg-<?= $ratingLabel['color'] ?>"><?= $avgRating ?>/5 — <?= $ratingLabel['label'] ?></span>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Ratings not submitted yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Feedback -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-chat-left-text me-2"></i>Manager Feedback</div>
            <?php if (!empty($feedback)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($feedback as $fb): ?>
                <div class="list-group-item">
                    <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($fb['feedback_date'])) ?></small>
                    <div class="row g-3">
                        <?php if ($fb['strengths']): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <small class="fw-semibold text-success d-block mb-1"><i class="bi bi-check-circle me-1"></i>Strengths</small>
                                <p class="mb-0" style="font-size:0.875rem;"><?= nl2br(htmlspecialchars($fb['strengths'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($fb['improvements']): ?>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:#fffbeb;border:1px solid #fde68a;">
                                <small class="fw-semibold text-warning d-block mb-1"><i class="bi bi-arrow-up-circle me-1"></i>Areas for Improvement</small>
                                <p class="mb-0" style="font-size:0.875rem;"><?= nl2br(htmlspecialchars($fb['improvements'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card-body text-center text-muted py-4">No feedback entries yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Final Remarks -->
    <?php if ($review['status'] !== 'Ongoing' && $review['final_remarks']): ?>
    <div class="col-12">
        <div class="card border-<?= $statusColors[$review['status']] ?? 'secondary' ?>">
            <div class="card-header bg-<?= $statusColors[$review['status']] ?? 'secondary' ?> <?= $review['status'] === 'Passed' ? 'text-white' : '' ?>">
                <i class="bi bi-flag me-2"></i>Final Review Decision — <?= $review['status'] ?>
            </div>
            <div class="card-body">
                <p class="mb-1"><?= nl2br(htmlspecialchars($review['final_remarks'])) ?></p>
                <?php if ($review['reviewed_at']): ?>
                    <small class="text-muted">Finalized on <?= date('M d, Y', strtotime($review['reviewed_at'])) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
