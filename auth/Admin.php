<?php
require_once '../config/Database.php';

class Admin{
    private $conn;
    private $table_name = 'admin';

    public function __construct()
    {
        $db = new Database;
        $this->conn = $db->connect();
    }
    
    public function createAdmin($username, $email, $password, $role = "admin"){
        $query = "INSERT INTO ". $this->table_name. " 
        (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt;
    }

    public function loginAdmin($username, $password){
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
// $admin = new Admin();

// // Create admin account
// if ($admin->createAdmin("admin", "admin@gmail.com", "admin123")) {
//     echo "Admin account created successfully!";
// } else {
//     echo "Failed to create admin.";
// }
?>