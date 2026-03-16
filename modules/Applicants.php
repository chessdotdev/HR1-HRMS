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
        $job_id,
        $job_title,
        $firstname,
        $lastname,
        $middle_name,
        $suffix,
        $birthdate,
        $age,
        $phone,
        $gender,
        $civil_status,
        $city,
        $province,
        $nationality,
        $email,
        $skills,
        $resume_path = null
    ){
        //check if applicant already submitted
        $checkQuery = "SELECT status FROM ". $this->table_name. " WHERE applicant_id = :applicant_id ORDER BY applied_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
        $stmt->execute();
        $existingStatus = $stmt->fetch(PDO::FETCH_ASSOC);


        if($existingStatus){
            if ($existingStatus['status'] === 'Pending') {
                return ['success' => false, 'message' => 'You already have a pending application.'];
            }
            if ($existingStatus['status'] === 'Interview') {
                return ['success' => false, 'message' => 'You already have a scheduled interview.'];
            }
            if ($existingStatus['status'] === 'Hired') {
                return ['success' => false, 'message' => 'You are already a hired employee.'];
            }
        }
    
        $insertQuery = "INSERT INTO " . $this->table_name . " 
            (applicant_id, job_id, job_title, firstname, lastname, middle_name, suffix, birthdate, age, phone, gender, civil_status, city, province, nationality, email, skills, resume_path)
            VALUES 
            (:applicant_id, :job_id, :job_title, :firstname, :lastname, :middle_name, :suffix, :birthdate, :age, :phone, :gender, :civil_status, :city, :province, :nationality, :email, :skills, :resume_path)";
    
        $stmt = $this->conn->prepare($insertQuery);
    
        $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
        $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':suffix', $suffix);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':civil_status', $civil_status);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':skills', $skills);
        $stmt->bindParam(':resume_path', $resume_path);
    
        // Execute
        if($stmt->execute()){
            return ['success' => true, 'message' => 'Application submitted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to submit application.'];
        }
    }
    //Get all applicants
    public function getApplicants($status = null){
        if($status){
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE status=:status ORDER BY applied_at DESC");
            $stmt->bindParam(':status',$status);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} ORDER BY applied_at DESC");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Get single applicant (Pending only — for view)
    public function getApplicant($id){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE apply_id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Internal: get applicant regardless of status (for emails)
    private function getApplicantById($id){
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE apply_id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function updateStatus($id,$status){
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET status=:status WHERE apply_id=:id");
        $stmt->bindParam(':status',$status);
        $stmt->bindParam(':id',$id);
        $success = $stmt->execute();

        // If rejected, send rejection email
        if($success && $status==='Rejected'){
            $this->sendRejectionNotification($id);
        }

        return $success;
    }

    private function sendRejectionNotification($applicant_id){
        $applicant = $this->getApplicantById($applicant_id);
        
        if($applicant && !empty($applicant['email'])){
            require_once '../MailService.php';
            
            $to = $applicant['email'];
            $name = $applicant['firstname'] . ' ' . $applicant['lastname'];
            $subject = 'Application Status Update - Hotel & Restaurant HR';
            
            $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .message { margin: 20px 0; padding: 15px; background-color: #fff; border-left: 4px solid #dc3545; }
                    .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Application Update</h2>
                    </div>
                    <div class='content'>   
                        <p>Dear <strong>$name</strong>,</p>
                        <p>Thank you for your interest in joining our team at Hotel & Restaurant.</p>
                        <div class='message'>
                            <p><strong>After careful consideration, we regret to inform you that we have decided to move forward with other candidates for this position.</strong></p>
                        </div>
                        <p>We encourage you to apply for future openings that match your skills and experience.</p>
                        <p>We wish you the best in your career journey.</p>
                        <p>Best regards,<br>Human Resource Department<br>Hotel & Restaurant</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated email. Please do not reply to this message.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            MailService::send($to, $subject, $body, true);
        }
    }

     private function createEmployeeAccount($applicant_id){
        // get applicant info
        $app = $this->getApplicant($applicant_id);
        $password = bin2hex(random_bytes(4));
        $hashed = password_hash($password,PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO employees (applicant_id,email,password)
            VALUES (:applicant_id,:email,:password)
        ");
        $stmt->bindParam(':applicant_id',$applicant_id);
        $stmt->bindParam(':email',$app['email']);
        $stmt->bindParam(':password',$hashed);
        $stmt->execute();

        // send email
        mail(
            $app['email'],
            "Employee Account Created",
            "Your account has been created.\nEmail: {$app['email']}\nPassword: {$password}"
        );
    }

    public function scheduleInterview($applicant_id, $date, $time, $type){
        $stmt = $this->conn->prepare("
            INSERT INTO interviews (applicant_id, date, time, type)
            VALUES (:applicant_id, :date, :time, :type)
        ");
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':type', $type);
    
        $result = $stmt->execute();
        
        // Send email notification to applicant
        if($result){
            // Update applicant status to Interview
            $this->updateStatus($applicant_id, 'Interview');
            
            // Send email notification
            $this->sendInterviewNotification($applicant_id, $date, $time, $type);
        }
        
        return $result;
    }
    
    private function sendInterviewNotification($applicant_id, $date, $time, $type){
        $applicant = $this->getApplicantById($applicant_id);
        
        if($applicant && !empty($applicant['email'])){
            require_once '../MailService.php';
            
            $to = $applicant['email'];
            $name = $applicant['firstname'] . ' ' . $applicant['lastname'];
            $subject = 'Interview Schedule Notification - Hotel & Restaurant HR';
            
            $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4a90e2; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .details { margin: 20px 0; padding: 15px; background-color: #fff; border-left: 4px solid #4a90e2; }
                    .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Interview Scheduled</h2>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>$name</strong>,</p>
                        <p>We are pleased to inform you that your interview has been scheduled. Please find the details below:</p>
                        <div class='details'>
                            <p><strong>Date:</strong> $date</p>
                            <p><strong>Time:</strong> $time</p>
                            <p><strong>Interview Type:</strong> $type</p>
                        </div>
                        <p>Please arrive 15 minutes before the scheduled time and bring all required documents.</p>
                        <p>If you have any questions or need to reschedule, please contact us.</p>
                        <p>Best regards,<br>Human Resource Department<br>Hotel & Restaurant</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated email. Please do not reply to this message.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            MailService::send($to, $subject, $body, true);
        }
    }
    
    public function updateInterviewResult($interview_id, $result){
        // Get interview details first
        $stmt = $this->conn->prepare("SELECT * FROM interviews WHERE interview_id = :interview_id");
        $stmt->bindParam(':interview_id', $interview_id, PDO::PARAM_INT);
        $stmt->execute();
        $interview = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update interview result
        $stmt = $this->conn->prepare("
            UPDATE interviews 
            SET result = :result 
            WHERE interview_id = :interview_id
        ");
        $stmt->bindParam(':result', $result);
        $stmt->bindParam(':interview_id', $interview_id, PDO::PARAM_INT);
    
        $success = $stmt->execute();
        
        // Send email notification based on result
        if($success && $interview){
            if($result === 'Passed'){
                // Create employee account AND send hiring email with credentials
                $this->createEmployeeAccountWithEmail($interview['applicant_id']);
            } elseif($result === 'Failed'){
                $this->updateStatus($interview['applicant_id'], 'Rejected');
            }
        }
        
        return $success; 
    }
    
    private function createEmployeeAccountWithEmail($applicant_id){
        $app = $this->getApplicantById($applicant_id);
        if(!$app) return;

        $year     = date('Y');
        $lastname = strtolower(preg_replace('/\s+/', '', $app['lastname']));
        $username = $lastname . $year . $app['apply_id'];

        $password = bin2hex(random_bytes(4));
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO employees (applicant_id, username, password)
            VALUES (:applicant_id, :username, :password)
        ");
        $stmt->bindParam(':applicant_id', $app['apply_id']);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed);
        $stmt->execute();

        // Update applicant status to Hired
        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET status='Hired' WHERE apply_id=:id");
        $stmt->bindParam(':id', $applicant_id);
        $stmt->execute();

        // Send hiring email with login credentials
        $this->sendHiringNotification($app['email'], $app['firstname'] . ' ' . $app['lastname'], $username, $password);
    }
    
    private function sendHiringNotification($email, $name, $username, $password){
        if($email && !empty($email)){
            require_once '../MailService.php';
            
            $to = $email;
            $subject = 'Congratulations! You Have Been Hired - Hotel & Restaurant HR';
            
            $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .credentials { margin: 20px 0; padding: 15px; background-color: #fff; border-left: 4px solid #28a745; }
                    .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Welcome to Hotel & Restaurant!</h2>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>$name</strong>,</p>
                        <p>We are delighted to inform you that you have been <strong>HIRED</strong>!</p>
                        <div class='credentials'>
                            <p><strong>Your Employee Portal Account:</strong></p>
                            <p>Email: <strong>$username</strong></p>
                            <p>Temporary Password: <strong>$password</strong></p>
                        </div>
                        <p>Please log in to the employee portal to complete your onboarding tasks and review important documents.</p>
                        <p>We will be sending you more details about your onboarding process and first day soon.</p>
                        <p>We are excited to have you join our team!</p>
                        <p>Best regards,<br>Human Resource Department<br>Hotel & Restaurant</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated email. Please do not reply to this message.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            MailService::send($to, $subject, $body, true);
        }
    }
    
    
}