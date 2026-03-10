<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $interview_id = $_POST['interview_id'];
    $result = $_POST['result'];

    if (!$interview_id || !in_array($result, ['Passed', 'Failed'])) {
        header("Location: interviews.php");
        exit;
    }

    $success = $applicantObj->updateInterviewResult($interview_id, $result);

    if($success){
        header("Location: interviews.php"); 
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to update interview result.</div>";
    }
}

?>