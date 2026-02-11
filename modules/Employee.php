<?php
require_once '../config/Database.php';

class Employee {
    private $conn;
    private $table_name = '';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }



}


?>