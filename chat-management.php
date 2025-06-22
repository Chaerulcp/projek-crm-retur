<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_peran'];
$allowed_roles = ['Customer Service', 'Gudang', 'Manajemen', 'Admin'];

if (!in_array($user_role, $allowed_roles)) {
    http_response_code(403);
    echo "Access denied.";
    exit();
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Live Chat Management</h3>

    <div class="mb-3">
        <label class="form-label">Chat Activation Status:</label>
        <span id="chat-status" class="fw-bold">Loading...</span>
    </div>

    <button id="toggle-chat-btn" class="btn btn-primary">Loading...</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSpan = document.getElementById('chat-status');
    const toggleBtn = document.getElementById('toggle-chat-btn');

    function fetchChatStatus() {
        fetch('chat-activation.php')
            .then(response => response.json())
            .then(data => {
                if (data.is_active) {
                    statusSpan.textContent = 'Active';
                    toggleBtn.textContent = 'Deactivate Chat';
                } else {
                    statusSpan.textContent = 'Inactive';
                    toggleBtn.textContent = 'Activate Chat';
                }
                toggleBtn.disabled = false;
            })
            .catch(err => {
                statusSpan.textContent = 'Error loading status';
                toggleBtn.textContent = 'Retry';
                toggleBtn.disabled = false;
                console.error('Error fetching chat status:', err);
            });
    }

    toggleBtn.addEventListener('click', function () {
        toggleBtn.disabled = true;
        fetch('chat-activation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: toggleBtn.textContent === 'Activate Chat' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.is_active) {
                statusSpan.textContent = 'Active';
                toggleBtn.textContent = 'Deactivate Chat';
            } else {
                statusSpan.textContent = 'Inactive';
                toggleBtn.textContent = 'Activate Chat';
            }
        })
        .catch(err => {
            alert('Error toggling chat activation');
            console.error('Error toggling chat activation:', err);
        })
        .finally(() => {
            toggleBtn.disabled = false;
        });
    });

    fetchChatStatus();
});
</script>

<?php require_once 'template-footer.php'; ?>
