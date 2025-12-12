// ============================================
// Messages Module - Weby Platform
// ============================================

class MessagingSystem {
    constructor() {
        this.messageModal = null;
        this.currentResourceId = null;
        this.currentResourceType = null;
        this.init();
    }

    init() {
        this.createMessageModal();
        this.attachEventListeners();
    }

    createMessageModal() {
        // Create modal HTML
        const modalHTML = `
            <div id="messageModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-gray-800 rounded-2xl border border-gray-700 w-full max-w-md shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="messageModalContent">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Ask a Question</h3>
                        <button id="closeMessageModal" class="h-9 w-9 rounded-full flex items-center justify-center bg-gray-700 border border-gray-600 text-gray-400 hover:text-white hover:bg-gray-600 transition">
                            <span class="material-icons-round text-lg">close</span>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm mb-4">Send a question or message to the owner of this resource.</p>
                        <textarea id="messageInput" rows="4" class="w-full resize-none bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Type your message here..."></textarea>
                        <p id="messageStatus" class="mt-2 text-xs text-gray-500"></p>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-700 flex justify-end gap-3">
                        <button id="cancelMessage" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white transition">
                            Cancel
                        </button>
                        <button id="sendMessage" class="px-6 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition flex items-center gap-2">
                            <span class="material-icons-round text-sm">send</span>
                            Send
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.messageModal = document.getElementById('messageModal');
    }

    attachEventListeners() {
        // Close modal buttons
        document.getElementById('closeMessageModal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancelMessage').addEventListener('click', () => this.closeModal());

        // Click outside to close
        this.messageModal.addEventListener('click', (e) => {
            if (e.target === this.messageModal) {
                this.closeModal();
            }
        });

        // Send message button
        document.getElementById('sendMessage').addEventListener('click', () => this.sendMessage());

        // Enter to send (Ctrl+Enter)
        document.getElementById('messageInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                this.sendMessage();
            }
        });
    }

    openModal(resourceId, resourceType) {
        this.currentResourceId = resourceId;
        this.currentResourceType = resourceType;

        const modal = this.messageModal;
        const content = document.getElementById('messageModalContent');

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Clear previous message
        document.getElementById('messageInput').value = '';
        document.getElementById('messageStatus').textContent = '';
    }

    closeModal() {
        const content = document.getElementById('messageModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            this.messageModal.classList.add('hidden');
        }, 300);
    }

    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        const statusEl = document.getElementById('messageStatus');
        const sendBtn = document.getElementById('sendMessage');

        if (!message) {
            statusEl.textContent = 'Please enter a message';
            statusEl.className = 'mt-2 text-xs text-red-400';
            return;
        }

        // Disable button
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="material-icons-round text-sm animate-spin">refresh</span> Sending...';
        statusEl.textContent = 'Sending...';
        statusEl.className = 'mt-2 text-xs text-gray-400';

        try {
            const formData = new FormData();
            formData.append('resource_id', this.currentResourceId);
            formData.append('resource_type', this.currentResourceType);
            formData.append('message', message);

            const response = await fetch('api/send_message.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                statusEl.textContent = 'Message sent successfully!';
                statusEl.className = 'mt-2 text-xs text-green-400';

                setTimeout(() => {
                    this.closeModal();
                }, 1000);
            } else {
                statusEl.textContent = result.message || 'Failed to send message';
                statusEl.className = 'mt-2 text-xs text-red-400';
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<span class="material-icons-round text-sm">send</span> Send';
            }
        } catch (error) {
            console.error('Error sending message:', error);
            statusEl.textContent = 'Network error. Please try again.';
            statusEl.className = 'mt-2 text-xs text-red-400';
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<span class="material-icons-round text-sm">send</span> Send';
        }
    }
}

// Initialize messaging system when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.messagingSystem = new MessagingSystem();
});

// Global function to open message modal
window.openMessageModal = function (resourceId, resourceType) {
    if (window.messagingSystem) {
        window.messagingSystem.openModal(resourceId, resourceType);
    }
};
