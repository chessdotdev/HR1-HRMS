<?php
session_start();
require_once '../modules/Applicants.php';
require_once '../modules/AuditLog.php';
$applicantObj = new Applicants();
$audit = new AuditLog();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $applicant_id    = $_POST['applicant_id'];
    $date            = $_POST['date'];
    $time            = $_POST['time'];
    $type            = $_POST['type'];

    if($applicantObj->scheduleInterview($applicant_id, $date, $time, $type)){
        if (isset($_SESSION['admin_id'])) {
            $audit->log($_SESSION['admin_id'], $_SESSION['admin_username'], 'Schedule Interview', 'Recruitment', "Scheduled {$type} interview for applicant ID {$applicant_id} on {$date} {$time}");
        }
        echo json_encode(['success' => true, 'message' => 'Interview scheduled and email notification sent!']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to schedule interview.']);
        exit;
    }
}
