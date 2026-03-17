<?php
session_start();
require_once '../modules/Applicants.php';
require_once '../modules/AuditLog.php';
$applicantObj = new Applicants();
$audit = new AuditLog();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $interview_id = $_POST['interview_id'];
    $result = $_POST['result'];

    if (!$interview_id || !in_array($result, ['Passed', 'Failed'])) {
        header("Location: interviews.php");
        exit;
    }

    $success = $applicantObj->updateInterviewResult($interview_id, $result);

    if($success){
        if (isset($_SESSION['admin_id'])) {
            $audit->log($_SESSION['admin_id'], $_SESSION['admin_username'], 'Interview Result', 'Recruitment', "Interview ID {$interview_id} marked as {$result}");
        }
        header("Location: interviews.php"); 
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to update interview result.</div>";
    }
}

?>