<?php

$jobs = [
    "requirements" => [
        "Good communication skills",
        "At least 2 years experience",
        "Can work under pressure",
        "Flexible schedule",
        "Team player"
    ]
];

$reqs = array_filter(array_map('trim', $jobs['requirements']));

foreach (array_slice($reqs, 0, 4) as $req) {
    echo '<li>' . htmlspecialchars($req) . '</li>';
}

$job = [
    "requirements" => "Good communication skills
At least 2 years experience
Can work under pressure
Flexible schedule
Team player"
];


$reqs = explode("\n", $job['requirements']);

foreach (array_slice($reqs, 0, 4) as $req) {
    if (trim($req)) {
        echo '<li>' . htmlspecialchars(trim($req)) . '</li>';
    }
}



$names = [" Alice ", " Bob ", "   Charlie   "];

// Remove spaces from start/end of each name
$cleanNames = array_map('trim', $names);

print_r($cleanNames);


$values = ["Apple", "", "Banana", " ", "Cherry"];

$nonEmpty = array_filter($values);

print_r($nonEmpty);


$requirements = [
    " Good communication skills ",
    "  ",
    "At least 2 years experience",
    "",
    "Can work under pressure"
];

// 1. Trim all lines
$trimmed = array_map('trim', $requirements);

// 2. Remove empty lines
$clean = array_filter($trimmed);
//for api 
// header('Content-Type: application/json');
// $test = json_encode($Job_openings,JSON_PRETTY_PRINT);
// echo $test;
?>