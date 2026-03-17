<?php
require_once '../config/Database.php';

class Performance {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    //Probation Reviews
    public function createReview($employee_id, $start, $end) {
        $stmt = $this->conn->prepare("INSERT INTO probation_reviews (employee_id, probation_start, probation_end) VALUES (:eid, :start, :end)");
        $stmt->execute([':eid' => $employee_id, ':start' => $start, ':end' => $end]);
        return $this->conn->lastInsertId();
    }

    public function getReviewByEmployee($employee_id) {
        $stmt = $this->conn->prepare("SELECT * FROM probation_reviews WHERE employee_id = :eid");
        $stmt->execute([':eid' => $employee_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getReviewById($review_id) {
        $stmt = $this->conn->prepare("SELECT pr.*, e.employee_id,
            a.firstname, a.lastname, a.job_title
            FROM probation_reviews pr
            JOIN employees e ON pr.employee_id = e.employee_id
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            WHERE pr.review_id = :id");
        $stmt->execute([':id' => $review_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllReviews() {
        $stmt = $this->conn->query("SELECT pr.*, a.firstname, a.lastname, a.job_title,
            (SELECT COUNT(*) FROM probation_goals WHERE review_id = pr.review_id) as goal_count,
            (SELECT COUNT(*) FROM probation_goals WHERE review_id = pr.review_id AND status = 'Achieved') as achieved_count,
            (SELECT COUNT(*) FROM probation_feedback WHERE review_id = pr.review_id) as feedback_count
            FROM probation_reviews pr
            JOIN employees e ON pr.employee_id = e.employee_id
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            ORDER BY pr.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function finalizeReview($review_id, $status, $remarks, $admin_id) {
        $stmt = $this->conn->prepare("UPDATE probation_reviews SET status=:status, final_remarks=:remarks, reviewed_by=:admin, reviewed_at=NOW() WHERE review_id=:id");
        return $stmt->execute([':status' => $status, ':remarks' => $remarks, ':admin' => $admin_id, ':id' => $review_id]);
    }

    public function extendReview($review_id, $new_end) {
        $stmt = $this->conn->prepare("UPDATE probation_reviews SET status='Ongoing', probation_start=CURDATE(), probation_end=:end, final_remarks=NULL, reviewed_by=NULL, reviewed_at=NULL WHERE review_id=:id");
        return $stmt->execute([':end' => $new_end, ':id' => $review_id]);
    }

    // Goals 

    public function addGoal($review_id, $title, $description, $target_date) {
        $stmt = $this->conn->prepare("INSERT INTO probation_goals (review_id, goal_title, description, target_date) VALUES (:rid, :title, :desc, :date)");
        return $stmt->execute([':rid' => $review_id, ':title' => $title, ':desc' => $description, ':date' => $target_date ?: null]);
    }

    public function updateGoalStatus($goal_id, $status) {
        $stmt = $this->conn->prepare("UPDATE probation_goals SET status=:status WHERE goal_id=:id");
        return $stmt->execute([':status' => $status, ':id' => $goal_id]);
    }

    public function deleteGoal($goal_id) {
        $stmt = $this->conn->prepare("DELETE FROM probation_goals WHERE goal_id=:id");
        return $stmt->execute([':id' => $goal_id]);
    }

    public function getGoals($review_id) {
        $stmt = $this->conn->prepare("SELECT * FROM probation_goals WHERE review_id=:rid ORDER BY goal_id");
        $stmt->execute([':rid' => $review_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Feedback
    public function addFeedback($review_id, $date, $strengths, $improvements, $admin_id) {
        $stmt = $this->conn->prepare("INSERT INTO probation_feedback (review_id, feedback_date, strengths, improvements, given_by) VALUES (:rid, :date, :str, :imp, :admin)");
        return $stmt->execute([':rid' => $review_id, ':date' => $date, ':str' => $strengths, ':imp' => $improvements, ':admin' => $admin_id]);
    }

    public function getFeedback($review_id) {
        $stmt = $this->conn->prepare("SELECT * FROM probation_feedback WHERE review_id=:rid ORDER BY feedback_date DESC");
        $stmt->execute([':rid' => $review_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFeedback($feedback_id) {
        $stmt = $this->conn->prepare("DELETE FROM probation_feedback WHERE feedback_id=:id");
        return $stmt->execute([':id' => $feedback_id]);
    }

    // Ratings 
    public function saveRatings($review_id, $ratings) {
        $this->conn->prepare("DELETE FROM probation_ratings WHERE review_id=:rid")->execute([':rid' => $review_id]);
        $stmt = $this->conn->prepare("INSERT INTO probation_ratings (review_id, category, score) VALUES (:rid, :cat, :score)");
        foreach ($ratings as $category => $score) {
            $stmt->execute([':rid' => $review_id, ':cat' => $category, ':score' => (int)$score]);
        }
    }

    public function getRatings($review_id) {
        $stmt = $this->conn->prepare("SELECT * FROM probation_ratings WHERE review_id=:rid");
        $stmt->execute([':rid' => $review_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($review_id) {
        $stmt = $this->conn->prepare("SELECT AVG(score) FROM probation_ratings WHERE review_id=:rid");
        $stmt->execute([
            ':rid' => $review_id
        ]);
        $val = $stmt->fetchColumn();
        return $val !== null ? round((float)$val, 1) : null;
    }

    // Helpers 

    public function getActiveEmployees() {
        $stmt = $this->conn->query("SELECT e.employee_id, a.firstname, a.lastname, a.job_title, e.hired_at
            FROM employees e
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            WHERE e.employment_status = 'Active'
            ORDER BY a.lastname");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingLabel($avg) {
        if ($avg >= 4.5) return ['label' => 'Outstanding',          'color' => 'success'];
        if ($avg >= 3.5) return ['label' => 'Exceeds Expectations', 'color' => 'primary'];
        if ($avg >= 2.5) return ['label' => 'Meets Expectations',   'color' => 'info'];
        if ($avg >= 1.5) return ['label' => 'Needs Improvement',    'color' => 'warning'];
        return                  ['label' => 'Unsatisfactory',       'color' => 'danger'];
    }

    public static function ratingCategories() {
        return ['Work Quality', 'Attendance & Punctuality', 'Teamwork & Collaboration', 'Communication', 'Initiative & Attitude'];
    }
}
?>
