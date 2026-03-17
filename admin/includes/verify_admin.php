<?php
if (!class_exists('RBAC')) require_once dirname(__DIR__, 2) . '/modules/RBAC.php';

if (!isset($_SESSION['admin_id'], $_SESSION['admin_role'])) {
    header("Location: ../admin/login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminRole   = $_SESSION['admin_role'];

if (!RBAC::canAccess($adminRole, $currentPage)) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600&display=swap" rel="stylesheet">
        <style>body{font-family:Geist,sans-serif;background:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;}</style>
    </head><body>
        <div class="text-center">
            <div style="font-size:3rem;">🔒</div>
            <h4 class="mt-3 fw-semibold">Access Denied</h4>
            <p class="text-muted">You don\'t have permission to view this page.</p>
            <a href="dashboard.php" class="btn btn-dark btn-sm">Back to Dashboard</a>
        </div>
    </body></html>';
    exit();
}
?>
