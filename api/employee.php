<?php
header("Content-Type: application/json");

$employees = [
    [
        "apply_id" => 1,
        "applicant_id" => 201,
        "job_id" => 1,
        "job_title" => "Front Desk Receptionist",
        "firstname" => "Juan",
        "lastname" => "Dela Cruz",
        "middle_name" => "M",
        "suffix" => "",
        "birthdate" => "1995-05-12",
        "age" => 31,
        "phone" => "09123456789",
        "gender" => "Male",
        "email" => "juan.delacruz@example.com",
        "skills" => "Customer Service, Booking Systems",
        "status" => "Pending",
        "applied_at" => "2026-03-01 10:00:00"
    ],
    [
        "apply_id" => 2,
        "applicant_id" => 202,
        "job_id" => 2,
        "job_title" => "Sous Chef",
        "firstname" => "Maria",
        "lastname" => "Santos",
        "middle_name" => "L",
        "suffix" => "",
        "birthdate" => "1990-08-22",
        "age" => 35,
        "phone" => "09234567890",
        "gender" => "Female",
        "email" => "maria.santos@example.com",
        "skills" => "Food Preparation, Menu Planning",
        "status" => "Hired",
        "applied_at" => "2026-02-25 14:30:00"
    ],
    [
        "apply_id" => 3,
        "applicant_id" => 203,
        "job_id" => 3,
        "job_title" => "Waiter / Waitress",
        "firstname" => "Pedro",
        "lastname" => "Reyes",
        "middle_name" => "C",
        "suffix" => "",
        "birthdate" => "1998-12-10",
        "age" => 27,
        "phone" => "09345678901",
        "gender" => "Male",
        "email" => "pedro.reyes@example.com",
        "skills" => "Serving, Customer Service",
        "status" => "Pending",
        "applied_at" => "2026-03-03 09:15:00"
    ],
];

$employees = array_values($employees);

// API response
$response = [
    "success" => true,
    "count" => count($employees),
    "data" => $employees,
];

echo json_encode($response, JSON_PRETTY_PRINT);

?>