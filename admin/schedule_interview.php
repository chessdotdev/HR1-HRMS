<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $applicant_id = $_POST['applicant_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $type = $_POST['type'];

    if($applicantObj->scheduleInterview($applicant_id, $date, $time, $type)){
        echo json_encode(['success' => true, 'message' => 'Interview scheduled and email notification sent!']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to schedule interview.']);
        exit;
    }
}
