<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

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

// Get nomor_tiket from GET or POST JSON
$nomor_tiket = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['nomor_tiket']) && is_string($_GET['nomor_tiket'])) {
        $nomor_tiket = trim($_GET['nomor_tiket']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing or invalid nomor_tiket parameter']);
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['nomor_tiket']) && is_string($input['nomor_tiket'])) {
        $nomor_tiket = trim($input['nomor_tiket']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing or invalid nomor_tiket parameter']);
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

// Fetch ticket info
$stmt = $pdo->prepare("SELECT id_tiket, chat_active FROM tiket_retur WHERE nomor_tiket = ?");
$stmt->execute([$nomor_tiket]);
$tiket = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tiket) {
    http_response_code(404);
    echo json_encode(['error' => 'Ticket not found']);
    exit();
}
$id_tiket = $tiket['id_tiket'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['chat_active' => (bool)$tiket['chat_active']]);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['chat_active'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing chat_active parameter']);
        exit();
    }
    $chat_active = $input['chat_active'] ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE tiket_retur SET chat_active = ? WHERE id_tiket = ?");
    $success = $stmt->execute([$chat_active, $id_tiket]);

    if ($success) {
        echo json_encode(['chat_active' => (bool)$chat_active]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update chat activation']);
    }
    exit();
}
?>
