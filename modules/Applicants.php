<?php
require_once '../config/Database.php';

class Applicants {
    private $conn;
    private $table_name = "applicantss";

    public function __construct()
    {
      $db = new Database();
      $this->conn = $db->connect();
    }

    public function applyJob(
        $applicant_id,
        $firstname,
        $lastname,
        $middle_name,
        $suffix,
        $birthdate,
        $age,
        $phone,
        $gender,
        $email,
        $skills
    ){
        //check if applicant already submitted
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE applicant_id = :applicant_id";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetchColumn() > 0){
            return ['success' => false, 'message' => 'You have already applied.'];
        }
    
        $insertQuery = "INSERT INTO " . $this->table_name . " 
            (applicant_id, firstname, lastname, middle_name, suffix, birthdate, age, phone, gender, email, skills)
            VALUES 
            (:applicant_id, :firstname, :lastname, :middle_name, :suffix, :birthdate, :age, :phone, :gender, :email, :skills)";
    
        $stmt = $this->conn->prepare($insertQuery);
    
        $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':suffix', $suffix);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':skills', $skills);
    
        // Execute
        if($stmt->execute()){
            return ['success' => true, 'message' => 'Application submitted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to submit application.'];
        }
    }
    
    
}