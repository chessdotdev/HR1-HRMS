<?php
require_once '../modules/Recognition.php';
$recog = new Recognition();

$leaderboard = $recog->getLeaderboard();
$stats       = $recog->getStats();
$top3        = array_slice($leaderboard, 0, 3);
$rest        = array_slice($leaderboard, 3);
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Leaderboard</h2>
            <p class="text-muted mb-0">Most recognized employees this period</p>
        </div>
        <a href="points_rewards.php" class="btn btn-dark"><i class="bi bi-award me-1"></i>Give Recognition</a>
    </div>

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

    <?php $hasData = !empty($leaderboard) && array_sum(array_column($leaderboard, 'total_recognitions')) > 0; ?>

    <?php if ($hasData): ?>

    <!-- Podium -->
    <div class="card mb-4">
        <div class="card-header bg-white"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Performers</div>
        <div class="card-body py-4">
            <div class="row justify-content-center align-items-end g-3">

                <!-- 2nd -->
                <?php if (isset($top3[1])): ?>
                <div class="col-md-3 text-center order-1">
                    <div class="p-3 rounded" style="background:#f8f9fa;border:2px solid #dee2e6;">
                        <div class="rounded-circle bg-secondary text-white mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;font-size:1.4rem;">
                            <?= strtoupper(substr($top3[1]['firstname'] ?? '?', 0, 1)) ?>
                        </div>
                        <span style="font-size:2rem;">🥈</span>
                        <h6 class="mb-0 mt-1"><?= htmlspecialchars(($top3[1]['firstname'] ?? '') . ' ' . ($top3[1]['lastname'] ?? '')) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($top3[1]['job_title'] ?? '') ?></small>
                        <div class="mt-2">
                            <span class="badge bg-secondary"><?= $top3[1]['total_recognitions'] ?> recognitions</span>
                            <br><small class="text-muted"><?= $top3[1]['total_reactions'] ?? 0 ?> reactions</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 1st -->
                <div class="col-md-3 text-center order-0">
                    <div class="p-3 rounded" style="background:#fff8e1;border:2px solid #ffc107;">
                        <div class="rounded-circle bg-warning text-dark mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width:64px;height:64px;font-size:1.6rem;font-weight:700;">
                            <?= strtoupper(substr($top3[0]['firstname'] ?? '?', 0, 1)) ?>
                        </div>
                        <span style="font-size:2.5rem;">🥇</span>
                        <h5 class="mb-0 mt-1"><?= htmlspecialchars(($top3[0]['firstname'] ?? '') . ' ' . ($top3[0]['lastname'] ?? '')) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($top3[0]['job_title'] ?? '') ?></small>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark"><?= $top3[0]['total_recognitions'] ?> recognitions</span>
                            <br><small class="text-muted"><?= $top3[0]['total_reactions'] ?? 0 ?> reactions</small>
                        </div>
                    </div>
                </div>

                <!-- 3rd -->
                <?php if (isset($top3[2])): ?>
                <div class="col-md-3 text-center order-2">
                    <div class="p-3 rounded" style="background:#f8f9fa;border:2px solid #dee2e6;">
                        <div class="rounded-circle bg-secondary text-white mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;font-size:1.4rem;">
                            <?= strtoupper(substr($top3[2]['firstname'] ?? '?', 0, 1)) ?>
                        </div>
                        <span style="font-size:2rem;">🥉</span>
                        <h6 class="mb-0 mt-1"><?= htmlspecialchars(($top3[2]['firstname'] ?? '') . ' ' . ($top3[2]['lastname'] ?? '')) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($top3[2]['job_title'] ?? '') ?></small>
                        <div class="mt-2">
                            <span class="badge bg-secondary"><?= $top3[2]['total_recognitions'] ?> recognitions</span>
                            <br><small class="text-muted"><?= $top3[2]['total_reactions'] ?? 0 ?> reactions</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Full Rankings -->
    <div class="card">
        <div class="card-header bg-white">Full Rankings</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">Rank</th>
                            <th>Employee</th>
                            <th>Job Title</th>
                            <th>Recognitions</th>
                            <th>Reactions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $i => $emp): ?>
                        <?php
                            $rank = $i + 1;
                            $medal = match($rank) {
                                1 => '🥇', 2 => '🥈', 3 => '🥉',
                                default => "#{$rank}"
                            };
                        ?>
                        <tr <?= $rank <= 3 ? 'class="table-warning"' : '' ?>>
                            <td class="text-center fw-bold"><?= $medal ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center"
                                         style="width:34px;height:34px;font-size:0.85rem;flex-shrink:0;">
                                        <?= strtoupper(substr($emp['firstname'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars(($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($emp['job_title'] ?? '') ?></small></td>
                            <td>
                                <span class="badge bg-dark"><?= $emp['total_recognitions'] ?? 0 ?></span>
                            </td>
                            <td>
                                <span class="text-muted"><i class="bi bi-heart me-1"></i><?= $emp['total_reactions'] ?? 0 ?></span>
                            </td>
                            <td>
                                <a href="points_rewards.php" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-award"></i> Recognize
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-trophy" style="font-size:3rem;color:#ccc;"></i>
        <p class="text-muted mt-3">No recognitions yet. Start celebrating your team!</p>
        <a href="points_rewards.php" class="btn btn-dark">Give First Recognition</a>
    </div></div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
