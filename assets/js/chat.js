document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chat-box');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const toggleBtn = document.getElementById('toggle-chat-btn');

    let isActive = chatActiveInitial;
    let isAdminUser = isAdmin;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    let lastMessageTimestamp = null;

    function fetchChatMessages() {
        fetch(window.location.origin + '/chat-messages.php?nomor_tiket=' + encodeURIComponent(nomorTiket), { credentials: 'include' })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error fetching chat messages:', data.error);
                    return;
                }
                if (typeof data.chat_active === 'boolean') {
                    setChatActive(data.chat_active);
                }
                if (data.messages && data.messages.length > 0) {
                    // Append only new messages after lastMessageTimestamp
                    data.messages.forEach(msg => {
                        if (!lastMessageTimestamp || new Date(msg.timestamp) > new Date(lastMessageTimestamp)) {
                            const sender = escapeHtml(msg.sender_name || 'Unknown');
                            const message = escapeHtml(msg.message);
                            const time = new Date(msg.timestamp).toLocaleTimeString();
                            const messageDiv = document.createElement('div');
                            messageDiv.innerHTML = `<strong>${sender}</strong> [${time}]: ${message}`;
                            chatBox.appendChild(messageDiv);
                            lastMessageTimestamp = msg.timestamp;
                        }
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(err => {
                console.error('Error fetching chat messages:', err);
            });
    }

    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        sendBtn.disabled = true;

        fetch(window.location.origin + '/chat-messages.php', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message, nomor_tiket: nomorTiket })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                chatInput.value = '';
                fetchChatMessages();
            } else if (data.error) {
                alert('Error: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
        })
        .finally(() => {
            sendBtn.disabled = false;
        });
    }

    function toggleChatActivation() {
        if (!toggleBtn) return;
        const newStatus = !isActive;

        fetch(window.location.origin + '/chat-activation.php', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ chat_active: newStatus, nomor_tiket: nomorTiket })
        })
        .then(response => response.json())
        .then(data => {
            if (typeof data.chat_active === 'boolean') {
                setChatActive(data.chat_active);
                toggleBtn.textContent = data.chat_active ? 'Deactivate Chat' : 'Activate Chat';
            } else if (data.error) {
                alert('Error toggling chat activation: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Error toggling chat activation:', err);
        });
    }

    function setChatActive(active) {
        isActive = active;
        if (chatInput) chatInput.disabled = !active;
        if (sendBtn) sendBtn.disabled = !active || !chatInput.value.trim();
        if (toggleBtn) toggleBtn.disabled = false;
        const statusMessage = document.getElementById('chat-status-message');
        if (statusMessage) {
            statusMessage.style.display = active ? 'none' : 'block';
        }
    }

    if (toggleBtn && isAdminUser) {
        toggleBtn.addEventListener('click', toggleChatActivation);
    } else if (toggleBtn) {
        toggleBtn.style.display = 'none';
    }

    sendBtn.addEventListener('click', sendMessage);

    chatInput.addEventListener('input', function () {
        sendBtn.disabled = !isActive || !chatInput.value.trim();
    });

    fetchChatMessages();
    setInterval(fetchChatMessages, 3000);
});
