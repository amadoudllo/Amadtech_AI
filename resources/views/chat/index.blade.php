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
        html, body {
            height: 100%;
            overflow: hidden;
        }
        body {
            min-height: 100vh;
        }
        /* Masquer la scrollbar tout en gardant le scroll */
        #chatContainer::-webkit-scrollbar {
            display: none;
        }
        #chatContainer {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display overflow-hidden">
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

<div class="relative mx-auto flex h-screen max-w-7xl flex-row bg-background-light dark:bg-background-dark overflow-hidden">
    <!-- Sidebar Menu -->
    <aside id="sidebar" class="fixed md:relative left-0 top-0 h-screen w-64 bg-slate-900 dark:bg-slate-950 flex flex-col z-40 transition-transform md:translate-x-0 -translate-x-full">
        <!-- Sidebar Header -->
        <div class="p-4 flex items-center justify-between">
            <h2 class="text-white font-bold">AmadtechAI</h2>
            <button id="closeSidebarBtn" class="md:hidden text-slate-400 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- New Chat Button -->
        <button id="newChatBtn" class="m-3 px-4 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg flex items-center gap-2 justify-center transition">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Nouvelle conversation</span>
        </button>

        <!-- Conversations List -->
        <div id="conversationsList" class="flex-1 overflow-y-auto p-2 space-y-1">
            <!-- Les conversations seront ajoutées ici dynamiquement -->
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 space-y-2">
            <!-- Déconnexion visible seulement si connecté -->
            <div id="sidebarLogoutBtn"></div>
        </div>
    </aside>

    <!-- Overlay pour fermer le sidebar sur mobile -->
    <div id="sidebarOverlay" class="fixed md:hidden inset-0 bg-black/50 z-30 hidden"></div>

    <!-- Main Chat Area -->
    <div class="relative mx-auto flex w-full max-w-lg md:max-w-none md:flex-1 flex-col overflow-hidden h-screen">
        <!-- Header -->
    <header class="flex shrink-0 items-center justify-between bg-background-light/80 p-4 pb-2 backdrop-blur-sm dark:bg-background-dark/80">
        <button id="menuBtn" class="flex size-10 items-center justify-center text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <h1 id="conversationTitle" class="text-lg font-bold leading-tight tracking-[-0.015em] text-slate-900 dark:text-white cursor-pointer hover:opacity-70 transition" contenteditable="false">AmadtechAI</h1>
        
        <!-- User Profile Avatar or Login Button -->
        <div id="userProfileSection" class="flex items-center gap-2">
            <!-- Avatar will be inserted here by JavaScript -->
        </div>
    </header>

    <!-- Chat Messages Container -->
    <main id="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0">
        <div class="flex h-full flex-col items-center justify-center space-y-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full">
                <?php /* Remplacé: fond supprimé pour ne pas entourer le logo */ ?>
                <img style="width:100px;height:auto" src="<?php echo asset('images/chat-logo.png'); ?>" alt="Amadtech logo" class="h-8 w-8 object-contain" />
            </div>
            <p class="text-xl font-medium text-slate-600 dark:text-slate-300">Comment puis-je vous aider ?</p>
        </div>
    </main>

    <!-- Input Footer -->
    <footer class="shrink-0 bg-background-light p-4 pt-2 dark:bg-background-dark">
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
</div>

<script>
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatContainer = document.getElementById('chatContainer');
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const conversationsList = document.getElementById('conversationsList');
    const newChatBtn = document.getElementById('newChatBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const menuBtn = document.getElementById('menuBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    
    // Vérifier si l'utilisateur est authentifié
    const isAuthenticated = <?php echo auth()->check() ? 'true' : 'false'; ?>;

    let isFirstMessage = true;
    let conversationId = null;  // 🔑 Stocker l'ID de la conversation

    // 🔑 Charger la conversation depuis localStorage
    function loadConversationId() {
        const saved = localStorage.getItem('currentConversationId');
        if (saved) {
            conversationId = parseInt(saved);
            console.log('✅ Conversation rechargée:', conversationId);
            loadConversationMessages(); // Charger les messages
        }
    }

    // 🔑 Sauvegarder la conversation dans localStorage
    function saveConversationId(id) {
        if (id) {
            localStorage.setItem('currentConversationId', id);
            console.log('💾 Conversation sauvegardée:', id);
        }
    }

    // 🔑 Sauvegarder les conversations guest dans localStorage (pour la barre latérale)
    function saveGuestConversation(convId, title) {
        let guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
        
        // Check if conversation already exists
        const existingIndex = guestConversations.findIndex(conv => conv.id === convId);
        
        if (existingIndex !== -1) {
            // Update existing conversation title
            guestConversations[existingIndex].title = title;
            guestConversations[existingIndex].updatedAt = new Date().toISOString();
        } else {
            // Add new conversation
            guestConversations.push({
                id: convId,
                title: title || 'Nouvelle conversation',
                updatedAt: new Date().toISOString()
            });
        }
        
        // Sort by most recent first
        guestConversations.sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt));
        
        localStorage.setItem('guestConversations', JSON.stringify(guestConversations));
        console.log('💾 Guest conversations sauvegardées:', guestConversations);
    }

    // Charger les messages d'une conversation
    async function loadConversationMessages() {
        if (!conversationId) return;

        try {
            const response = await fetch(`/api/chat/conversations/${conversationId}/messages`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                // Handle authorization errors
                if (response.status === 403) {
                    addMessage('❌ Vous n\'avez pas accès à cette conversation.', false);
                    conversationId = null;
                    localStorage.removeItem('currentConversationId');
                    return;
                }
                
                addMessage('Erreur: ' + (data.error || 'Impossible de charger les messages'), false);
                return;
            }

            if (data.success && data.messages) {
                chatContainer.innerHTML = '';
                isFirstMessage = false;

                // Charger les messages (filtrer les messages système)
                data.messages.forEach(msg => {
                    if (msg.role !== 'system') {
                        addMessage(msg.content, msg.role === 'user');
                    }
                });
            }
        } catch (error) {
            console.error('Erreur lors du chargement des messages:', error);
            addMessage('Erreur de connexion: ' + error.message, false);
        }
    }

    // Charger la liste des conversations (API pour users, localStorage pour guests)
    async function loadConversations() {
        try {
            // Si l'utilisateur n'est pas connecté, ne rien afficher dans l'historique
            if (!isAuthenticated) {
                conversationsList.innerHTML = '';
                return;
            }

            // Utilisateur authentifié - charger ses conversations
            const response = await fetch('/api/chat/conversations', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await response.json();

            if (data.success && data.conversations && data.conversations.length > 0) {
                // Afficher les conversations de l'utilisateur
                conversationsList.innerHTML = '';
                data.conversations.forEach(conv => {
                    addConversationButton(conv.id, conv.title);
                });
            } else {
                // Utilisateur connecté mais pas de conversations
                conversationsList.innerHTML = '';
            }
        } catch (error) {
            console.error('Erreur lors du chargement des conversations:', error);
            conversationsList.innerHTML = '';
        }
    }

    // Charger les conversations guest depuis localStorage
    function loadGuestConversationsFromStorage() {
        conversationsList.innerHTML = '';
        const guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
        
        guestConversations.forEach(conv => {
            addConversationButton(conv.id, conv.title);
        });
    }

    // Ajouter un bouton de conversation à la liste
    function addConversationButton(convId, title) {
        const convBtn = document.createElement('button');
        convBtn.type = 'button';
        convBtn.dataset.convId = convId;
        convBtn.className = `w-full text-left px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 transition truncate group relative ${
            conversationId === convId ? 'bg-slate-800 text-white' : ''
        }`;
        
        // Limiter le titre à 10 caractères + "..."
        const truncatedTitle = title.length > 10 ? title.substring(0, 10) + '...' : title;
        
        // Afficher les boutons edit/delete SEULEMENT si l'utilisateur est authentifié
        const actionButtons = isAuthenticated ? `
            <div class="hidden group-hover:flex gap-1 absolute right-2 top-1/2 transform -translate-y-1/2">
                <button type="button" class="text-blue-400 hover:text-blue-300 p-1" onclick="editConversation(${convId}, event)" title="Modifier">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </button>
                <button type="button" class="text-red-400 hover:text-red-300 p-1" onclick="deleteConversation(${convId}, event)" title="Supprimer">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
        ` : '';
        
        convBtn.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="truncate" title="${escapeHtml(title)}">${escapeHtml(truncatedTitle)}</span>
                ${actionButtons}
            </div>
        `;
        convBtn.onclick = () => switchConversation(convId);
        conversationsList.appendChild(convBtn);
    }

    // Échapper HTML pour éviter les injections
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Changer de conversation
    async function switchConversation(convId) {
        conversationId = convId;
        saveConversationId(convId);
        closeSidebar();
        await loadConversationMessages();
        await loadConversations(); // Mettre à jour l'affichage de la liste
    }

    // Supprimer une conversation
    async function deleteConversation(convId, event) {
        event.stopPropagation();

        if (!confirm('Êtes-vous sûr de vouloir supprimer cette conversation?')) return;

        try {
            // Try to delete from API (if authenticated)
            const response = await fetch(`/api/chat/conversations/${convId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await response.json();

            if (data.success || response.status === 404) {
                // Deletion successful or conversation doesn't exist in DB
                if (conversationId === convId) {
                    conversationId = null;
                    localStorage.removeItem('currentConversationId');
                    chatContainer.innerHTML = `
                        <div class="flex h-full flex-col items-center justify-center space-y-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full">
                                <img style="width:100px;height:auto" src="/images/chat-logo.png" alt="Amadtech logo" class="h-8 w-8 object-contain" />
                            </div>
                            <p class="text-xl font-medium text-slate-600 dark:text-slate-300">Comment puis-je vous aider ?</p>
                        </div>
                    `;
                }

                // Also remove from guest conversations localStorage
                let guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
                guestConversations = guestConversations.filter(conv => conv.id !== convId);
                localStorage.setItem('guestConversations', JSON.stringify(guestConversations));

                await loadConversations();
            } else {
                alert('Erreur lors de la suppression');
            }
        } catch (error) {
            // Not authenticated - just remove from localStorage (guest mode)
            if (conversationId === convId) {
                conversationId = null;
                localStorage.removeItem('currentConversationId');
                chatContainer.innerHTML = `
                    <div class="flex h-full flex-col items-center justify-center space-y-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full">
                            <img style="width:100px;height:auto" src="/images/chat-logo.png" alt="Amadtech logo" class="h-8 w-8 object-contain" />
                        </div>
                        <p class="text-xl font-medium text-slate-600 dark:text-slate-300">Comment puis-je vous aider ?</p>
                    </div>
                `;
            }

            let guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
            guestConversations = guestConversations.filter(conv => conv.id !== convId);
            localStorage.setItem('guestConversations', JSON.stringify(guestConversations));

            await loadConversations();
        }
    }

    // Créer une nouvelle conversation
    function startNewChat() {
        conversationId = null;
        localStorage.removeItem('currentConversationId');
        isFirstMessage = true;
        chatContainer.innerHTML = `
            <div class="flex h-full flex-col items-center justify-center space-y-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full">
                    <img style="width:100px;height:auto" src="/images/chat-logo.png" alt="Amadtech logo" class="h-8 w-8 object-contain" />
                </div>
                <p class="text-xl font-medium text-slate-600 dark:text-slate-300">Comment puis-je vous aider ?</p>
            </div>
        `;
        messageInput.value = '';
        messageInput.focus();
        closeSidebar();
    }

    // Fermer le sidebar
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }

    // Ouvrir le sidebar
    menuBtn.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    });

    closeSidebarBtn.addEventListener('click', closeSidebar);

    sidebarOverlay.addEventListener('click', closeSidebar);

    newChatBtn.addEventListener('click', startNewChat);

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
                body: JSON.stringify({ 
                    message,
                    conversation_id: conversationId  // 🔑 Envoyer l'ID de la conversation
                }),
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
                // 🔑 Sauvegarder l'ID de la conversation pour les messages suivants
                if (data.conversation_id) {
                    conversationId = data.conversation_id;
                    saveConversationId(data.conversation_id);  // 💾 Persister dans localStorage
                    
                    // For guests, also save to guest conversations list
                    saveGuestConversation(data.conversation_id, messageInput.value.substring(0, 50));
                    
                    await loadConversations(); // Charger la liste mise à jour
                }
            }
        } catch (error) {
            removeLoadingIndicator();
            addMessage('Erreur de connexion: ' + error.message, false);
        } finally {
            sendBtn.disabled = false;
            messageInput.focus();
        }
    });

    // Créer et afficher le profil utilisateur
    function initUserProfile() {
        const userProfileSection = document.getElementById('userProfileSection');
        const sidebarLogoutBtn = document.getElementById('sidebarLogoutBtn');
        
        <?php if (auth()->check()): ?>
            // Utilisateur connecté - afficher son profil
            const userName = "<?php echo auth()->user()->name ?? 'User'; ?>";
            const userEmail = "<?php echo auth()->user()->email ?? ''; ?>";
            
            // Extraire les 2 premières lettres du prénom
            const nameParts = userName.trim().split(' ');
            const initials = (nameParts[0]?.[0] || 'U') + (nameParts[1]?.[0] || nameParts[0]?.[1] || '');
            
            // Créer le HTML du profil (avatar uniquement)
            userProfileSection.innerHTML = `
                <div class="flex items-center gap-2">
                    <!-- Avatar -->
                    <div class="relative flex size-10 items-center justify-center rounded-full bg-primary text-white font-bold text-sm hover:bg-primary/90 transition cursor-pointer" title="${escapeHtml(userName)}">
                        ${initials.toUpperCase()}
                    </div>
                </div>
            `;
            
            // Ajouter le bouton Déconnexion dans la sidebar
            sidebarLogoutBtn.innerHTML = `
                <form method="POST" action="<?php echo route('logout'); ?>" class="w-full">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm flex items-center gap-2 transition justify-center font-semibold">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        <span>Déconnexion</span>
                    </button>
                </form>
            `;
        <?php else: ?>
            // Utilisateur non connecté - afficher un bouton de connexion
            userProfileSection.innerHTML = `
                <div class="flex gap-2">
                    <a href="<?php echo route('login'); ?>" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition text-sm font-semibold">
                        Se connecter
                    </a>
                </div>
            `;
            
            // Pas de bouton Déconnexion pour guests
            sidebarLogoutBtn.innerHTML = '';
        <?php endif; ?>
    }
    
    // Fonction pour modifier une conversation (renommer)
    async function editConversation(convId, event) {
        event.stopPropagation();
        
        // 1. Charger la conversation
        conversationId = convId;
        saveConversationId(convId);
        await loadConversationMessages();
        
        // 2. Récupérer le titre et l'afficher dans le header
        const convButton = document.querySelector(`[data-conv-id="${convId}"]`);
        const titleElement = convButton.querySelector('span:first-child');
        const fullTitle = titleElement.title || titleElement.textContent;
        
        // 3. Afficher le titre complet dans le header et le rendre éditable
        const titleHeader = document.getElementById('conversationTitle');
        titleHeader.textContent = fullTitle;
        titleHeader.contentEditable = 'true';
        titleHeader.focus();
        titleHeader.select();
        
        // 4. Quand l'utilisateur termine l'édition
        titleHeader.onblur = async function() {
            titleHeader.contentEditable = 'false';
            const newTitle = titleHeader.textContent.trim();
            
            if (!newTitle || newTitle === fullTitle) {
                titleHeader.textContent = fullTitle;
                return;
            }
            
            try {
                // Mettre à jour via l'API (si authentifié)
                const response = await fetch(`/api/chat/conversations/${convId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ title: newTitle })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour le titre dans le sidebar
                    const truncatedTitle = newTitle.length > 10 ? newTitle.substring(0, 10) + '...' : newTitle;
                    titleElement.textContent = truncatedTitle;
                    titleElement.title = newTitle;
                    titleHeader.textContent = newTitle;
                    
                    // Mettre à jour aussi dans localStorage
                    let guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
                    const index = guestConversations.findIndex(c => c.id === convId);
                    if (index !== -1) {
                        guestConversations[index].title = newTitle;
                        localStorage.setItem('guestConversations', JSON.stringify(guestConversations));
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                // Fallback: mettre à jour seulement en localStorage (guest mode)
                let guestConversations = JSON.parse(localStorage.getItem('guestConversations') || '[]');
                const index = guestConversations.findIndex(c => c.id === convId);
                if (index !== -1) {
                    guestConversations[index].title = newTitle;
                    localStorage.setItem('guestConversations', JSON.stringify(guestConversations));
                    
                    // Mettre à jour le sidebar
                    const truncatedTitle = newTitle.length > 10 ? newTitle.substring(0, 10) + '...' : newTitle;
                    titleElement.textContent = truncatedTitle;
                    titleElement.title = newTitle;
                    titleHeader.textContent = newTitle;
                }
            }
        };
    }
    }

    // Focus on load et charger les données
    document.addEventListener('DOMContentLoaded', async () => {
        initUserProfile();              // 👤 Initialiser le profil utilisateur
        loadConversationId();           // 🔑 Charger la conversation sauvegardée
        await loadConversations();      // Charger la liste des conversations
        messageInput.focus();
    });
</script>

</body>
</html>
