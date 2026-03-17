<?php
require_once '../config/Database.php';

class Recognition {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // ── Posts ──────────────────────────────────────────────────

    public function createPost($nominee_id, $award_type, $message, $admin_id) {
        $stmt = $this->conn->prepare("INSERT INTO recognition_posts (nominee_employee_id, nominated_by_admin_id, award_type, message)
            VALUES (:nominee, :admin, :type, :msg)");
        return $stmt->execute([
            ':nominee' => $nominee_id,
            ':admin'   => $admin_id,
            ':type'    => $award_type,
            ':msg'     => $message
        ]);
    }

    public function deletePost($post_id) {
        $stmt = $this->conn->prepare("DELETE FROM recognition_posts WHERE post_id=:id");
        return $stmt->execute([':id' => $post_id]);
    }

    public function getAllPosts() {
        $stmt = $this->conn->query("SELECT rp.*,
            an.firstname as nominee_first, an.lastname as nominee_last, an.job_title as nominee_job,
            ap.firstname as nominator_first, ap.lastname as nominator_last,
            (SELECT COUNT(*) FROM recognition_reactions rr WHERE rr.post_id = rp.post_id) as reaction_count
            FROM recognition_posts rp
            LEFT JOIN employees en ON rp.nominee_employee_id = en.employee_id
            LEFT JOIN applicantss an ON en.applicant_id = an.apply_id
            LEFT JOIN employees ep ON rp.nominated_by_employee_id = ep.employee_id
            LEFT JOIN applicantss ap ON ep.applicant_id = ap.apply_id
            ORDER BY rp.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostsByNominee($employee_id) {
        $stmt = $this->conn->prepare("SELECT rp.*,
            an.firstname as nominee_first, an.lastname as nominee_last, an.job_title as nominee_job,
            (SELECT COUNT(*) FROM recognition_reactions rr WHERE rr.post_id = rp.post_id) as reaction_count
            FROM recognition_posts rp
            JOIN employees en ON rp.nominee_employee_id = en.employee_id
            LEFT JOIN applicantss an ON en.applicant_id = an.apply_id
            WHERE rp.nominee_employee_id = :eid
            ORDER BY rp.created_at DESC");
        $stmt->execute([':eid' => $employee_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostsByType($type) {
        $stmt = $this->conn->prepare("SELECT rp.*,
            an.firstname as nominee_first, an.lastname as nominee_last, an.job_title as nominee_job,
            (SELECT COUNT(*) FROM recognition_reactions rr WHERE rr.post_id = rp.post_id) as reaction_count
            FROM recognition_posts rp
            JOIN employees en ON rp.nominee_employee_id = en.employee_id
            LEFT JOIN applicantss an ON en.applicant_id = an.apply_id
            WHERE rp.award_type = :type
            ORDER BY rp.created_at DESC");
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Reactions ──────────────────────────────────────────────

    public function toggleReaction($post_id, $employee_id) {
        $check = $this->conn->prepare("SELECT reaction_id FROM recognition_reactions WHERE post_id=:pid AND employee_id=:eid");
        $check->execute([':pid' => $post_id, ':eid' => $employee_id]);
        if ($check->fetch()) {
            $this->conn->prepare("DELETE FROM recognition_reactions WHERE post_id=:pid AND employee_id=:eid")
                ->execute([':pid' => $post_id, ':eid' => $employee_id]);
            return 'removed';
        }
        $this->conn->prepare("INSERT INTO recognition_reactions (post_id, employee_id) VALUES (:pid, :eid)")
            ->execute([':pid' => $post_id, ':eid' => $employee_id]);
        return 'added';
    }

    public function hasReacted($post_id, $employee_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM recognition_reactions WHERE post_id=:pid AND employee_id=:eid");
        $stmt->execute([':pid' => $post_id, ':eid' => $employee_id]);
        return (bool)$stmt->fetch();
    }

    // ── Leaderboard ────────────────────────────────────────────

    public function getMonthlyLeaderboard($limit = 5) {
        $stmt = $this->conn->prepare("SELECT e.employee_id,
            a.firstname, a.lastname, a.job_title,
            COUNT(rp.post_id) as total_recognitions,
            COALESCE(SUM(sub.reactions), 0) as total_reactions
            FROM employees e
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            LEFT JOIN recognition_posts rp ON e.employee_id = rp.nominee_employee_id
                AND MONTH(rp.created_at) = MONTH(NOW())
                AND YEAR(rp.created_at) = YEAR(NOW())
            LEFT JOIN (
                SELECT post_id, COUNT(*) as reactions FROM recognition_reactions GROUP BY post_id
            ) sub ON rp.post_id = sub.post_id
            WHERE e.employment_status = 'Active'
            GROUP BY e.employee_id
            ORDER BY total_recognitions DESC, total_reactions DESC
            LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaderboard() {
        $stmt = $this->conn->query("SELECT e.employee_id,
            a.firstname, a.lastname, a.job_title,
            COUNT(rp.post_id) as total_recognitions,
            SUM(sub.reactions) as total_reactions
            FROM employees e
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            LEFT JOIN recognition_posts rp ON e.employee_id = rp.nominee_employee_id
            LEFT JOIN (
                SELECT post_id, COUNT(*) as reactions FROM recognition_reactions GROUP BY post_id
            ) sub ON rp.post_id = sub.post_id
            WHERE e.employment_status = 'Active'
            GROUP BY e.employee_id
            ORDER BY total_recognitions DESC, total_reactions DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Stats ──────────────────────────────────────────────────

    public function getStats() {
        $total  = $this->conn->query("SELECT COUNT(*) FROM recognition_posts")->fetchColumn();
        $month  = $this->conn->query("SELECT COUNT(*) FROM recognition_posts WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
        $empotm = $this->conn->query("SELECT COUNT(*) FROM recognition_posts WHERE award_type='Employee of the Month'")->fetchColumn();
        return compact('total', 'month', 'empotm');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function getActiveEmployees() {
        $stmt = $this->conn->query("SELECT e.employee_id, a.firstname, a.lastname, a.job_title
            FROM employees e
            LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
            WHERE e.employment_status = 'Active'
            ORDER BY a.lastname");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function awardTypes() {
        return ['Employee of the Month', 'Peer Appreciation', 'Above & Beyond', 'Best Attendance', 'Team Player', 'Innovation Award'];
    }

    public static function awardIcon($type) {
        return match($type) {
            'Employee of the Month' => 'bi-trophy-fill text-warning',
            'Peer Appreciation'     => 'bi-heart-fill text-danger',
            'Above & Beyond'        => 'bi-star-fill text-warning',
            'Best Attendance'       => 'bi-calendar-check-fill text-success',
            'Team Player'           => 'bi-people-fill text-primary',
            'Innovation Award'      => 'bi-lightbulb-fill text-info',
            default                 => 'bi-award-fill text-secondary'
        };
    }
}
?>
