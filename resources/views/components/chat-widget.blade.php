<div id="ai-chat-widget" class="fixed bottom-6 right-6 z-[9999] font-sans"
    style="font-family: 'Inter', 'Poppins', sans-serif;">
    <!-- Chat Popup -->
    <div id="ai-chat-popup"
        class="hidden flex-col bg-white w-[360px] max-w-[calc(100vw-2rem)] shadow-[0_5px_40px_rgba(0,0,0,0.16)] rounded-2xl border border-gray-100 mb-4 overflow-hidden origin-bottom-right transition-all duration-300 transform scale-95 opacity-0">
        <!-- Close Button & Header -->
        <div class="relative pt-8 pb-4 px-6 text-center flex flex-col items-center">
            <!-- Clear Chat Button -->
            <button id="ai-clear-btn"
                class="hidden absolute top-4 left-4 text-gray-400 hover:text-red-500 transition-colors"
                title="{{ __('home.chat.clear') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            <!-- Close Button -->
            <button id="ai-close-btn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors"
                title="{{ __('home.chat.close') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="mb-3 text-[#2563EB]"> <!-- Blue color -->
                <!-- Headset/Customer Support Icon -->
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2C6.47715 2 2 6.47715 2 12V18C2 19.1046 2.89543 20 4 20H6V14H4V12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12V14H18V20H20C21.1046 20 22 19.1046 22 18V12C22 6.47715 17.5228 2 12 2Z"
                        fill="currentColor" />
                    <path d="M12 22C14.2091 22 16 20.2091 16 18H8C8 20.2091 9.79086 22 12 22Z" fill="currentColor" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">{{ __('home.chat.greeting') }} 👋</h3>
            <p class="text-gray-500 text-[15px] mt-1">{{ __('home.chat.subtitle') }}</p>
        </div>

        <!-- Chat Content Area -->
        <div class="flex-grow overflow-y-auto px-5 py-2 flex flex-col h-[280px]" id="ai-chat-area">

            <!-- FAQs -->
            <div id="ai-faq-container" class="flex flex-col">
                <div class="border-b border-gray-100">
                    <button
                        class="faq-btn w-full text-left py-3.5 flex items-center group transition-colors hover:text-[#2563EB]"
                        data-question="{{ __('home.chat.faq_1') }}">
                        <svg class="w-4 h-4 mr-3 text-gray-800 group-hover:text-[#2563EB]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                        <span
                            class="text-[14px] font-medium group-hover:text-[#2563EB] transition-colors pointer-events-none">{{ __('home.chat.faq_1') }}</span>
                    </button>
                </div>
                <div class="border-b border-gray-100">
                    <button
                        class="faq-btn w-full text-left py-3.5 flex items-center group transition-colors hover:text-[#2563EB]"
                        data-question="{{ __('home.chat.faq_2') }}">
                        <svg class="w-4 h-4 mr-3 text-gray-800 group-hover:text-[#2563EB]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                        <span
                            class="text-[14px] font-medium group-hover:text-[#2563EB] transition-colors pointer-events-none">{{ __('home.chat.faq_2') }}</span>
                    </button>
                </div>
                <div>
                    <button
                        class="faq-btn w-full text-left py-3.5 flex items-center group transition-colors hover:text-[#2563EB]"
                        data-question="{{ __('home.chat.faq_3') }}">
                        <svg class="w-4 h-4 mr-3 text-gray-800 group-hover:text-[#2563EB]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                        <span
                            class="text-[14px] font-medium group-hover:text-[#2563EB] transition-colors pointer-events-none">{{ __('home.chat.faq_3') }}</span>
                    </button>
                </div>
            </div>

            <!-- Chat History -->
            <div id="ai-chat-history" class="hidden flex-col gap-3 pb-2">
                <!-- Messages will be appended here -->
            </div>

            <!-- Loading Indicator -->
            <div id="ai-typing-indicator"
                class="hidden flex gap-1 mt-2 mb-2 p-3 bg-gray-100 rounded-2xl rounded-tl-none self-start w-fit">
                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
            </div>

        </div>

        <!-- Input Area -->
        <div class="px-5 py-4 pb-2 mt-auto border-t border-gray-50">
            <div
                class="relative border hover:border-gray-400 border-gray-800 rounded-2xl bg-white overflow-hidden transition-all group focus-within:border-[#2563EB] focus-within:ring-1 focus-within:ring-[#2563EB]">
                <textarea id="ai-chat-input" rows="1"
                    class="w-full bg-transparent border-0 focus:ring-0 resize-none px-4 py-3 pb-10 text-[14px] text-gray-800 placeholder-gray-500 max-h-[100px]"
                    placeholder="{{ __('home.chat.placeholder') }}" style="box-shadow: none; outline: none;"></textarea>
                <div class="absolute bottom-2 right-2">
                    <button id="ai-send-btn"
                        class="bg-[#f3f4f6] hover:bg-[#2563EB] group-focus-within:bg-[#2563EB] group-focus-within:text-white hover:text-white text-gray-400 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer terms -->
        <div class="px-5 pb-4 pt-1 text-center bg-white">
            <p class="text-[12px] text-gray-400">{{ __('home.chat.disclaimer') }}</p>
        </div>
    </div>

    <!-- Toggle Button -->
    <div class="flex justify-end mt-4">
        <button id="ai-toggle-btn"
            class="flex items-center space-x-2.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-6 py-3.5 rounded-full shadow-[0_8px_20px_rgba(37,99,235,0.3)] hover:shadow-[0_10px_25px_rgba(37,99,235,0.4)] transition-all duration-300 transform hover:-translate-y-1">
            <!-- Icon -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg" class="text-white">
                <path
                    d="M12 2C6.47715 2 2 6.47715 2 12V18C2 19.1046 2.89543 20 4 20H6V14H4V12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12V14H18V20H20C21.1046 20 22 19.1046 22 18V12C22 6.47715 17.5228 2 12 2Z"
                    fill="currentColor" />
                <path d="M12 22C14.2091 22 16 20.2091 16 18H8C8 20.2091 9.79086 22 12 22Z" fill="currentColor" />
            </svg>
            <span class="font-bold text-[16px] tracking-wide">{{ __('home.chat.button') }}</span>
        </button>
    </div>
</div>

<script type="application/json" id="ai-chat-translations">
    {!! json_encode([
        'error_no_reply' => __('home.chat.error_no_reply'),
        'error_busy' => __('home.chat.error_busy'),
        'error_network' => __('home.chat.error_network')
    ]) !!}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const __chatLang = JSON.parse(document.getElementById('ai-chat-translations').textContent);
        const toggleBtn = document.getElementById('ai-toggle-btn');
        const popup = document.getElementById('ai-chat-popup');
        const closeBtn = document.getElementById('ai-close-btn');
        const clearBtn = document.getElementById('ai-clear-btn');
        const faqContainer = document.getElementById('ai-faq-container');
        const chatHistory = document.getElementById('ai-chat-history');
        const chatArea = document.getElementById('ai-chat-area');
        const chatInput = document.getElementById('ai-chat-input');
        const sendBtn = document.getElementById('ai-send-btn');
        const typingIndicator = document.getElementById('ai-typing-indicator');
        const faqBtns = document.querySelectorAll('.faq-btn');

        let isOpen = false;
        let isChatActive = false;

        // Auto-resize textarea
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.value === '') {
                this.style.height = 'auto';
            }
        });

        // Toggle chat window
        function toggleChat() {
            isOpen = !isOpen;
            if (isOpen) {
                popup.classList.remove('hidden');
                popup.classList.add('flex');

                setTimeout(() => {
                    popup.classList.remove('scale-95', 'opacity-0');
                    popup.classList.add('scale-100', 'opacity-100');
                    if (isChatActive) chatInput.focus();
                }, 10);
            } else {
                popup.classList.remove('scale-100', 'opacity-100');
                popup.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    popup.classList.add('hidden');
                    popup.classList.remove('flex');
                }, 300);
            }
        }

        toggleBtn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        // Core bot logic (Gemini API)
        async function getBotResponse(message) {
            const lowerMsg = message.toLowerCase();

            // Use the backend route for AI API
            try {
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document
                            .querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data && data.reply) {
                    // Remove markdown boldings if any, for cleaner chat display natively
                    let cleanReply = data.reply.replace(/\*\*/g, '');
                    return cleanReply;
                } else {
                    return __chatLang.error_no_reply;
                }
            } catch (error) {
                console.error('API Proxy Error:', error);
                return __chatLang.error_busy;
            }
        }

        function scrollToBottom() {
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        function appendMessage(text, isUser = false) {
            const msgDiv = document.createElement('div');

            if (isUser) {
                msgDiv.className =
                    'bg-[#2563EB] text-white p-3 rounded-2xl rounded-tr-none self-end max-w-[85%] text-[14px] leading-relaxed shadow-sm break-words whitespace-pre-wrap';
            } else {
                msgDiv.className =
                    'bg-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-none self-start max-w-[85%] text-[14px] leading-relaxed shadow-sm break-words format-text whitespace-pre-wrap';
            }

            msgDiv.textContent = text;
            chatHistory.appendChild(msgDiv);
            scrollToBottom();
        }

        function handleSend(message) {
            if (!message.trim()) return;

            // Switch to chat mode if in FAQ mode
            if (!isChatActive) {
                isChatActive = true;
                faqContainer.classList.add('hidden');
                chatHistory.classList.remove('hidden');
                chatHistory.classList.add('flex');
                clearBtn.classList.remove('hidden');
            }

            // Append user message
            appendMessage(message, true);

            // Clear input
            chatInput.value = '';
            chatInput.style.height = 'auto';

            // Show typing indicator
            chatHistory.appendChild(typingIndicator);
            typingIndicator.classList.remove('hidden');
            scrollToBottom();

            // Fetch response from Gemini AI
            getBotResponse(message).then(reply => {
                typingIndicator.classList.add('hidden');
                appendMessage(reply, false);
            }).catch(err => {
                typingIndicator.classList.add('hidden');
                appendMessage(__chatLang.error_network, false);
            });
        }

        // Event listeners for sending
        sendBtn.addEventListener('click', () => {
            handleSend(chatInput.value);
        });

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSend(this.value);
            }
        });

        // Clear chat functionality
        clearBtn.addEventListener('click', function() {
            // Remove all messages except typing indicator
            const messages = chatHistory.querySelectorAll('div:not(#ai-typing-indicator)');
            messages.forEach(msg => msg.remove());

            // Reset to FAQ view
            isChatActive = false;
            chatHistory.classList.add('hidden');
            chatHistory.classList.remove('flex');
            faqContainer.classList.remove('hidden');
            clearBtn.classList.add('hidden');
            chatInput.value = '';
            chatInput.style.height = 'auto';
        });

        // Event listeners for FAQs
        faqBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const question = this.getAttribute('data-question');
                handleSend(question);
            });
        });
    });
</script>
