<?php
require('../config/database.php');

class Recruitment{
    private $conn;
    private $table_name = "job_openings";

    public function __construct()
    {
       $db = new Database();
       $this->conn = $db->connect();
    }

    public function createJob($title, $department, $description, $requirements){
        $query = "INSERT INTO ".  $this->table_name . 
        "(title, department, description, requirements) VALUES (:title, :department, :description, :requirements)"; 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':requirements', $requirements);
        return $stmt->execute();
    }

    public function getAllJobs(){
        $query = "SELECT * FROM ". $this->table_name. " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


}

 

$recruitment = new Recruitment();

?>