<?php
require_once '../config/Database.php';

class AuditLog {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function log(int $admin_id, string $admin_name, string $action, string $module, string $description = '') {
        $stmt = $this->conn->prepare("INSERT INTO audit_logs (admin_id, admin_name, action, module, description) VALUES (:aid, :aname, :action, :module, :desc)");
        $stmt->execute([
            ':aid'    => $admin_id,
            ':aname'  => $admin_name,
            ':action' => $action,
            ':module' => $module,
            ':desc'   => $description,
        ]);
    }

    public function getLogs(int $limit = 100, string $module = '', int $admin_id = 0) {
        $where = [];
        $params = [];
        if ($module)   { $where[] = 'module = :module'; $params[':module'] = $module; }
        if ($admin_id) { $where[] = 'admin_id = :aid';  $params[':aid']    = $admin_id; }

        $sql = "SELECT * FROM audit_logs" . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY created_at DESC LIMIT {$limit}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModules() {
        return $this->conn->query("SELECT DISTINCT module FROM audit_logs ORDER BY module")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAdmins() {
        return $this->conn->query("SELECT DISTINCT admin_id, admin_name FROM audit_logs ORDER BY admin_name")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
