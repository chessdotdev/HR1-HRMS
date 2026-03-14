<?php
require_once '../config/Database.php';

class Employee {
    private $conn;
    private $table_name = 'employees';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Login employee
    public function loginEmployee($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($employee && password_verify($password, $employee['password'])) {
            return $employee;
        }
        return false;
    }

    // Get employee by ID
    public function getEmployeeById($employee_id) {
        $query = "SELECT e.*, 
                a.firstname,
                a.lastname,
                a.middle_name,
                a.suffix,
                a.email,
                a.phone,
                a.job_title,
                a.birthdate,
                a.age,
                a.gender,
                a.skills
                  FROM " . $this->table_name . " e
                  LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
                  WHERE e.employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get onboarding data
    public function getOnboardingData($employee_id) {
        $query = "SELECT * FROM employee_onboarding WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create onboarding record
    public function createOnboardingRecord($employee_id, $applicant_id) {
        $query = "INSERT INTO employee_onboarding (employee_id, applicant_id) VALUES (:employee_id, :applicant_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':applicant_id', $applicant_id);
        return $stmt->execute();
    }

    // Update personal information
    public function updatePersonalInfo($employee_id, $data) {
        $query = "UPDATE employee_onboarding SET 
                  emergency_contact = :emergency_contact,
                  emergency_phone = :emergency_phone,
                  emergency_relationship = :emergency_relationship,
                  tin_number = :tin_number,
                  sss_number = :sss_number,
                  pagibig_number = :pagibig_number,
                  philhealth_number = :philhealth_number,
                  address = :address,
                  city = :city,
                  province = :province,
                  postal_code = :postal_code,
                  bank_name = :bank_name,
                  bank_account_number = :bank_account_number,
                  personal_info_completed = 0,
                  personal_info_status = 'Pending Review',
                  onboarding_status = 'In Progress'
                  WHERE employee_id = :employee_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emergency_contact', $data['emergency_contact']);
        $stmt->bindParam(':emergency_phone', $data['emergency_phone']);
        $stmt->bindParam(':emergency_relationship', $data['emergency_relationship']);
        $stmt->bindParam(':tin_number', $data['tin_number']);
        $stmt->bindParam(':sss_number', $data['sss_number']);
        $stmt->bindParam(':pagibig_number', $data['pagibig_number']);
        $stmt->bindParam(':philhealth_number', $data['philhealth_number']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':province', $data['province']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':bank_name', $data['bank_name']);
        $stmt->bindParam(':bank_account_number', $data['bank_account_number']);
        $stmt->bindParam(':employee_id', $employee_id);
        
        $result = $stmt->execute();
        
        // Update employee onboarding status
        if ($result) {
            $this->updateEmployeeOnboardingStatus($employee_id);
        }
        
        return $result;
    }

    // Update documents
    public function updateDocuments($employee_id, $documents) {
        $query = "UPDATE employee_onboarding SET 
                  government_id_path = :government_id_path,
                  diploma_tor_path = :diploma_tor_path,
                  nbi_clearance_path = :nbi_clearance_path,
                  medical_certificate_path = :medical_certificate_path,
                  documents_submitted = 0,
                  documents_status = 'Pending Review',
                  onboarding_status = 'In Progress'
                  WHERE employee_id = :employee_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':government_id_path', $documents['government_id_path']);
        $stmt->bindParam(':diploma_tor_path', $documents['diploma_tor_path']);
        $stmt->bindParam(':nbi_clearance_path', $documents['nbi_clearance_path']);
        $stmt->bindParam(':medical_certificate_path', $documents['medical_certificate_path']);
        $stmt->bindParam(':employee_id', $employee_id);
        
        $result = $stmt->execute();
        
        // Update employee onboarding status
        if ($result) {
            $this->updateEmployeeOnboardingStatus($employee_id);
        }
        
        return $result;
    }

    // Update orientation status
    public function updateOrientationStatus($employee_id, $day, $status) {
        $query = "UPDATE employee_onboarding SET 
                  orientation_day{$day}_status = :status,
                  onboarding_status = 'In Progress'
                  WHERE employee_id = :employee_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':employee_id', $employee_id);
        
        $result = $stmt->execute();
        
        // Check if all orientation days are completed
        if ($result) {
            $this->checkOrientationCompletion($employee_id);
        }
        
        return $result;
    }

    // Check if orientation is completed
    private function checkOrientationCompletion($employee_id) {
        $onboarding = $this->getOnboardingData($employee_id);
        
        if ($onboarding['orientation_day1_status'] === 'Completed' &&
            $onboarding['orientation_day2_status'] === 'Completed' &&
            $onboarding['orientation_day3_status'] === 'Completed') {
            
            $query = "UPDATE employee_onboarding SET orientation_completed = 1 WHERE employee_id = :employee_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->execute();
            
            $this->updateEmployeeOnboardingStatus($employee_id);
        }
    }

    // Admin: approve personal info
    public function approvePersonalInfo($employee_id) {
        $query = "UPDATE employee_onboarding SET personal_info_completed = 1, personal_info_status = 'Approved' WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $result = $stmt->execute();
        if ($result) $this->updateEmployeeOnboardingStatus($employee_id);
        return $result;
    }

    // Admin: reject personal info
    public function rejectPersonalInfo($employee_id) {
        $query = "UPDATE employee_onboarding SET personal_info_completed = 0, personal_info_status = 'Rejected' WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        return $stmt->execute();
    }

    // Admin: approve documents
    public function approveDocuments($employee_id) {
        $query = "UPDATE employee_onboarding SET documents_submitted = 1, documents_status = 'Approved' WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $result = $stmt->execute();
        if ($result) $this->updateEmployeeOnboardingStatus($employee_id);
        return $result;
    }

    // Admin: reject documents
    public function rejectDocuments($employee_id) {
        $query = "UPDATE employee_onboarding SET documents_submitted = 0, documents_status = 'Rejected' WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        return $stmt->execute();
    }

    // Update employee onboarding status
    private function updateEmployeeOnboardingStatus($employee_id) {
        $onboarding = $this->getOnboardingData($employee_id);
        
        // Check if all sections are completed
        if ($onboarding['personal_info_completed'] == 1 &&
            $onboarding['documents_submitted'] == 1 &&
            $onboarding['orientation_completed'] == 1) {
            
            // Mark onboarding as completed
            $query = "UPDATE employee_onboarding SET 
                      onboarding_status = 'Completed',
                      onboarding_completion_date = NOW()
                      WHERE employee_id = :employee_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->execute();
            
            // Update employee status to Active
            $query2 = "UPDATE employees SET 
                       employment_status = 'Active',
                       onboarding_status = 'Completed'
                       WHERE employee_id = :employee_id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':employee_id', $employee_id);
            $stmt2->execute();
        } else {
            // Update employee onboarding status to In Progress
            $query = "UPDATE employees SET onboarding_status = 'In Progress' WHERE employee_id = :employee_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->execute();
        }
    }

    // Calculate onboarding progress percentage
    public function getOnboardingProgress($employee_id) {
        $onboarding = $this->getOnboardingData($employee_id);
        
        if (!$onboarding) {
            return 0;
        }
        
        $completed = 0;
        $total = 3; // 3 main sections
        
        if (($onboarding['personal_info_status'] ?? '') === 'Approved') $completed++;
        if (($onboarding['documents_status'] ?? '') === 'Approved') $completed++;
        if ($onboarding['orientation_completed'] == 1) $completed++;
        
        return round(($completed / $total) * 100);
    }

}

?>