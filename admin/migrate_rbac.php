<?php
require_once '../config/Database.php';
$db   = new Database();
$conn = $db->connect();

$steps = [];

// 1. Alter role column to ENUM
try {
    $conn->exec("ALTER TABLE `admin` MODIFY `role` ENUM('super_admin','hr_manager','recruiter') NOT NULL DEFAULT 'recruiter'");
    $steps[] = ['ok', 'Altered admin.role to ENUM(super_admin, hr_manager, recruiter)'];
} catch (PDOException $e) {
    $steps[] = ['err', 'Alter role column: ' . $e->getMessage()];
}

// 2. Migrate old 'admin' role values to 'super_admin'
try {
    $affected = $conn->exec("UPDATE `admin` SET `role` = 'super_admin' WHERE `role` NOT IN ('super_admin','hr_manager','recruiter')");
    $steps[] = ['ok', "Migrated {$affected} existing account(s) to super_admin"];
} catch (PDOException $e) {
    $steps[] = ['err', 'Migrate roles: ' . $e->getMessage()];
}

?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600&display=swap" rel="stylesheet">
    <style>body{font-family:Geist,sans-serif;padding:2rem;background:#fff;}</style>
</head>
<body>
<div style="max-width:520px;margin:auto;">
    <h4 class="fw-semibold mb-4">RBAC Migration</h4>
    <?php foreach ($steps as [$status, $msg]): ?>
        <div class="alert alert-<?= $status === 'ok' ? 'success' : 'danger' ?> py-2">
            <?= $status === 'ok' ? '✓' : '✗' ?> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endforeach; ?>
    <a href="../admin/roles.php" class="btn btn-dark mt-2">Go to Roles & Access →</a>
    <p class="text-muted small mt-3">Delete this file after running.</p>
</div>
</body>
</html>
