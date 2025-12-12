// ========================================
// Ask Gemini AI Assistant - Weby Platform
// ========================================

// ========== Drawer Open/Close ==========

const askGeminiBtn = document.getElementById('askGeminiBtn');
const geminiDrawer = document.getElementById('geminiDrawer');
const geminiBackdrop = document.getElementById('geminiBackdrop');
const closeGeminiBtn = document.getElementById('closeGeminiDrawer');

const geminiForm = document.getElementById('geminiForm');
const geminiInput = document.getElementById('geminiInput');
const geminiMessages = document.getElementById('geminiMessages');
const geminiStatus = document.getElementById('geminiStatus');
const geminiSendBtn = document.getElementById('geminiSendBtn');

function openGeminiDrawer() {
    if (!geminiDrawer) return;
    geminiDrawer.classList.remove('translate-x-full');
    geminiBackdrop?.classList.remove('hidden');
}

function closeGeminiDrawer() {
    if (!geminiDrawer) return;
    geminiDrawer.classList.add('translate-x-full');
    geminiBackdrop?.classList.add('hidden');
}

// Event Listeners
askGeminiBtn?.addEventListener('click', openGeminiDrawer);
closeGeminiBtn?.addEventListener('click', closeGeminiDrawer);
geminiBackdrop?.addEventListener('click', closeGeminiDrawer);

// Close with Escape key
window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeGeminiDrawer();
});

// ========== Message Display System ==========

function appendMessage(role, text) {
    if (!geminiMessages) return;

    const wrapper = document.createElement('div');
    wrapper.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-3`;

    const bubble = document.createElement('div');
    bubble.className = `max-w-[85%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed whitespace-pre-wrap ${role === 'user'
        ? 'bg-purple-600 text-white'
        : 'bg-gray-800 border border-gray-700 text-gray-100'
        }`;

    bubble.textContent = text;
    wrapper.appendChild(bubble);

    geminiMessages.appendChild(wrapper);
    geminiMessages.scrollTop = geminiMessages.scrollHeight;

    return wrapper;
}

// ========== Thinking Animation ==========

function createThinkingMessage() {
    if (!geminiMessages) {
        return { stop: () => { } };
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'flex justify-start mb-3';

    const textEl = document.createElement('span');
    textEl.className = 'text-xs text-gray-400 italic';
    wrapper.appendChild(textEl);

    geminiMessages.appendChild(wrapper);
    geminiMessages.scrollTop = geminiMessages.scrollHeight;

    const frames = ['Thinking', 'Thinking.', 'Thinking..', 'Thinking...'];
    let idx = 0;
    textEl.textContent = frames[idx];

    const intervalId = setInterval(() => {
        idx = (idx + 1) % frames.length;
        textEl.textContent = frames[idx];
    }, 350);

    function stop(newText) {
        clearInterval(intervalId);

        // Convert to normal AI message bubble
        textEl.className = 'max-w-[85%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed whitespace-pre-wrap bg-gray-800 border border-gray-700 text-gray-100';
        textEl.textContent = newText;
    }

    return { stop };
}

// ========== Suggested Prompts ==========

document.querySelectorAll('[data-gemini-suggest]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const prompt = btn.getAttribute('data-gemini-suggest') || '';
        if (!geminiInput) return;
        geminiInput.value = prompt;
        geminiInput.focus();
    });
});

// ========== Gemini API Integration ==========

// SECURE: API calls now go through PHP backend
// The API key is stored server-side and never exposed to the browser

async function callGemini(prompt) {
    try {
        const res = await fetch('api/gemini.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                prompt: prompt
            })
        });

        const data = await res.json();

        // Check for errors from our backend
        if (!data.success) {
            throw new Error(data.error || 'Unknown error occurred');
        }

        // Return the AI response
        return data.response;

    } catch (error) {
        console.error("Failed to call Gemini:", error);
        throw error;
    }
}




// ========== Form Submission ==========

geminiForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = geminiInput?.value.trim();
    if (!text) return;

    // Clear input and show user message
    geminiInput.value = '';
    geminiInput.focus();
    appendMessage('user', text);

    // Create thinking animation
    const thinking = createThinkingMessage();

    // Disable send button
    if (geminiSendBtn) geminiSendBtn.disabled = true;
    if (geminiStatus) geminiStatus.textContent = 'Contacting Gemini...';

    try {
        const reply = await callGemini(text);
        thinking.stop(reply);
        if (geminiStatus) geminiStatus.textContent = '';
    } catch (err) {
        console.error(err);
        const errorMsg = err.message || 'Sorry, something went wrong while contacting Gemini. Please try again.';
        thinking.stop(errorMsg);
        if (geminiStatus) {
            geminiStatus.textContent = 'Error: ' + errorMsg;
        }
    } finally {
        if (geminiSendBtn) geminiSendBtn.disabled = false;
    }
});
