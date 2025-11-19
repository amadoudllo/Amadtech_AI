<?php
// Pure PHP/HTML chat view - no Blade processing to avoid Vite manifest errors
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html class="dark" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Amadtech_AI Chat</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f97316", // Orange color
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display">
    <!-- Auth Required Modal -->
    <div id="authModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8 max-w-md mx-4 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-center mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">lock</span>
                </div>
            </div>
            <h2 class="text-xl font-bold text-center mb-2 text-slate-900 dark:text-white">Authentification requise</h2>
            <p class="text-center text-slate-600 dark:text-slate-300 mb-6">Vous avez atteint votre limite de messages gratuits. Connectez-vous ou créez un compte pour continuer.</p>
            <div class="flex gap-3">
                <a href="<?php echo route('login'); ?>" class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition text-center">Se connecter</a>
                <a href="<?php echo route('register'); ?>" class="flex-1 px-4 py-3 bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white font-semibold rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition text-center">S'inscrire</a>
            </div>
        </div>
    </div>

<div class="relative mx-auto flex h-screen max-w-lg flex-col overflow-hidden bg-background-light dark:bg-background-dark">
    <!-- Header -->
    <header class="flex shrink-0 items-center justify-between bg-background-light/80 p-4 pb-2 backdrop-blur-sm dark:bg-background-dark/80">
        <button id="menuBtn" class="flex size-10 items-center justify-center text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <h1 class="text-lg font-bold leading-tight tracking-[-0.015em] text-slate-900 dark:text-white">Amadtech_AI</h1>
        <button class="flex size-10 items-center justify-center text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">
            <span class="material-symbols-outlined text-2xl">more_vert</span>
        </button>
    </header>

    <!-- Chat Messages Container -->
    <main id="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
        <div class="flex h-full flex-col items-center justify-center space-y-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-800">
                <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" x2="12" y1="19" y2="23"></line>
                    <line x1="8" x2="16" y1="23" y2="23"></line>
                </svg>
            </div>
            <p class="text-xl font-medium text-slate-600 dark:text-slate-300">Comment puis-je vous aider ?</p>
        </div>
    </main>

    <!-- Input Footer -->
    <footer class="shrink-0 bg-background-light p-4 pt-2 dark:bg-background-dark border-t border-slate-200 dark:border-slate-700">
        <form id="chatForm" class="flex items-center gap-3">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="relative flex-1">
                <input
                    type="text"
                    id="messageInput"
                    class="form-input h-12 w-full resize-none rounded-full border-none bg-slate-200 py-3 pl-5 pr-28 text-base font-normal text-slate-900 placeholder:text-slate-500 focus:outline-0 focus:ring-2 focus:ring-primary/50 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400"
                    placeholder="Envoyez un message..."
                    autocomplete="off"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full text-slate-500 hover:bg-slate-300 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <span class="material-symbols-outlined text-xl">attach_file</span>
                    </button>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full text-slate-500 hover:bg-slate-300 dark:text-slate-400 dark:hover:bg-slate-700 transition">
                        <span class="material-symbols-outlined text-xl">mic</span>
                    </button>
                </div>
            </div>
            <button type="submit" id="sendBtn" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 disabled:opacity-50">
                <span class="material-symbols-outlined text-2xl">arrow_upward</span>
            </button>
        </form>
    </footer>
</div>

<script>
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatContainer = document.getElementById('chatContainer');
    const csrfToken = document.querySelector('input[name="_token"]').value;

    let isFirstMessage = true;

    // Remove initial welcome message on first message
    function removeWelcomeMessage() {
        if (isFirstMessage) {
            chatContainer.innerHTML = '';
            isFirstMessage = false;
        }
    }

    // Add message to chat (text-only)
    function addMessage(content, isUser = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
            isUser
                ? 'bg-primary text-white rounded-br-none'
                : 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white rounded-bl-none'
        }`;
        bubble.textContent = content;

        messageDiv.appendChild(bubble);
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Add HTML message (for links / actions)
    function addHtmlMessage(html, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
            isUser
                ? 'bg-primary text-white rounded-br-none'
                : 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white rounded-bl-none'
        }`;
        bubble.innerHTML = html;

        messageDiv.appendChild(bubble);
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Show loading indicator
    function showLoadingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-start';
        messageDiv.id = 'loadingIndicator';

        const bubble = document.createElement('div');
        bubble.className = 'bg-slate-200 dark:bg-slate-700 px-4 py-2 rounded-lg rounded-bl-none';

        const dots = document.createElement('div');
        dots.className = 'flex space-x-1';
        dots.innerHTML = '<span class="h-2 w-2 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0s;"></span><span class="h-2 w-2 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0.15s;"></span><span class="h-2 w-2 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0.3s;"></span>';

        bubble.appendChild(dots);
        messageDiv.appendChild(bubble);
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Remove loading indicator
    function removeLoadingIndicator() {
        const loadingDiv = document.getElementById('loadingIndicator');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    // Show auth modal
    function showAuthModal() {
        document.getElementById('authModal').classList.remove('hidden');
    }

    // Close auth modal
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        removeWelcomeMessage();
        addMessage(message, true);
        messageInput.value = '';
        sendBtn.disabled = true;
        showLoadingIndicator();

        try {
            const response = await fetch('<?php echo route("chat.send"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();

            removeLoadingIndicator();

            if (!response.ok || data.error) {
                // If backend signals auth required, show login/register modal
                if (data && data.error === 'auth_required') {
                    showAuthModal();
                } else {
                    addMessage('Une erreur est survenue: ' + (data.error || 'Réponse invalide'), false);
                }
            } else {
                addMessage(data.reply, false);
            }
        } catch (error) {
            removeLoadingIndicator();
            addMessage('Erreur de connexion: ' + error.message, false);
        } finally {
            sendBtn.disabled = false;
            messageInput.focus();
        }
    });

    // Focus on load
    messageInput.focus();
</script>

</body>
</html>
