# Chatbot AI - Documentation de Configuration

## 📋 Vue d'ensemble

Ce système implémente un chatbot AI basé sur l'API Groq avec persistance complète des conversations en base de données. Le système gère automatiquement :

- ✅ Création et gestion des conversations
- ✅ Stockage des messages (utilisateur et IA)
- ✅ Contexte avec sliding window (10 derniers messages)
- ✅ System prompt personnalisé
- ✅ Titre auto-généré pour les conversations

---

## 📁 Fichiers créés/modifiés

### 1. **Migrations** (`database/migrations/`)
- `2025_01_01_000001_create_conversations_table.php` - Table conversations
- `2025_01_01_000002_create_messages_table.php` - Table messages

### 2. **Modèles** (`app/Models/`)
- `Conversation.php` - Modèle avec relations et méthode `getContextMessages()`
- `Message.php` - Modèle avec méthode `toApiFormat()`

### 3. **Contrôleur** (`app/Http/Controllers/`)
- `ChatController.php` - Logique complète du chatbot

### 4. **Script SQL** (`database/sql/`)
- `conversations_and_messages.sql` - Import direct pour phpMyAdmin

---

## ⚙️ Installation & Configuration

### Étape 1 : Appliquer les migrations

```bash
php artisan migrate
```

**OU** importer le script SQL manuellement :
1. Ouvrir phpMyAdmin
2. Sélectionner votre base de données `amadtech_ai`
3. Aller dans l'onglet **SQL**
4. Copier-coller le contenu de `database/sql/conversations_and_messages.sql`
5. Cliquer sur **Exécuter**

### Étape 2 : Vérifier le .env

```dotenv
# Groq API Configuration
GROQ_API_KEY=your_api_key_here
GROQ_MODEL=mixtral-8x7b-32768
```

**Modèles Groq disponibles :**
- `mixtral-8x7b-32768` (recommandé)
- `llama-2-70b-chat`
- `gemma-7b-it`

### Étape 3 : Vérifier la route

La route `/chat` doit pointer vers `ChatController@show` (déjà configurée dans `routes/web.php`)

---

## 🔄 Flux de fonctionnement

### Envoi d'un message

**Endpoint :** `POST /chat/send`

**Payload :**
```json
{
  "message": "Bonjour, comment ça va?",
  "conversation_id": null  // Optionnel
}
```

**Réponse :**
```json
{
  "success": true,
  "reply": "Bonjour! Je vais bien, merci de demander...",
  "conversation_id": 1
}
```

### Flux interne

1. **Validation** → Vérifier que le message est présent
2. **Gestion Conversation** → Créer ou récupérer la conversation
3. **Sauvegarde User** → Enregistrer le message de l'utilisateur en BD
4. **Construction du Contexte** :
   - Récupérer les 10 derniers messages
   - Les mettre en ordre chronologique
   - Ajouter le system prompt en début
5. **Appel API Groq** → POST vers `https://api.groq.com/openai/v1/chat/completions`
6. **Sauvegarde IA** → Enregistrer la réponse en BD
7. **Réponse JSON** → Retourner la réponse + conversation_id

---

## 📊 Structure de la Base de Données

### Table `conversations`
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
user_id     INT NULLABLE (FK → users.id)
title       VARCHAR(255)
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

**Index :**
- `idx_user_id` - Pour les requêtes par utilisateur
- `idx_created_at` - Pour le tri par date

### Table `messages`
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
conversation_id INT (FK → conversations.id) NOT NULL
role            ENUM('user', 'assistant', 'system')
content         LONGTEXT
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Index :**
- `idx_conversation_id` - Pour récupérer les messages d'une conversation
- `idx_created_at` - Pour le tri par date

---

## 🎯 Fonctionnalités clés

### 1. Sliding Window (Gestion du contexte)

```php
public function getContextMessages(int $limit = 10)
{
    return $this->messages()
        ->latest()        // Les plus récents d'abord
        ->take($limit)    // Prendre les N derniers
        ->get()
        ->reverse()       // Remettre en ordre chronologique
        ->values();
}
```

**Avantage :** Économise les tokens en ne gardant que le contexte pertinent (10 messages = ~2000 tokens environ)

### 2. System Prompt

```php
private const SYSTEM_PROMPT = "Tu es un assistant IA utile et bienveillant. Tu réponds en français. Tu es attentif, honnête et polis. Tu fournis des réponses précises et complètes.";
```

Modifiable facilement dans la classe.

### 3. Titre auto-généré

Les 50 premiers caractères du premier message deviennent le titre de la conversation.

### 4. Gestion des erreurs

- Logging complet de toutes les erreurs
- Réessais automatiques (3 tentatives) en cas d'erreur réseau
- Support du proxy optionnel via `GROQ_PROXY`

---

## 🔧 Exemples d'utilisation

### JavaScript/Fetch

```javascript
const sendMessage = async (message, conversationId = null) => {
  const response = await fetch('/chat/send', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({
      message: message,
      conversation_id: conversationId,
    }),
  });

  const data = await response.json();
  
  if (data.success) {
    console.log('Réponse IA:', data.reply);
    console.log('Conversation ID:', data.conversation_id);
  } else {
    console.error('Erreur:', data.error);
  }
};

// Utilisation
sendMessage('Bonjour!');
```

### Blade/Livewire

```blade
<!-- Dans votre vue -->
<form wire:submit="sendMessage">
  <input 
    type="text" 
    wire:model="message" 
    placeholder="Votre message"
  >
  <button type="submit">Envoyer</button>
</form>
```

---

## 🧪 Tests

### Test depuis le terminal

```bash
curl -X POST http://localhost:8000/chat/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -d '{
    "message": "Bonjour, comment tu t'\''appelles?"
  }'
```

---

## 📝 Notes importantes

1. **Authentification optionnelle** : Le système accepte les utilisateurs anonymes (user_id = null)
2. **Sliding Window** : Limité à 10 messages pour économiser les tokens
3. **Token Limit** : Groq a des limites de tokens selon le modèle (~30k tokens/min généralement)
4. **Logs** : Consultez `storage/logs/laravel.log` pour déboguer

---

## 🐛 Dépannage

### Erreur : "Groq API key is not configured"
→ Vérifier que `GROQ_API_KEY` est défini dans `.env`

### Erreur : "Failed to get response from Groq API"
→ Vérifier les logs : `tail storage/logs/laravel.log`

### Les conversations ne sont pas sauvegardées
→ Vérifier que les migrations ont été exécutées : `php artisan migrate:status`

### Réponses lentes
→ Vérifier la limite de taux de l'API Groq

---

## 📚 Ressources

- [Groq API Docs](https://console.groq.com/docs)
- [Laravel Eloquent Relations](https://laravel.com/docs/eloquent-relationships)
- [Laravel HTTP Client](https://laravel.com/docs/http-client)

---

**Développé par :** Amadtech AI Team  
**Date :** 2025
