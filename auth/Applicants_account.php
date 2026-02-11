<?php
require('../config/database.php');

class Applicants_account{
    private $conn;
    private $table_name = 'applicants_account';

    public function __construct()
    {
        $db = new Database;
        $this->conn = $db->connect();
    }

    public function createApplicant($username, $email, $password){
        $query = "INSERT INTO ". $this->table_name. " 
        (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        return $stmt;
    }

    public function loginApplicant($username, $password){
        $query = "SELECT * FROM ". $this->table_name. " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            return $user;
        }

    }

}

?>