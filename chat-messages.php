<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Allow guest access: set user_id and user_role accordingly
$user_id = null;
$user_role = 'G'; // Default to Guest

if (isset($_SESSION['user_id']) && isset($_SESSION['user_peran'])) {
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_peran'];
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
$chat_active = (bool)$tiket['chat_active'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch last 50 messages for the ticket ordered by timestamp ascending
    $stmt = $pdo->prepare("
        SELECT cm.id, cm.sender_id, cm.sender_role, cm.message, cm.timestamp,
            CASE 
                WHEN cm.sender_role = 'G' THEN 'Guest'
                ELSE ps.nama_lengkap
            END AS sender_name
        FROM chat_messages cm
        LEFT JOIN pengguna_sistem ps ON cm.sender_id = ps.id_pengguna AND cm.sender_role != 'G'
        WHERE cm.id_tiket = ?
        ORDER BY cm.timestamp ASC
        LIMIT 50
    ");
    $stmt->execute([$id_tiket]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['messages' => $messages, 'chat_active' => $chat_active]);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$chat_active) {
        http_response_code(403);
        echo json_encode(['error' => 'Chat is not active']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['message']) || trim($input['message']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Message cannot be empty']);
        exit();
    }
    $message = trim($input['message']);

    // Determine sender_role for database
    $sender_role = $user_role;
    if (!in_array($sender_role, ['Customer Service', 'Gudang', 'Manajemen', 'Admin'])) {
        $sender_role = 'G'; // Guest or other roles
    }

    // For guests, set sender_id to null or 0
    $sender_id = $user_id ?? 0;

    $stmt = $pdo->prepare("INSERT INTO chat_messages (id_tiket, sender_id, sender_role, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_tiket, $sender_id, $sender_role, $message]);

    echo json_encode(['success' => true]);
    exit();
}
?>
