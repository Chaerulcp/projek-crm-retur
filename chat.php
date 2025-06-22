<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_peran'];
$allowed_roles = ['Customer Service', 'Gudang', 'Manajemen', 'Admin'];
$is_admin = in_array($user_role, $allowed_roles);

// Get nomor_tiket from query parameter
if (!isset($_GET['nomor_tiket']) || empty(trim($_GET['nomor_tiket']))) {
    echo "Ticket number (nomor_tiket) is required.";
    exit();
}
$nomor_tiket = trim($_GET['nomor_tiket']);

// Fetch ticket info
$stmt = $pdo->prepare("SELECT id_tiket, chat_active FROM tiket_retur WHERE nomor_tiket = ?");
$stmt->execute([$nomor_tiket]);
$tiket = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tiket) {
    echo "Ticket not found.";
    exit();
}
$id_tiket = $tiket['id_tiket'];
$chat_active = (bool)$tiket['chat_active'];

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Live Chat for Ticket #<?php echo htmlspecialchars($nomor_tiket); ?></h3>

    <?php if ($is_admin): ?>
    <div class="mb-3">
        <label class="form-label">Chat Activation</label>
        <button id="toggle-chat-btn" class="btn btn-sm btn-primary"><?php echo $chat_active ? 'Deactivate Chat' : 'Activate Chat'; ?></button>
    </div>
    <?php endif; ?>

    <div id="chat-box" style="height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
        Loading messages...
    </div>

    <div class="mt-3">
        <textarea id="chat-input" class="form-control" rows="3" placeholder="Type your message..." <?php echo $chat_active ? '' : 'disabled'; ?>></textarea>
        <button id="send-btn" class="btn btn-primary mt-2" <?php echo $chat_active ? '' : 'disabled'; ?>>Send</button>
    </div>

    <?php if (!$chat_active): ?>
    <div id="chat-status-message" class="mt-2 text-muted">
        Chat is currently inactive or you do not have permission to send messages.
    </div>
    <?php endif; ?>
</div>

<script>
    const nomorTiket = <?php echo json_encode($nomor_tiket); ?>;
    const isAdmin = <?php echo json_encode($is_admin); ?>;
    const chatActiveInitial = <?php echo json_encode($chat_active); ?>;
</script>
<script src="assets/js/chat.js"></script>

<?php require_once 'template-footer.php'; ?>
