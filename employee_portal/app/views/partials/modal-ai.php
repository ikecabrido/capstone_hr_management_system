<div class="modal fade" id="aiChatModal" tabindex="-1" aria-labelledby="aiChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aiChatModalLabel"><i class="fas fa-robot"></i> AI Assistant</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="aiChatBody" style="height:400px; overflow-y:auto; display:flex; flex-direction:column; gap:8px; padding:10px; background:#f5f5f5;">
            </div>

            <div class="modal-footer d-flex p-2">
                <input type="text" class="form-control me-2" id="aiChatInput" placeholder="Ask me anything..." style="border-radius:20px;">
                <button class="btn btn-primary rounded-pill" id="aiChatSend">Send</button>
            </div>

        </div>
    </div>
</div>

<script>
    const chatBody = document.getElementById('aiChatBody');
    const chatInput = document.getElementById('aiChatInput');
    const chatSend = document.getElementById('aiChatSend');

    function addMessage(sender, text) {
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.flexDirection = 'column';
        wrapper.style.alignItems = sender === 'user' ? 'flex-end' : 'flex-start';

        const bubble = document.createElement('div');
        bubble.innerText = text;
        bubble.style.maxWidth = '75%';
        bubble.style.padding = '8px 12px';
        bubble.style.borderRadius = '15px';
        bubble.style.margin = '2px 0';
        bubble.style.wordBreak = 'break-word';
        bubble.style.background = sender === 'user' ? '#1d4ed8' : '#e5e5ea';
        bubble.style.color = sender === 'user' ? 'white' : 'black';
        bubble.style.boxShadow = '0 1px 2px rgba(0,0,0,0.2)';

        wrapper.appendChild(bubble);
        chatBody.appendChild(wrapper);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    async function sendMessage(message) {
        if (!message) return;
        addMessage('user', message);
        chatInput.value = '';

        try {
            const response = await fetch('app/api/ai-chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'question=' + encodeURIComponent(message)
            });
            const data = await response.json();
            addMessage('ai', data.answer || "Sorry, I couldn't generate an answer.");
        } catch (e) {
            addMessage('ai', 'Error connecting to AI.');
        }
    }

    chatSend.onclick = () => sendMessage(chatInput.value);
    chatInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') sendMessage(chatInput.value);
    });
</script>