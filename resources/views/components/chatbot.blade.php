<div id="amatrung-chatbot" class="fixed bottom-6 right-6 z-[9995] font-sans" style="touch-action: none;">
    <!-- Chat Button -->
    <button id="chatbot-toggle" class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] border-2 border-blue-100 flex items-center justify-center transition-transform hover:scale-105 focus:outline-none overflow-hidden relative group p-0 cursor-move">
        <img id="chatbot-icon-chat" src="{{ asset('images/image-AI.png') }}" class="w-full h-full object-cover" alt="AI Chatbot">
        <div id="chatbot-icon-close" class="absolute inset-0 bg-blue-500/90 hidden items-center justify-center backdrop-blur-sm">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window-panel hidden absolute bg-white rounded-2xl shadow-2xl border border-blue-100 flex-col overflow-hidden z-50">
        <div id="chatbot-resize-handle" class="chatbot-resize-handle" title="Kéo để thay đổi kích thước" aria-hidden="true"></div>

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 flex items-center justify-between text-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full overflow-hidden border border-white/40 shadow-sm shrink-0">
                    <img src="{{ asset('images/image-AI.png') }}" class="w-full h-full object-cover" alt="AI Icon">
                </div>
                <div>
                    <h3 class="font-bold text-base leading-tight">Trợ lý Y tế AmaTrung</h3>
                    <p class="text-blue-100 text-xs">Luôn sẵn sàng hỗ trợ</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="chatbot-reset-size-btn" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-blue-100 hover:text-white flex items-center justify-center focus:outline-none transition-colors" title="Khôi phục kích thước" type="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3m8 0h3a2 2 0 002-2v-3"></path>
                    </svg>
                </button>
                <button id="chatbot-close-btn" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-blue-100 hover:text-white flex items-center justify-center focus:outline-none transition-colors" type="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" class="flex-1 min-h-0 p-5 overflow-y-auto bg-slate-50 flex flex-col gap-4">
            <!-- Bot Message -->
            <div class="flex gap-3">
                <img src="{{ asset('images/image-AI.png') }}" class="w-9 h-9 rounded-full object-cover shadow-sm shrink-0" alt="AI Icon">
                <div class="bg-white border border-slate-200 p-4 rounded-2xl rounded-tl-sm text-base leading-7 text-slate-700 shadow-sm">
                    Xin chào! Tôi là trợ lý AI của AmaTrung. Tôi có thể giúp bạn tìm hiểu về các loại dược liệu, tra cứu bệnh học, hoặc tóm tắt các bài viết y khoa trên website của chúng tôi. Bạn cần hỏi gì nào?
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="shrink-0 p-4 bg-white border-t border-slate-100">
            <form id="chatbot-form" class="flex items-end gap-2 relative">
                <textarea id="chatbot-input" rows="1" placeholder="Nhập câu hỏi của bạn..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-base leading-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none max-h-36" style="min-height: 48px;"></textarea>
                <button type="submit" id="chatbot-send" class="w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-xl flex-shrink-0 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@php
    $chatbotHistoryStorageKey = auth()->check()
        ? 'amatrung_chat_history_user_' . auth()->id()
        : 'amatrung_chat_history_guest';
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close-btn');
        const resetSizeBtn = document.getElementById('chatbot-reset-size-btn');
        const chatWindow = document.getElementById('chatbot-window');
        const resizeHandle = document.getElementById('chatbot-resize-handle');
        const iconChat = document.getElementById('chatbot-icon-chat');
        const iconClose = document.getElementById('chatbot-icon-close');
        const socialBar = document.getElementById('floating-social-contacts') || document.querySelector('[data-floating-social]');
        
        const form = document.getElementById('chatbot-form');
        const input = document.getElementById('chatbot-input');
        const messagesArea = document.getElementById('chatbot-messages');
        const sendBtn = document.getElementById('chatbot-send');

        const chatHistoryStorageKey = @json($chatbotHistoryStorageKey);
        const legacyChatHistoryStorageKey = 'amatrung_chat_history';

        try {
            sessionStorage.removeItem(legacyChatHistoryStorageKey);
        } catch (e) {
            console.error('Failed to clear legacy chat history', e);
        }

        let chatHistory = [];

        function saveMessageToHistory(text, isUser) {
            chatHistory.push({ text: text, isUser: isUser });
            try {
                sessionStorage.setItem(chatHistoryStorageKey, JSON.stringify(chatHistory));
            } catch (e) {
                console.error('Failed to save chat history to sessionStorage', e);
            }
        }

        function loadChatHistory() {
            try {
                const storedHistory = sessionStorage.getItem(chatHistoryStorageKey);
                if (storedHistory) {
                    const parsed = JSON.parse(storedHistory);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        chatHistory = parsed;
                        // Do not clear the default message in HTML, just append the loaded history
                        chatHistory.forEach(msg => {
                            appendMessage(msg.text, msg.isUser, false);
                        });
                    }
                }
            } catch (e) {
                console.error('Failed to load chat history from sessionStorage', e);
                chatHistory = [];
            }
        }
        const defaultChatSize = {
            width: 520,
            height: 680,
            minWidth: 420,
            minHeight: 500,
            viewportGap: 48,
        };

        function isMobileViewport() {
            return window.matchMedia('(max-width: 640px)').matches;
        }

        function clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }

        function maxChatWidth() {
            return Math.max(defaultChatSize.minWidth, window.innerWidth - defaultChatSize.viewportGap);
        }

        function maxChatHeight() {
            return Math.max(defaultChatSize.minHeight, window.innerHeight - defaultChatSize.viewportGap);
        }

        function applyDefaultSize() {
            if (isMobileViewport()) {
                chatWindow.style.width = '';
                chatWindow.style.height = '';
                return;
            }

            chatWindow.style.width = clamp(defaultChatSize.width, defaultChatSize.minWidth, maxChatWidth()) + 'px';
            chatWindow.style.height = clamp(defaultChatSize.height, defaultChatSize.minHeight, maxChatHeight()) + 'px';
        }

        function keepSizeInsideViewport() {
            if (isMobileViewport()) {
                chatWindow.style.width = '';
                chatWindow.style.height = '';
                return;
            }

            const currentWidth = parseFloat(chatWindow.style.width) || chatWindow.offsetWidth || defaultChatSize.width;
            const currentHeight = parseFloat(chatWindow.style.height) || chatWindow.offsetHeight || defaultChatSize.height;
            chatWindow.style.width = clamp(currentWidth, defaultChatSize.minWidth, maxChatWidth()) + 'px';
            chatWindow.style.height = clamp(currentHeight, defaultChatSize.minHeight, maxChatHeight()) + 'px';
        }

        function setFloatingSocialVisible(isVisible) {
            if (!socialBar) {
                return;
            }

            socialBar.classList.toggle('chatbot-social-hidden', !isVisible);
        }

        function positionChatWindow() {
            const rect = toggleBtn.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            chatWindow.style.top = '';
            chatWindow.style.bottom = '';
            chatWindow.style.left = '';
            chatWindow.style.right = '';
            chatWindow.style.marginTop = '';
            chatWindow.style.marginBottom = '';
            chatWindow.style.transformOrigin = '';

            if (isMobileViewport()) {
                return;
            }

            if (rect.top + rect.height / 2 < viewportHeight / 2) {
                chatWindow.style.top = '100%';
                chatWindow.style.marginTop = '1rem';
                chatWindow.style.transformOrigin = 'top ';
            } else {
                chatWindow.style.bottom = '100%';
                chatWindow.style.marginBottom = '1rem';
                chatWindow.style.transformOrigin = 'bottom ';
            }

            if (rect.left + rect.width / 2 < viewportWidth / 2) {
                chatWindow.style.left = '0';
                chatWindow.style.transformOrigin += 'left';
            } else {
                chatWindow.style.right = '0';
                chatWindow.style.transformOrigin += 'right';
            }
        }

        function toggleChat() {
            const willOpen = chatWindow.classList.contains('hidden');

            if (willOpen) {
                positionChatWindow();
                applyDefaultSize();
            }

            chatWindow.classList.toggle('hidden');
            chatWindow.classList.toggle('flex');
            iconChat.classList.toggle('hidden');
            
            if (chatWindow.classList.contains('hidden')) {
                iconClose.classList.add('hidden');
                iconClose.classList.remove('flex');
                setFloatingSocialVisible(true);
            } else {
                iconClose.classList.remove('hidden');
                iconClose.classList.add('flex');
                setFloatingSocialVisible(false);
            }

            if(!chatWindow.classList.contains('hidden')) {
                input.focus();
            }
        }

        // Draggable Logic
        const chatbotContainer = document.getElementById('amatrung-chatbot');
        let isDragging = false;
        let dragStarted = false;
        let currentX;
        let currentY;
        let initialX;
        let initialY;
        let xOffset = 0;
        let yOffset = 0;

        function dragStart(e) {
            if (e.type === "touchstart") {
                initialX = e.touches[0].clientX - xOffset;
                initialY = e.touches[0].clientY - yOffset;
            } else {
                initialX = e.clientX - xOffset;
                initialY = e.clientY - yOffset;
            }

            // Only drag if clicking the toggle button or its children (not the chat window)
            if (e.target === toggleBtn || toggleBtn.contains(e.target)) {
                isDragging = true;
                dragStarted = false;
            }
        }

        function dragEnd(e) {
            initialX = currentX;
            initialY = currentY;
            isDragging = false;
        }

        function drag(e) {
            if (isDragging) {
                e.preventDefault(); // prevent scrolling while dragging
                dragStarted = true;
                
                if (e.type === "touchmove") {
                    currentX = e.touches[0].clientX - initialX;
                    currentY = e.touches[0].clientY - initialY;
                } else {
                    currentX = e.clientX - initialX;
                    currentY = e.clientY - initialY;
                }

                xOffset = currentX;
                yOffset = currentY;

                setTranslate(currentX, currentY, chatbotContainer);
            }
        }

        function setTranslate(xPos, yPos, el) {
            el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
        }

        chatbotContainer.addEventListener("touchstart", dragStart, { passive: false });
        document.addEventListener("touchend", dragEnd, { passive: false });
        document.addEventListener("touchmove", drag, { passive: false });

        chatbotContainer.addEventListener("mousedown", dragStart);
        document.addEventListener("mouseup", dragEnd);
        document.addEventListener("mousemove", drag);

        toggleBtn.addEventListener('click', function(e) {
            // Prevent opening chat if we just finished dragging
            if (dragStarted) {
                e.preventDefault();
                dragStarted = false;
                return;
            }
            
            toggleChat();
        });
        
        closeBtn.addEventListener('click', toggleChat);
        resetSizeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            applyDefaultSize();
        });

        // Desktop resize from the top-left handle while keeping the chat anchored.
        let isResizing = false;
        let resizeStartX = 0;
        let resizeStartY = 0;
        let resizeStartWidth = 0;
        let resizeStartHeight = 0;

        function resizeStart(e) {
            if (isMobileViewport()) {
                return;
            }

            const point = e.touches ? e.touches[0] : e;
            isResizing = true;
            resizeStartX = point.clientX;
            resizeStartY = point.clientY;
            resizeStartWidth = chatWindow.offsetWidth;
            resizeStartHeight = chatWindow.offsetHeight;
            chatWindow.classList.add('chatbot-resizing');
            document.body.classList.add('chatbot-resizing');
            e.preventDefault();
            e.stopPropagation();
        }

        function resizeMove(e) {
            if (!isResizing) {
                return;
            }

            const point = e.touches ? e.touches[0] : e;
            const anchoredRight = chatWindow.style.right !== '';
            const anchoredBottom = chatWindow.style.bottom !== '';
            const widthDelta = anchoredRight ? resizeStartX - point.clientX : point.clientX - resizeStartX;
            const heightDelta = anchoredBottom ? resizeStartY - point.clientY : point.clientY - resizeStartY;

            chatWindow.style.width = clamp(resizeStartWidth + widthDelta, defaultChatSize.minWidth, maxChatWidth()) + 'px';
            chatWindow.style.height = clamp(resizeStartHeight + heightDelta, defaultChatSize.minHeight, maxChatHeight()) + 'px';
            messagesArea.scrollTop = messagesArea.scrollHeight;
            e.preventDefault();
        }

        function resizeEnd() {
            if (!isResizing) {
                return;
            }

            isResizing = false;
            chatWindow.classList.remove('chatbot-resizing');
            document.body.classList.remove('chatbot-resizing');
        }

        resizeHandle.addEventListener('mousedown', resizeStart);
        document.addEventListener('mousemove', resizeMove);
        document.addEventListener('mouseup', resizeEnd);
        resizeHandle.addEventListener('touchstart', resizeStart, { passive: false });
        document.addEventListener('touchmove', resizeMove, { passive: false });
        document.addEventListener('touchend', resizeEnd);

        window.addEventListener('resize', function() {
            if (!chatWindow.classList.contains('hidden')) {
                positionChatWindow();
                keepSizeInsideViewport();
            }
        });

        // Auto-resize textarea
        input.addEventListener('input', function() {
            this.style.height = '48px';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Submit on Enter (prevent default newline), allow Shift+Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if(this.value.trim() !== '') form.dispatchEvent(new Event('submit'));
            }
        });

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function sanitizeBotText(value) {
            return String(value || '')
                .replace(/(?:^|\n)\s*(?:Nguồn\s+tham\s+khảo|Tài\s+liệu\s+tham\s+khảo|Tham\s+khảo|Sources?|References?)\s*:?\s*[\s\S]*$/iu, '')
                .replace(/\[([^\]]+)\]\(\s*\/(?:bai-viet|tu-dien-thuoc-nam|duoc-lieu)\/[^)\s]*\s*\)/giu, '$1')
                .replace(/(?:^|\n)\s*(?:Xem thêm|Đọc thêm|Chi tiết)(?:\s+tại)?\s*:?\s*\/(?:bai-viet|tu-dien-thuoc-nam|duoc-lieu)\/[^\n]*(?=\n|$)/giu, '')
                .replace(/\s*\(\s*\/(?:bai-viet|tu-dien-thuoc-nam|duoc-lieu)\/[^)\s]*\s*\)/giu, '')
                .replace(/\/(?:bai-viet|tu-dien-thuoc-nam|duoc-lieu)\/[^\s)\]}<>"']*/giu, '')
                .replace(/[ \t]+\n/g, '\n')
                .replace(/\n{3,}/g, '\n\n')
                .trim();
        }

        function appendMessage(text, isUser = false, saveToHistory = true) {
            if (!text) return;

            const messageText = isUser ? text : sanitizeBotText(text);
            if (!messageText) return;
            
            if (saveToHistory) {
                saveMessageToHistory(messageText, isUser);
            }

            const msgDiv = document.createElement('div');
            msgDiv.className = isUser ? 'flex gap-2 flex-row-reverse' : 'flex gap-2';
            
            let avatar = '';
            let bubbleClass = '';
            
            if (isUser) {
                avatar = '<div class="w-9 h-9 bg-slate-200 rounded-full shrink-0 flex items-center justify-center text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>';
                bubbleClass = 'bg-blue-500 text-white p-4 rounded-2xl rounded-tr-sm text-base leading-7 shadow-sm';
            } else {
                avatar = '<img src="{{ asset('images/image-AI.png') }}" class="w-9 h-9 rounded-full object-cover shadow-sm shrink-0" alt="AI Icon">';
                bubbleClass = 'bg-white border border-slate-200 p-4 rounded-2xl rounded-tl-sm text-base leading-7 text-slate-700 shadow-sm chatbot-markdown';
            }

            // Simple markdown parser for bold and line breaks
            let formattedText = escapeHtml(messageText).replace(/\n/g, '<br>');
            formattedText = formattedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            msgDiv.innerHTML = `
                ${avatar}
                <div class="${bubbleClass}">
                    ${formattedText}
                </div>
            `;
            
            messagesArea.appendChild(msgDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function shouldSaveBotMessage(text) {
            const transientErrors = [
                'Hiện tại Trợ lý AmaTrung chưa thể phản hồi',
                'Không thể kết nối đến máy chủ',
                'Xin lỗi, đã có lỗi xảy ra',
            ];

            return !transientErrors.some(errorText => String(text || '').includes(errorText));
        }

        function appendTyping() {
            const msgDiv = document.createElement('div');
            msgDiv.id = 'chatbot-typing';
            msgDiv.className = 'flex gap-2';
            msgDiv.innerHTML = `
                <img src="{{ asset('images/image-AI.png') }}" class="w-8 h-8 rounded-full object-cover shadow-sm shrink-0" alt="AI Icon">
                <div class="bg-white border border-slate-200 py-3 px-4 rounded-2xl rounded-tl-sm text-slate-500 shadow-sm flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            `;
            messagesArea.appendChild(msgDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function removeTyping() {
            const typingIndicator = document.getElementById('chatbot-typing');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            // 1. Add User Message
            appendMessage(message, true, true);
            input.value = '';
            input.style.height = '48px';
            input.disabled = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            // 2. Show Typing Indicator
            appendTyping();

            // 3. Fetch from API
            fetch('/api/chatbot/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                removeTyping();
                const botAnswer = data.answer || data.reply || "Xin lỗi, đã có lỗi xảy ra. Hãy thử lại sau nhé.";
                if (data.success) {
                    appendMessage(botAnswer, false, shouldSaveBotMessage(botAnswer));
                } else {
                    appendMessage(botAnswer, false, false);
                }
            })
            .catch(err => {
                removeTyping();
                appendMessage("Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại mạng.", false, false);
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
                input.focus();
            });
        });

        // Load chat history if it exists in the current session
        loadChatHistory();
    });
</script>
<style>
.chatbot-window-panel {
    width: 520px;
    height: 680px;
    min-width: 420px;
    min-height: 500px;
    max-width: calc(100vw - 48px);
    max-height: calc(100vh - 48px);
}

.chatbot-resize-handle {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 5;
    width: 28px;
    height: 28px;
    cursor: nwse-resize;
}

.chatbot-resize-handle::before {
    content: "";
    position: absolute;
    top: 8px;
    left: 8px;
    width: 12px;
    height: 12px;
    border-top: 2px solid rgba(255, 255, 255, 0.85);
    border-left: 2px solid rgba(255, 255, 255, 0.85);
    border-radius: 3px 0 0 0;
}

.chatbot-resizing,
body.chatbot-resizing {
    cursor: nwse-resize !important;
    user-select: none;
}

.chatbot-social-hidden {
    opacity: 0 !important;
    pointer-events: none !important;
    transform: translateY(-50%) translateX(28px) !important;
}

#chatbot-messages {
    scrollbar-width: thin;
    scrollbar-color: #bfdbfe transparent;
}

.chatbot-markdown {
    max-width: min(100%, 38rem);
}

.chatbot-markdown p { margin-bottom: 0.5rem; }
.chatbot-markdown p:last-child { margin-bottom: 0; }
.chatbot-markdown strong { font-weight: 600; color: #0f172a; }
.chatbot-markdown ul { list-style-type: disc; margin-left: 1rem; margin-bottom: 0.5rem; }

@media (max-width: 640px) {
    #amatrung-chatbot {
        right: 16px !important;
        bottom: 16px !important;
    }

    #chatbot-window {
        position: fixed !important;
        top: auto !important;
        right: 12px !important;
        bottom: 88px !important;
        left: 12px !important;
        width: calc(100vw - 24px) !important;
        height: 68vh !important;
        min-width: 0 !important;
        min-height: 0 !important;
        max-width: calc(100vw - 24px) !important;
        max-height: 70vh !important;
        margin: 0 !important;
    }

    #chatbot-resize-handle,
    #chatbot-reset-size-btn {
        display: none !important;
    }

    #chatbot-messages {
        padding: 1rem;
    }

    .chatbot-markdown {
        max-width: calc(100vw - 98px);
    }
}
</style>
