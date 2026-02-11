<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'test');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    class Database {
        private $host = DB_HOST;
        private $db_name = DB_NAME;
        private $username = DB_USER;
        private $password = DB_PASS;
        public $conn;
        


        public function connect(){
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=". $this->host.
                                      ";dbname=". $this->db_name ,
                                                 $this->username,
                                                 $this->password
                                    );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

              
                // echo "connected";
              
                return $this->conn;
            } catch (PDOException $e) {
                //throw 
                echo "Connection error: " . $e->getMessage();

            }
        }

    }

    
    // $db = new Database();     // create instance
    // $conn = $db->connect();
?>