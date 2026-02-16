<?php

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: ../admin/login.php");
    exit();
}

?>
