<?php
require_once '../modules/Recruitment.php';

$recruitment = new Recruitment();

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $title = $_POST['title'];
    $department = $_POST['department'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];

    $result = $recruitment->createJob($title, $department, $description, $requirements);

    if ($result) {
        echo "Job posted successfully!";
    } else {
        echo "Failed to create job.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Job Posting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Add New Job Posting</h2>

    <form action="job.php" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" name="department" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Job Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="requirements" class="form-label">Requirements</label>
            <textarea class="form-control" id="requirements" name="requirements" rows="3" required></textarea>
        </div>
   
        <button type="submit" class="btn btn-primary">Create Job</button>
    </form>
</div>
</body>
</html>
