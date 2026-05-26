<div id="amatrung-chatbot" class="fixed bottom-6 right-6 z-50 font-sans cursor-move" style="touch-action: none;">
    <!-- Chat Button -->
    <button id="chatbot-toggle" class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] border-2 border-blue-100 flex items-center justify-center transition-transform hover:scale-105 focus:outline-none overflow-hidden relative group p-0">
        <img id="chatbot-icon-chat" src="{{ asset('images/image-AI.png') }}" class="w-full h-full object-cover" alt="AI Chatbot">
        <div id="chatbot-icon-close" class="absolute inset-0 bg-blue-500/90 hidden items-center justify-center backdrop-blur-sm">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="hidden absolute w-[350px] sm:w-[400px] h-[500px] bg-white rounded-2xl shadow-2xl border border-blue-100 flex-col overflow-hidden transition-all z-50">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Trợ lý Y tế AmaTrung</h3>
                    <p class="text-blue-100 text-[11px]">Luôn sẵn sàng hỗ trợ</p>
                </div>
            </div>
            <button id="chatbot-close-btn" class="text-blue-100 hover:text-white focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" class="flex-1 p-4 overflow-y-auto bg-slate-50 flex flex-col gap-3">
            <!-- Bot Message -->
            <div class="flex gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div class="bg-white border border-slate-200 p-3 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm">
                    Xin chào! Tôi là trợ lý AI của AmaTrung. Tôi có thể giúp bạn tìm hiểu về các loại dược liệu, tra cứu bệnh học, hoặc tóm tắt các bài viết y khoa trên website của chúng tôi. Bạn cần hỏi gì nào?
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-slate-100">
            <form id="chatbot-form" class="flex items-end gap-2 relative">
                <textarea id="chatbot-input" rows="1" placeholder="Nhập câu hỏi của bạn..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none max-h-32" style="min-height: 44px;"></textarea>
                <button type="submit" id="chatbot-send" class="w-11 h-11 bg-blue-500 hover:bg-blue-600 text-white rounded-xl flex-shrink-0 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close-btn');
        const chatWindow = document.getElementById('chatbot-window');
        const iconChat = document.getElementById('chatbot-icon-chat');
        const iconClose = document.getElementById('chatbot-icon-close');
        
        const form = document.getElementById('chatbot-form');
        const input = document.getElementById('chatbot-input');
        const messagesArea = document.getElementById('chatbot-messages');
        const sendBtn = document.getElementById('chatbot-send');

        function toggleChat() {
            if (chatWindow.classList.contains('hidden')) {
                // Calculate position before opening
                const rect = toggleBtn.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                // Reset styles
                chatWindow.style.top = '';
                chatWindow.style.bottom = '';
                chatWindow.style.left = '';
                chatWindow.style.right = '';
                chatWindow.style.marginTop = '';
                chatWindow.style.marginBottom = '';
                chatWindow.style.transformOrigin = '';

                // Vertical
                if (rect.top + rect.height / 2 < viewportHeight / 2) {
                    chatWindow.style.top = '100%';
                    chatWindow.style.marginTop = '1rem';
                    chatWindow.style.transformOrigin = 'top ';
                } else {
                    chatWindow.style.bottom = '100%';
                    chatWindow.style.marginBottom = '1rem';
                    chatWindow.style.transformOrigin = 'bottom ';
                }

                // Horizontal
                if (rect.left + rect.width / 2 < viewportWidth / 2) {
                    chatWindow.style.left = '0';
                    chatWindow.style.transformOrigin += 'left';
                } else {
                    chatWindow.style.right = '0';
                    chatWindow.style.transformOrigin += 'right';
                }
            }

            chatWindow.classList.toggle('hidden');
            chatWindow.classList.toggle('flex');
            iconChat.classList.toggle('hidden');
            
            if (chatWindow.classList.contains('hidden')) {
                iconClose.classList.add('hidden');
                iconClose.classList.remove('flex');
            } else {
                iconClose.classList.remove('hidden');
                iconClose.classList.add('flex');
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

        // Auto-resize textarea
        input.addEventListener('input', function() {
            this.style.height = '44px';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Submit on Enter (prevent default newline), allow Shift+Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if(this.value.trim() !== '') form.dispatchEvent(new Event('submit'));
            }
        });

        function appendMessage(text, isUser = false) {
            const msgDiv = document.createElement('div');
            msgDiv.className = isUser ? 'flex gap-2 flex-row-reverse' : 'flex gap-2';
            
            let avatar = '';
            let bubbleClass = '';
            
            if (isUser) {
                avatar = '<div class="w-8 h-8 bg-slate-200 rounded-full flex-shrink-0 flex items-center justify-center text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>';
                bubbleClass = 'bg-blue-500 text-white p-3 rounded-2xl rounded-tr-sm text-sm shadow-sm';
            } else {
                avatar = '<div class="w-8 h-8 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>';
                bubbleClass = 'bg-white border border-slate-200 p-3 rounded-2xl rounded-tl-sm text-sm text-slate-700 shadow-sm chatbot-markdown';
            }

            // Simple markdown parser for bold and line breaks
            let formattedText = text.replace(/\n/g, '<br>');
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

        function appendTyping() {
            const msgDiv = document.createElement('div');
            msgDiv.id = 'chatbot-typing';
            msgDiv.className = 'flex gap-2';
            msgDiv.innerHTML = `
                <div class="w-8 h-8 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
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
            appendMessage(message, true);
            input.value = '';
            input.style.height = '44px';
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
                if (data.success) {
                    appendMessage(data.reply, false);
                } else {
                    appendMessage("Xin lỗi, đã có lỗi xảy ra. Hãy thử lại sau nhé.", false);
                }
            })
            .catch(err => {
                removeTyping();
                appendMessage("Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại mạng.", false);
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
                input.focus();
            });
        });
    });
</script>
<style>
.chatbot-markdown p { margin-bottom: 0.5rem; }
.chatbot-markdown p:last-child { margin-bottom: 0; }
.chatbot-markdown strong { font-weight: 600; color: #0f172a; }
.chatbot-markdown ul { list-style-type: disc; margin-left: 1rem; margin-bottom: 0.5rem; }
</style>
