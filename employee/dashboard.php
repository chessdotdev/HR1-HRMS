<?php
session_start();
include 'includes/header.php';
require_once '../modules/Recognition.php';

if ($employee['employment_status'] === 'New Hire') {
    header("Location: onboarding.php");
    exit();
}

$recog          = new Recognition();
$emp_id         = $_SESSION['employee_id'];
$myAwards       = $recog->getPostsByNominee($emp_id);
$monthlyBoard   = $recog->getMonthlyLeaderboard(5);
?>

<div class="page-header mb-3">
    <h1 class="page-title">Welcome, <?= htmlspecialchars($employee['firstname']) ?>!</h1>
</div>

<?php if (!empty($myAwards)): ?>
<?php $latest = $myAwards[0]; ?>
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:#fff8e1;border:1px solid #ffc107;border-radius:8px;">
    <span style="font-size:2rem;">🏆</span>
    <div>
        <strong>You've been recognized!</strong>
        <p class="mb-0" style="font-size:0.875rem;">
            HR awarded you <strong><?= htmlspecialchars($latest['award_type']) ?></strong>
            — "<?= htmlspecialchars(substr($latest['message'], 0, 80)) . (strlen($latest['message']) > 80 ? '...' : '') ?>"
        </p>
        <a href="recognition.php#my-awards" class="text-decoration-none" style="font-size:0.8rem;">
            View all my recognitions <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="profile.php" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-person"></i> View Profile
                    </a>
                    <a href="my_performance.php" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-graph-up"></i> Performance
                    </a>
                    <a href="recognition.php" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-trophy"></i> Recognition
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Recognition Leaderboard -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-trophy-fill text-warning me-2"></i>Recognition Leaderboard — <?= date('F Y') ?></span>
        <a href="recognition.php" class="btn btn-sm btn-outline-dark">View All</a>
    </div>
    <?php if (!empty($monthlyBoard) && array_sum(array_column($monthlyBoard, 'total_recognitions')) > 0): ?>
    <div class="card-body p-0">
        <?php foreach ($monthlyBoard as $i => $row): ?>
        <?php
            $rank  = $i + 1;
            $isMe  = $row['employee_id'] == $emp_id;
            $medal = match($rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => "#{$rank}" };
        ?>
        <div class="d-flex align-items-center gap-3 px-3 py-2"
             style="border-bottom:1px solid #f4f4f5;<?= $isMe ? 'background:#fff8e1;' : '' ?>">
            <div style="width:28px;text-align:center;font-size:<?= $rank <= 3 ? '1.2rem' : '0.85rem' ?>;font-weight:700;color:#71717a;">
                <?= $medal ?>
            </div>
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:36px;height:36px;background:<?= $isMe ? '#ffc107' : '#09090b' ?>;color:<?= $isMe ? '#000' : '#fff' ?>;font-weight:600;font-size:0.85rem;">
                <?= strtoupper(substr($row['firstname'] ?? '?', 0, 1)) ?>
            </div>
            <div class="flex-grow-1">
                <strong style="font-size:0.875rem;">
                    <?= htmlspecialchars(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?>
                    <?php if ($isMe): ?>
                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">You</span>
                    <?php endif; ?>
                </strong>
                <br><small class="text-muted"><?= htmlspecialchars($row['job_title'] ?? '') ?></small>
            </div>
            <div class="text-end">
                <span class="badge bg-dark"><?= $row['total_recognitions'] ?></span>
                <br><small class="text-muted" style="font-size:0.72rem;"><?= $row['total_reactions'] ?> <i class="bi bi-heart-fill text-danger"></i></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card-body text-center py-4">
        <i class="bi bi-trophy" style="font-size:2rem;color:#ccc;"></i>
        <p class="text-muted mt-2 mb-0" style="font-size:0.875rem;">No recognitions this month yet. Be outstanding!</p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
