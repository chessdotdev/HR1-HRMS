<?php
session_start();

if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin') {
    $redirect = 'admin/login.php';
} else {
    $redirect = 'public/login.php';
}

// Clear all session data
$_SESSION = [];
session_destroy();

header("Location: $redirect");
exit();
?>
