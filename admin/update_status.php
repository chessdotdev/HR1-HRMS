<?php
session_start();
require_once '../modules/Applicants.php';
require_once '../modules/AuditLog.php';
$applicantObj = new Applicants();
$audit = new AuditLog();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $id     = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($id && $status) {
        $applicantObj->updateStatus($id, $status);
        if (isset($_SESSION['admin_id'])) {
            $audit->log($_SESSION['admin_id'], $_SESSION['admin_username'], 'Update Applicant Status', 'Recruitment', "Applicant ID {$id} set to {$status}");
        }
    }
}

header("Location: applicants.php");
exit;
