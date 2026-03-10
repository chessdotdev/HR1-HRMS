<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

$id = $_POST['id'];
$status = $_POST['status'];

$applicantObj->updateStatus($id,$status);

header("Location: view_applicant.php?id=".$id);
exit;
