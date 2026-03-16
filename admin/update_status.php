<?php
require_once '../modules/Applicants.php';

$applicantObj = new Applicants();

$id     = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id && $status) {
    $applicantObj->updateStatus($id, $status);
}

header("Location: applicants.php");
exit;
