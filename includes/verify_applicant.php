<?php


if (!isset($_SESSION['applicant_role']) || $_SESSION['applicant_role'] !== "applicant") {
    header("Location: ../public/login.php");
    exit();
}
    



?>