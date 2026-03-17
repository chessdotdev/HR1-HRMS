<?php
session_start();
require_once '../modules/Recognition.php';
require_once '../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

$post_id = (int)($_POST['post_id'] ?? 0);
if (!$post_id) {
    echo json_encode(['error' => 'Invalid post']); exit;
}

$recog  = new Recognition();
$action = $recog->toggleReaction($post_id, $_SESSION['employee_id']);

$db   = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT COUNT(*) FROM recognition_reactions WHERE post_id = :pid");
$stmt->execute([':pid' => $post_id]);
$count = (int)$stmt->fetchColumn();

echo json_encode(['action' => $action, 'count' => $count]);
