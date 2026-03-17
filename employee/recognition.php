<?php
session_start();
include 'includes/header.php';
require_once '../modules/Recognition.php';

$recog      = new Recognition();
$emp_id     = $_SESSION['employee_id'];
$filterType = $_GET['type'] ?? '';
$posts      = $filterType ? $recog->getPostsByType($filterType) : $recog->getAllPosts();
$awardTypes = Recognition::awardTypes();
?>

<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Recognition</h1>
        <p class="text-muted" style="font-size:0.875rem;">Celebrating employees who go above and beyond</p>
    </div>
    <a href="recognition_history.php" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-clock-history me-1"></i>My History
    </a>
</div>

<!-- Filter Tabs -->
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="recognition.php" class="btn btn-sm <?= !$filterType ? 'btn-dark' : 'btn-outline-secondary' ?>">All</a>
    <?php foreach ($awardTypes as $type): ?>
        <a href="?type=<?= urlencode($type) ?>" class="btn btn-sm <?= $filterType === $type ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="bi <?= Recognition::awardIcon($type) ?> me-1"></i><?= $type ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Feed -->
<?php if (!empty($posts)): ?>
<div class="row g-3">
    <?php foreach ($posts as $p): ?>
    <?php $reacted = $recog->hasReacted($p['post_id'], $emp_id); ?>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">

                <!-- Award + Date -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-dark">
                        <i class="bi <?= Recognition::awardIcon($p['award_type']) ?> me-1"></i>
                        <?= htmlspecialchars($p['award_type']) ?>
                    </span>
                    <small class="text-muted"><?= date('M d, Y', strtotime($p['created_at'])) ?></small>
                </div>

                <!-- Nominee -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;font-size:1.1rem;flex-shrink:0;font-weight:600;">
                        <?= strtoupper(substr($p['nominee_first'] ?? '?', 0, 1)) ?>
                    </div>
                    <div>
                        <strong style="font-size:0.9rem;">
                            <?= htmlspecialchars(($p['nominee_first'] ?? '') . ' ' . ($p['nominee_last'] ?? '')) ?>
                            <?php if ($p['nominee_employee_id'] == $emp_id): ?>
                                <span class="badge bg-success ms-1" style="font-size:0.65rem;">You</span>
                            <?php endif; ?>
                        </strong>
                        <br><small class="text-muted"><?= htmlspecialchars($p['nominee_job'] ?? '') ?></small>
                    </div>
                </div>

                <!-- Message -->
                <p class="mb-3" style="font-size:0.875rem;color:#52525b;line-height:1.6;">
                    "<?= htmlspecialchars($p['message']) ?>"
                </p>

                <!-- Footer -->
                <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px solid #f4f4f5;">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>Awarded by HR
                    </small>
                    <button class="btn btn-sm <?= $reacted ? 'btn-danger' : 'btn-outline-secondary' ?> reaction-btn"
                            data-post-id="<?= $p['post_id'] ?>"
                            style="font-size:0.8rem;">
                        <i class="bi bi-heart<?= $reacted ? '-fill' : '' ?> me-1"></i>
                        <span class="reaction-count"><?= $p['reaction_count'] ?></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-award" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3 mb-0">No recognitions posted yet.</p>
        <small class="text-muted">Check back soon — HR will highlight outstanding employees here.</small>
    </div>
</div>
<?php endif; ?>

<script>
document.querySelectorAll('.reaction-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const postId = this.dataset.postId;
        const countEl = this.querySelector('.reaction-count');
        const icon = this.querySelector('i');

        fetch('toggle_reaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}`
        })
        .then(r => r.json())
        .then(data => {
            countEl.textContent = data.count;
            if (data.action === 'added') {
                this.classList.replace('btn-outline-secondary', 'btn-danger');
                icon.classList.replace('bi-heart', 'bi-heart-fill');
            } else {
                this.classList.replace('btn-danger', 'btn-outline-secondary');
                icon.classList.replace('bi-heart-fill', 'bi-heart');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
