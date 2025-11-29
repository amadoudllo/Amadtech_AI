# 📖 GUIDE D'UTILISATION - CHATBOT GROQ

## 🎯 Objectif

Ce guide vous montre comment utiliser le système de chatbot Groq avec persistance des conversations.

---

## 📋 Prérequis

- ✅ PHP 8.2+
- ✅ Laravel 11
- ✅ MySQL 5.7+
- ✅ Clé API Groq (https://console.groq.com)
- ✅ Migrations exécutées

---

## 🚀 Installation rapide

### 1. Appliquer les migrations

```bash
cd C:\xampp\htdocs\git clone\Amadtech_AI

# Option 1: Laravel Migrations
php artisan migrate

# Option 2: Script PowerShell (Windows)
.\setup-chat.ps1

# Option 3: phpMyAdmin (Import SQL)
# Ouvrir: http://localhost/phpmyadmin
# Base: amadtech_ai
# Coller: database/sql/conversations_and_messages.sql
```

### 2. Démarrer le serveur

```bash
php artisan serve
```

Accéder à: `http://localhost:8000/chat`

---

## 💬 Envoi de messages

### Via Frontend (JavaScript)

**Fichier:** `resources/js/chat-examples.js`

```javascript
const chat = new ChatAPI();

// Premier message (crée une nouvelle conversation)
const response = await chat.sendMessage('Bonjour!');

console.log(response.success);           // true
console.log(response.reply);             // Réponse de l'IA
console.log(response.conversation_id);   // 1

// Messages suivants (réutilise la même conversation)
const response2 = await chat.sendMessage('Comment ça va?');
// conversation_id est automatiquement conservée
```

### Via cURL (Terminal)

```bash
# Obtenir le CSRF token d'abord
curl http://localhost:8000/chat

# Puis envoyer un message
curl -X POST http://localhost:8000/chat/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -d '{
    "message": "Bonjour!",
    "conversation_id": null
  }'
```

### Via Tinker (Laravel REPL)

```bash
php artisan tinker

# Envoyer un message via le contrôleur
$request = new Illuminate\Http\Request();
$request->merge([
    'message' => 'Bonjour!',
    'conversation_id' => null,
]);

$controller = new App\Http\Controllers\ChatController();
$response = $controller->sendMessage($request)->getData();

print_r($response);
```

---

## 📊 Visualiser les conversations

### Via Tinker

```bash
php artisan tinker

# Voir toutes les conversations
ChatHelper::showConversations();

# Voir les messages d'une conversation
ChatHelper::showConversationMessages(1);

# Voir le contexte pour l'API
ChatHelper::showContextMessages(1);

# Estimer les tokens utilisés
ChatHelper::estimateTokens(1);
```

### Via phpMyAdmin

1. Ouvrir: `http://localhost/phpmyadmin`
2. Base: `amadtech_ai`
3. Table: `conversations` ou `messages`

```sql
-- Voir toutes les conversations
SELECT * FROM conversations ORDER BY created_at DESC;

-- Voir les messages d'une conversation
SELECT * FROM messages WHERE conversation_id = 1 ORDER BY created_at;

-- Compter les messages par conversation
SELECT conversation_id, COUNT(*) as count FROM messages GROUP BY conversation_id;
```

---

## 🔄 Flux d'une conversation

### Étape 1: Créer une conversation

```javascript
// Premier message (aucun conversation_id)
const response = await chat.sendMessage('Bonjour!');

// Réponse:
{
  "success": true,
  "reply": "Bonjour! Comment puis-je vous aider?",
  "conversation_id": 1
}

// ✅ Nouvelle conversation créée en BD
// ✅ Message utilisateur sauvegardé
// ✅ Message IA sauvegardé
// ✅ Titre auto-généré: "Bonjour!"
```

### Étape 2: Continuer la conversation

```javascript
// Utiliser la conversation_id obtenue
const response = await chat.sendMessage('Explique-moi la gravité', conversation_id: 1);

// Réponse:
{
  "success": true,
  "reply": "La gravité est une force fondamentale...",
  "conversation_id": 1
}

// ✅ Même conversation réutilisée
// ✅ Contexte des 10 derniers messages inclus
// ✅ Réponse de l'IA tenant compte des messages précédents
```

### Étape 3: Nouvelle conversation

```javascript
// Sans conversation_id (nouveau)
const response = await chat.sendMessage('Comment faire une pizza?');

// Réponse:
{
  "success": true,
  "reply": "Voici comment faire une pizza...",
  "conversation_id": 2
}

// ✅ Nouvelle conversation créée (ID: 2)
// ✅ Contexte précédent de la conversation 1 ignoré
```

---

## 🎛️ Personnalisation

### Changer le System Prompt

**Fichier:** `app/Http/Controllers/ChatController.php`

```php
private const SYSTEM_PROMPT = "Tu es un assistant IA spécialisé en [VOTRE DOMAINE]";
```

### Changer le modèle Groq

**Fichier:** `.env`

```dotenv
GROQ_MODEL=mixtral-8x7b-32768  # Rapide, bon compromis
# GROQ_MODEL=llama-2-70b-chat   # Plus puissant, plus lent
# GROQ_MODEL=gemma-7b-it        # Ultra léger
```

### Changer la limite du contexte

**Fichier:** `app/Models/Conversation.php`

```php
public function getContextMessages(int $limit = 10)  // ← Modifier 10
{
    return $this->messages()
        ->latest()
        ->take($limit)  // Ici
        ->get()
        ->reverse()
        ->values();
}
```

---

## 📈 Optimisation

### Réduire la latence

```php
// app/Http/Controllers/ChatController.php

$payload = [
    'max_completion_tokens' => 512,  // ← Réduire (était 1024)
    'temperature' => 0.5,             // ← Réduire (était 0.7)
];
```

### Économiser les tokens

```php
// Réduire le contexte
public function getContextMessages(int $limit = 5)  // ← 5 au lieu de 10
```

### Support du proxy

```dotenv
# .env
GROQ_PROXY=http://127.0.0.1:8888
```

---

## 🧪 Tests

### Test automatisé

```bash
php test-chat.php
```

Cela testera:
- ✅ Création de conversation
- ✅ Ajout de messages
- ✅ Sliding window
- ✅ Format API
- ✅ Estimation tokens

### Test manuel

```bash
php artisan tinker

# Créer une conversation de test
$conv = App\Models\Conversation::create(['user_id' => null]);

# Ajouter des messages
$conv->messages()->create(['role' => 'user', 'content' => 'Bonjour']);
$conv->messages()->create(['role' => 'assistant', 'content' => 'Salut!']);

# Vérifier
$conv->getContextMessages()->each(fn($m) => echo "[$m->role]: $m->content\n");
```

---

## 🔍 Debugging

### Activer les logs détaillés

```dotenv
# .env
LOG_LEVEL=debug
```

### Voir les requêtes API

```bash
tail -f storage/logs/laravel.log

# Filtrer Groq
tail -f storage/logs/laravel.log | grep -i groq
```

### Voir les queries SQL

```bash
php artisan tinker

# Activer les logs SQL
DB::listen(function($query) {
    echo $query->sql . "\n";
});

# Puis exécuter
ChatHelper::showConversations();
```

---

## 🐛 Problèmes courants

### "Groq API key is not configured"

```bash
# Vérifier le .env
grep GROQ_API_KEY .env

# Si vide, l'obtenir: https://console.groq.com
# Puis copier-coller dans .env
```

### "Table 'amadtech_ai.conversations' doesn't exist"

```bash
# Exécuter les migrations
php artisan migrate

# Vérifier
php artisan migrate:status
```

### "Unauthorized" (erreur 401)

```bash
# La clé API est invalide
# 1. Aller sur: https://console.groq.com
# 2. Obtenir une nouvelle clé
# 3. Coller dans .env GROQ_API_KEY
```

### Réponses lentes

```bash
# 1. Groq peut être surchargé, attendre
# 2. Vérifier la limite de tokens/min
# 3. Réduire max_completion_tokens
```

---

## 📚 Exemples complets

### Vue.js 3

```javascript
import { ref, onMounted } from 'vue';

export default {
  setup() {
    const messages = ref([]);
    const input = ref('');
    const conversationId = ref(null);

    const sendMessage = async () => {
      if (!input.value.trim()) return;

      messages.value.push({
        role: 'user',
        content: input.value,
      });

      try {
        const response = await fetch('/chat/send', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({
            message: input.value,
            conversation_id: conversationId.value,
          }),
        });

        const data = await response.json();

        if (data.success) {
          messages.value.push({
            role: 'assistant',
            content: data.reply,
          });
          conversationId.value = data.conversation_id;
        }
      } finally {
        input.value = '';
      }
    };

    return {
      messages,
      input,
      sendMessage,
    };
  },
};
```

### React

```jsx
import { useState } from 'react';

export default function Chat() {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState('');
  const [conversationId, setConversationId] = useState(null);

  const handleSend = async (e) => {
    e.preventDefault();

    setMessages([...messages, { role: 'user', content: input }]);

    const response = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ message: input, conversation_id: conversationId }),
    });

    const data = await response.json();

    if (data.success) {
      setMessages(prev => [...prev, { role: 'assistant', content: data.reply }]);
      setConversationId(data.conversation_id);
    }

    setInput('');
  };

  return (
    <div>
      {messages.map((msg, i) => (
        <div key={i}>{msg.role}: {msg.content}</div>
      ))}
      <form onSubmit={handleSend}>
        <input value={input} onChange={(e) => setInput(e.target.value)} />
        <button>Send</button>
      </form>
    </div>
  );
}
```

---

## 📞 Support

- **Groq Docs:** https://console.groq.com/docs
- **Laravel Docs:** https://laravel.com/docs
- **API Status:** https://status.groq.com

---

**Happy Chatting! 🚀**
