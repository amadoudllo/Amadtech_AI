# ✅ CHECKLIST DE VÉRIFICATION - CHATBOT GROQ

## 📋 Configuration initiale

- [ ] **Clé API Groq**
  - [ ] Compte créé sur https://console.groq.com
  - [ ] Clé API obtenue
  - [ ] Clé copiée dans `.env` → `GROQ_API_KEY`

- [ ] **Base de données**
  - [ ] MySQL en cours d'exécution
  - [ ] Base `amadtech_ai` créée
  - [ ] Connexion testée dans `.env` (DB_HOST, DB_USERNAME, DB_PASSWORD)

- [ ] **Laravel**
  - [ ] PHP 8.2+ installé
  - [ ] Laravel 11 installé
  - [ ] Dépendances installées (`composer install`)
  - [ ] `.env` configuré

---

## 🔧 Installation fichiers

- [ ] **Migrations**
  - [ ] `database/migrations/2025_01_01_000001_create_conversations_table.php` ✓
  - [ ] `database/migrations/2025_01_01_000002_create_messages_table.php` ✓

- [ ] **Modèles**
  - [ ] `app/Models/Conversation.php` ✓
  - [ ] `app/Models/Message.php` ✓

- [ ] **Contrôleurs**
  - [ ] `app/Http/Controllers/ChatController.php` ✓
  - [ ] `app/Http/Controllers/ChatHelper.php` ✓

- [ ] **Configuration**
  - [ ] `config/groq.php` ✓
  - [ ] `.env` modifié ✓

---

## 📦 Exécution migrations

### Étape 1: Vérifier les migrations
```bash
php artisan migrate:status
```

**Résultat attendu:**
```
2025_01_01_000001_create_conversations_table: No
2025_01_01_000002_create_messages_table: No
```

- [ ] Migrations affichées dans la liste

### Étape 2: Exécuter les migrations
```bash
php artisan migrate
```

**Résultat attendu:**
```
Migrating: 2025_01_01_000001_create_conversations_table
Migrated: 2025_01_01_000001_create_conversations_table
Migrating: 2025_01_01_000002_create_messages_table
Migrated: 2025_01_01_000002_create_messages_table
```

- [ ] Migrations exécutées sans erreur

### Étape 3: Vérifier les tables
```bash
php artisan tinker

> DB::table('conversations')->count();
> DB::table('messages')->count();
```

**Résultat attendu:** `0` (tables vides)

- [ ] Tables créées avec succès
- [ ] Tables accessibles

---

## 🧪 Tests fonctionnels

### Test 1: Créer une conversation
```bash
php artisan tinker

> $conv = App\Models\Conversation::create(['user_id' => null, 'title' => 'Test']);
> $conv->id
```

**Résultat attendu:** ID retourné (ex: 1)

- [ ] Conversation créée en BD
- [ ] ID retourné correct

### Test 2: Ajouter un message
```bash
> $conv->messages()->create(['role' => 'user', 'content' => 'Bonjour']);
```

**Résultat attendu:** Message créé

- [ ] Message sauvegardé
- [ ] Relation fonctionnelle

### Test 3: Récupérer contexte
```bash
> $conv->getContextMessages();
```

**Résultat attendu:** Collection de messages

- [ ] Sliding window fonctionne
- [ ] Messages en ordre chronologique

### Test 4: Script test
```bash
php test-chat.php
```

**Résultat attendu:**
```
✨ ALL TESTS PASSED!
```

- [ ] Tous les tests passent

---

## 🌐 Test API

### Test 1: Accéder au chat
```
http://localhost:8000/chat
```

**Résultat attendu:** Page du chat affichée

- [ ] Route `/chat` fonctionnelle
- [ ] Page s'affiche

### Test 2: Tester l'endpoint
```bash
curl -X POST http://localhost:8000/chat/send \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN" \
  -d '{"message":"Bonjour","conversation_id":null}'
```

**Résultat attendu:**
```json
{
  "success": true,
  "reply": "Bonjour! ...",
  "conversation_id": 1
}
```

- [ ] Endpoint retourne JSON
- [ ] `success` = true
- [ ] `reply` non vide
- [ ] `conversation_id` retourné

### Test 3: Vérifier la persistance
```bash
php artisan tinker

> ChatHelper::showConversations();
```

**Résultat attendu:**
```
📌 Conversation ID: 1
   Title: Bonjour
   Messages: 2
```

- [ ] Conversation sauvegardée
- [ ] Messages sauvegardés

---

## 🐛 Vérification erreurs

### Logs
```bash
tail -f storage/logs/laravel.log
```

- [ ] Pas d'erreurs critiques
- [ ] Logs propres

### PHP
```bash
php -l app/Models/Conversation.php
php -l app/Models/Message.php
php -l app/Http/Controllers/ChatController.php
```

**Résultat attendu:** `No syntax errors detected`

- [ ] Pas d'erreurs PHP

### Migrations
```bash
php artisan migrate:status
```

**Résultat attendu:** `Yes` pour les nouvelles migrations

- [ ] Migrations status à jour

---

## 📊 Tests de charge (Optionnel)

### Créer 100 messages
```bash
php artisan tinker

> for ($i = 0; $i < 100; $i++) {
    App\Models\Message::create([
      'conversation_id' => 1,
      'role' => $i % 2 == 0 ? 'user' : 'assistant',
      'content' => 'Message test ' . $i
    ]);
  }
```

- [ ] 100 messages créés
- [ ] Performance acceptable

### Vérifier sliding window
```bash
> ChatHelper::showContextMessages(1);
```

**Résultat attendu:** 10 messages max

- [ ] Limite contexte respectée

---

## 🎯 Configuration finale

- [ ] **Environment**
  - [ ] `APP_ENV=local` ou `production`
  - [ ] `APP_DEBUG=true` (dev) ou `false` (prod)
  - [ ] `GROQ_API_KEY` configurée

- [ ] **Database**
  - [ ] Connexion établie
  - [ ] Tables créées
  - [ ] Indexes présents

- [ ] **Routes**
  - [ ] GET /chat → affiche interface
  - [ ] POST /chat/send → accepte messages
  - [ ] / → redirige vers /chat

---

## 📚 Documentation

- [ ] **CHATBOT_DOCUMENTATION.md** - Docs complètes
- [ ] **IMPLEMENTATION_SUMMARY.md** - Architecture
- [ ] **USAGE_GUIDE.md** - Guide pratique
- [ ] **QUICK_START.md** - Démarrage rapide
- [ ] **test-chat.php** - Script test
- [ ] **chat-examples.js** - Exemples frontend

---

## 🎉 PRÊT À L'EMPLOI

Si TOUS les points sont cochés ✅:

```bash
php artisan serve

# Puis accéder à:
http://localhost:8000/chat
```

### Premiers messages à tester:
1. "Bonjour" → Vérifie création conversation
2. "Comment ça marche?" → Vérifie contexte
3. "Explique-moi X" → Vérifie réponses longues

---

## 🆘 En cas de problème

### "Groq API key is not configured"
1. Vérifier `.env` → `GROQ_API_KEY`
2. Redémarrer le serveur
3. Vérifier avec: `php artisan tinker` → `env('GROQ_API_KEY')`

### "Table doesn't exist"
1. Vérifier migrations: `php artisan migrate:status`
2. Exécuter: `php artisan migrate`
3. Vérifier: `php artisan migrate:status`

### "Unauthorized (401)"
1. Clé API invalide → Obtenir nouvelle sur console.groq.com
2. Vérifier format: doit commencer par `gsk_`

### Performance lente
1. Vérifier limite tokens/min (7,900)
2. Réduire `max_completion_tokens`
3. Réduire limite contexte

---

## ✨ Fonctionnalités validées

- [x] Création conversations
- [x] Sauvegarde messages
- [x] Contexte sliding window
- [x] System prompt
- [x] Appel API Groq
- [x] Titre auto-généré
- [x] Gestion erreurs
- [x] Logging
- [x] Typage PHP
- [x] Relations Eloquent
- [x] CSRF Protection
- [x] Timestamps

---

**Checklist crée à: 2025**  
**Status: ✅ PRÊT**  
**Avant de déployer en prod:** Mettre `APP_DEBUG=false`
