<?php
session_start();
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';

$audit   = new AuditLog();
$module  = $_GET['module'] ?? '';
$aid     = (int)($_GET['admin_id'] ?? 0);
$logs    = $audit->getLogs(200, $module, $aid);
$modules = $audit->getModules();
$admins  = $audit->getAdmins();

$actionColors = [
    'Update'   => 'primary',
    'Change'   => 'warning',
    'Delete'   => 'danger',
    'Create'   => 'success',
    'Finalize' => 'dark',
    'Login'    => 'info',
];

function logBadgeColor(string $action): string {
    global $actionColors;
    foreach ($actionColors as $key => $color) {
        if (stripos($action, $key) !== false) return $color;
    }
    return 'secondary';
}
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Audit Logs</h2>
            <p class="text-muted mb-0">Track all admin actions across the system</p>
        </div>
        <span class="badge bg-dark fs-6"><?= count($logs) ?> entries</span>
    </div>

    <!-- Filters -->
    <form method="GET" class="d-flex gap-2 flex-wrap mb-4">
        <select name="module" class="form-select form-select-sm" style="width:auto;">
            <option value="">All Modules</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?= htmlspecialchars($m) ?>" <?= $module === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="admin_id" class="form-select form-select-sm" style="width:auto;">
            <option value="">All Admins</option>
            <?php foreach ($admins as $a): ?>
                <option value="<?= $a['admin_id'] ?>" <?= $aid === (int)$a['admin_id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['admin_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-dark">Filter</button>
        <?php if ($module || $aid): ?>
            <a href="audit_logs.php" class="btn btn-sm btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Logs Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width:160px;">Date & Time</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-muted" style="white-space:nowrap;"><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($log['admin_name']) ?></strong></td>
                        <td><span class="badge bg-<?= logBadgeColor($log['action']) ?>"><?= htmlspecialchars($log['action']) ?></span></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($log['module']) ?></span></td>
                        <td class="text-muted"><?= htmlspecialchars($log['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
