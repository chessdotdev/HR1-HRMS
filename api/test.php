<?php
    
// $Job_openings = [
//     [
//     "test"=> "test"
//     ]
//  ];
// header('Content-Type: application/json');
// $test = json_encode($Job_openings,JSON_PRETTY_PRINT);
// echo $test;

require_once '../modules/Applicants.php';
header("Content-Type: application/json");

$applicantObj = new Applicants();
$status = isset($_GET['status']) ? $_GET['status'] : null;


$applicants = $applicantObj->getApplicants($status);

$response = [
    "success" => true,
    "count" => count($applicants),
    "data" => $applicants
];

echo json_encode($response, JSON_PRETTY_PRINT);

?>