<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has allowed role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_peran'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_role = $_SESSION['user_peran'];
$allowed_roles = ['Customer Service', 'Gudang', 'Manajemen', 'Admin'];

if (!in_array($user_role, $allowed_roles)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Set response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id_tiket']) || !is_numeric($input['id_tiket']) || !isset($input['chat_active'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid parameters']);
    exit();
}

$id_tiket = (int)$input['id_tiket'];
$chat_active = $input['chat_active'] ? 1 : 0;

// Update chat_active status for the ticket
$stmt = $pdo->prepare("UPDATE tiket_retur SET chat_active = ? WHERE id_tiket = ?");
$success = $stmt->execute([$chat_active, $id_tiket]);

if ($success) {
    echo json_encode(['chat_active' => (bool)$chat_active]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update chat activation']);
}
?>
