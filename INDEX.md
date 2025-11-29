# 🤖 CHATBOT GROQ - GUIDE COMPLET D'INDEX

Bienvenue! Vous trouverez ici un index complet de toutes les ressources créées pour votre chatbot IA.

---

## 🚀 DÉMARRAGE RAPIDE (5 minutes)

### 1. Exécuter les migrations
```bash
cd C:\xampp\htdocs\git clone\Amadtech_AI
php artisan migrate
```

### 2. Démarrer le serveur
```bash
php artisan serve
```

### 3. Accéder au chat
```
http://localhost:8000/chat
```

---

## 📚 DOCUMENTATION (Lire dans cet ordre)

### 1. **QUICK_START.md** ← **COMMENCER ICI**
   - Résumé rapide
   - Architecture visuelle
   - Points clés
   - 5 minutes de lecture

### 2. **CHATBOT_DOCUMENTATION.md**
   - Documentation technique complète
   - Structure BD expliquée
   - Flux d'exécution détaillé
   - Bonnes pratiques
   - 15 minutes de lecture

### 3. **USAGE_GUIDE.md**
   - Guide pratique d'utilisation
   - Exemples Vue, React, JavaScript
   - Debugging et dépannage
   - Tests et vérification
   - 20 minutes de lecture

### 4. **IMPLEMENTATION_SUMMARY.md**
   - Résumé d'implémentation
   - Fichiers créés
   - Architecture complète
   - Performance et optimisation
   - 10 minutes de lecture

### 5. **VERIFICATION_CHECKLIST.md**
   - Checklist de vérification
   - Tests à effectuer
   - Vérification des installations
   - À cocher avant production

---

## 🗂️ FICHIERS CRÉÉS

### 🗄️ Base de données
```
database/
├── migrations/
│   ├── 2025_01_01_000001_create_conversations_table.php
│   └── 2025_01_01_000002_create_messages_table.php
└── sql/
    └── conversations_and_messages.sql
```

**À faire:**
```bash
php artisan migrate
# OU copier-coller conversations_and_messages.sql dans phpMyAdmin
```

### 📦 Modèles (app/Models/)
```
Conversation.php
├── relations: hasMany(messages), belongsTo(user)
├── méthodes: getContextMessages($limit)
└── casts: timestamps

Message.php
├── relations: belongsTo(conversation)
├── méthodes: toApiFormat()
└── enum: role (user, assistant, system)
```

### 🎮 Contrôleurs (app/Http/Controllers/)
```
ChatController.php
├── show() - Afficher l'interface chat
├── sendMessage() - Logique complète
├── getOrCreateConversation()
├── buildContextMessages()
├── callGroqApi()
└── generateConversationTitle()

ChatHelper.php (Utilitaires de test)
├── createTestConversation()
├── showConversations()
├── showContextMessages()
├── estimateTokens()
└── deleteAllConversations()
```

**À utiliser:**
```bash
php artisan tinker
> ChatHelper::showConversations();
> ChatHelper::estimateTokens(1);
```

### ⚙️ Configuration (config/)
```
groq.php
├── Groq API settings
├── Chat configuration
├── Rate limiting
└── Temperature, tokens, etc.
```

### 📝 Exemples & Tests
```
resources/js/chat-examples.js
├── Classe ChatAPI
├── Exemple Vue.js 3
├── Exemple React
├── Exemple Alpine.js
└── Exemple cURL

test-chat.php
├── Test création conversation
├── Test ajout messages
├── Test sliding window
├── Test format API
└── Test estimation tokens
```

**À exécuter:**
```bash
php test-chat.php
```

### 🚀 Scripts d'installation
```
setup-chat.sh (Linux/Mac)
setup-chat.ps1 (Windows)
```

**À exécuter:**
```bash
.\setup-chat.ps1  # Windows
bash setup-chat.sh  # Linux/Mac
```

---

## 💻 UTILISATION - EXAMPLES

### Via JavaScript
```javascript
const chat = new ChatAPI();
const response = await chat.sendMessage('Bonjour!');
console.log(response.reply);           // Réponse IA
console.log(response.conversation_id); // ID (pour messages suivants)
```

### Via Tinker
```bash
php artisan tinker

> $conv = App\Models\Conversation::create(['user_id' => null]);
> $conv->messages()->create(['role' => 'user', 'content' => 'Salut!']);
> $conv->getContextMessages();
```

### Via cURL
```bash
curl -X POST http://localhost:8000/chat/send \
  -H "Content-Type: application/json" \
  -d '{"message":"Bonjour!"}'
```

---

## 🎯 ARCHITECTURE

```
                    Frontend
                       ↓
                 POST /chat/send
                       ↓
            ChatController::sendMessage()
                       ↓
        ┌──────────────┬──────────────┐
        ↓              ↓              ↓
    Validation   Conversation   Contexte
        ↓              ↓              ↓
   Message BD    Créer/Récup   10 messages
        ↓              ↓              ↓
        └──────────────┴──────────────┘
                       ↓
                   Groq API
                       ↓
              Message IA (BD)
                       ↓
            JSON Response ← Frontend
```

---

## 📊 DATABASE SCHEMA

### Table: conversations
```
id             INT PRIMARY KEY AUTO_INCREMENT
user_id        INT NULLABLE (FK → users.id)
title          VARCHAR(255)
created_at     TIMESTAMP
updated_at     TIMESTAMP
```

### Table: messages
```
id             INT PRIMARY KEY AUTO_INCREMENT
conversation_id INT (FK → conversations.id)
role           ENUM('user', 'assistant', 'system')
content        LONGTEXT
created_at     TIMESTAMP
updated_at     TIMESTAMP
```

---

## 🔑 CONFIGURATION REQUISE

### .env
```dotenv
GROQ_API_KEY=gsk_...                    # Obtenir: console.groq.com
GROQ_MODEL=mixtral-8x7b-32768          # (optionnel)
GROQ_PROXY=http://127.0.0.1:8888       # (optionnel)
```

### Modèles Groq disponibles
- `mixtral-8x7b-32768` ← **RECOMMANDÉ** (rapide)
- `llama-2-70b-chat` (puissant, lent)
- `gemma-7b-it` (léger)

---

## ✨ FEATURES

✅ Persistence conversations MySQL  
✅ Sliding window contexte (10 messages)  
✅ System prompt personnalisable  
✅ Titre auto-généré  
✅ Gestion erreurs robuste  
✅ Logging complet  
✅ Support utilisateurs anonymes  
✅ CSRF protection  
✅ Typage PHP complet  
✅ Relations Eloquent  

---

## 🧪 TESTS

### Test rapide
```bash
php test-chat.php
```

### Test interactif
```bash
php artisan tinker

> ChatHelper::showConversations();
> ChatHelper::estimateTokens(1);
```

### Vérification
```bash
php artisan migrate:status
php artisan route:list | grep chat
```

---

## 📈 PERFORMANCE

| Métrique | Valeur |
|----------|--------|
| Tokens/min | 7,900 |
| Contexte | 10 messages (~2,000 tokens) |
| Max réponse | 1,024 tokens |
| Timeout | 60s |
| Retries | 3x |

---

## 🐛 AIDE & DÉPANNAGE

### Problème: "Table doesn't exist"
```bash
php artisan migrate
```

### Problème: "Groq API key not configured"
```bash
# Vérifier .env
grep GROQ_API_KEY .env

# Obtenir clé: https://console.groq.com
```

### Problème: "Unauthorized (401)"
- Clé API invalide
- Obtenir nouvelle clé sur console.groq.com

### Voir les logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📞 RESSOURCES

- **Groq Console:** https://console.groq.com
- **Groq Docs:** https://console.groq.com/docs
- **Laravel Docs:** https://laravel.com/docs
- **Eloquent ORM:** https://laravel.com/docs/eloquent

---

## 🎓 PROCHAINES ÉTAPES

1. **Lire:** QUICK_START.md
2. **Installer:** `php artisan migrate`
3. **Tester:** `php test-chat.php`
4. **Accéder:** http://localhost:8000/chat
5. **Intégrer:** Frontend avec exemples de chat-examples.js
6. **Déployer:** Lire VERIFICATION_CHECKLIST.md

---

## 📋 FICHIERS RÉSUMÉ

| Fichier | Type | Utilisé pour |
|---------|------|--------------|
| QUICK_START.md | Doc | Démarrage rapide |
| CHATBOT_DOCUMENTATION.md | Doc | Docs techniques |
| USAGE_GUIDE.md | Doc | Utilisation pratique |
| IMPLEMENTATION_SUMMARY.md | Doc | Architecture |
| VERIFICATION_CHECKLIST.md | Checklist | Vérification |
| chat-examples.js | Code | Exemples frontend |
| test-chat.php | Code | Tests automatisés |
| setup-chat.ps1 | Script | Installation Windows |
| setup-chat.sh | Script | Installation Linux |

---

## ✅ STATUS

- ✅ Migrations créées
- ✅ Modèles créés
- ✅ Contrôleur complet
- ✅ Configuration complète
- ✅ Documentation complète
- ✅ Exemples fournis
- ✅ Tests automatisés
- ✅ Scripts installation
- ✅ Prêt pour production (après vérifications)

---

**Crée pour Amadtech AI**  
**Date:** 2025  
**Version:** 1.0  
**Status:** ✅ COMPLÈTE

---

## 🎉 C'EST PRÊT!

```bash
php artisan migrate
php artisan serve
# → http://localhost:8000/chat
```

**Bon développement! 🚀**
