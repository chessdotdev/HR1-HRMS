<?php
session_start();
include 'includes/header.php';
require_once '../modules/Recognition.php';

$recog     = new Recognition();
$emp_id    = $_SESSION['employee_id'];
$myAwards  = $recog->getPostsByNominee($emp_id);
$awardTypes = Recognition::awardTypes();

// Breakdown by award type
$breakdown = [];
foreach ($myAwards as $a) {
    $breakdown[$a['award_type']] = ($breakdown[$a['award_type']] ?? 0) + 1;
}
arsort($breakdown);

// Total reactions received
$totalReactions = array_sum(array_column($myAwards, 'reaction_count'));
?>

<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">My Recognition History</h1>
        <p class="text-muted" style="font-size:0.875rem;">All recognitions you've received from HR</p>
    </div>
    <a href="recognition.php" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-grid-3x3-gap me-1"></i>All Recognitions
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
        <small class="text-muted">Total Awards</small>
        <h3 class="mb-0"><?= count($myAwards) ?></h3>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
        <small class="text-muted">Reactions Received</small>
        <h3 class="mb-0"><?= $totalReactions ?></h3>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
        <small class="text-muted">Award Types</small>
        <h3 class="mb-0"><?= count($breakdown) ?></h3>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
        <small class="text-muted">Latest Award</small>
        <div class="mt-1" style="font-size:0.82rem;font-weight:600;">
            <?= !empty($myAwards) ? date('M d, Y', strtotime($myAwards[0]['created_at'])) : '—' ?>
        </div>
    </div></div></div>
</div>

<?php if (!empty($myAwards)): ?>

<!-- Award Type Breakdown -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Award Breakdown</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($breakdown as $type => $count): ?>
            <span class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#f4f4f5;font-size:0.82rem;">
                <i class="bi <?= Recognition::awardIcon($type) ?>"></i>
                <span><?= htmlspecialchars($type) ?></span>
                <span class="badge bg-dark"><?= $count ?></span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Timeline</span>
        <span class="badge bg-dark"><?= count($myAwards) ?> total</span>
    </div>
    <div class="card-body">
        <div style="position:relative;padding-left:28px;">
            <div style="position:absolute;left:9px;top:0;bottom:0;width:2px;background:#e4e4e7;"></div>
            <?php foreach ($myAwards as $i => $a): ?>
            <div style="position:relative;<?= $i < count($myAwards)-1 ? 'margin-bottom:24px;' : '' ?>">
                <!-- dot -->
                <div style="position:absolute;left:-23px;top:5px;width:12px;height:12px;border-radius:50%;background:#09090b;border:2px solid #fff;box-shadow:0 0 0 2px #e4e4e7;"></div>
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                        <span class="badge bg-dark mb-2" style="font-size:0.72rem;">
                            <i class="bi <?= Recognition::awardIcon($a['award_type']) ?> me-1"></i>
                            <?= htmlspecialchars($a['award_type']) ?>
                        </span>
                        <p class="mb-1" style="font-size:0.875rem;color:#3f3f46;line-height:1.6;">
                            "<?= htmlspecialchars($a['message']) ?>"
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Awarded by HR</small>
                            <small class="text-muted"><i class="bi bi-heart me-1"></i><?= $a['reaction_count'] ?> reactions</small>
                        </div>
                    </div>
                    <small class="text-muted text-nowrap" style="font-size:0.75rem;padding-top:2px;">
                        <?= date('M d, Y', strtotime($a['created_at'])) ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-trophy" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3 mb-1">No recognitions yet.</p>
        <small class="text-muted">Keep up the great work — HR will recognize outstanding performance here.</small>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
