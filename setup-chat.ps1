# ============================================
# Script de Configuration du Chatbot Groq (PowerShell)
# ============================================
# Ce script configure automatiquement le système de chat
# Exécution: .\setup-chat.ps1

Write-Host "🚀 Configuration du Chatbot Groq..." -ForegroundColor Cyan
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan

# 1. Vérifier que les variables d'environnement sont définies
Write-Host "`n✅ Étape 1: Vérification du .env"
$envContent = Get-Content ".env" -ErrorAction SilentlyContinue
if ($envContent -match "GROQ_API_KEY") {
    Write-Host "   ✓ GROQ_API_KEY trouvée" -ForegroundColor Green
} else {
    Write-Host "   ✗ GROQ_API_KEY manquante dans .env" -ForegroundColor Red
    exit 1
}

# 2. Exécuter les migrations
Write-Host "`n✅ Étape 2: Exécution des migrations"
php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "   ✓ Migrations exécutées avec succès" -ForegroundColor Green
} else {
    Write-Host "   ✗ Erreur lors des migrations" -ForegroundColor Red
    exit 1
}

# 3. Vérifier les tables
Write-Host "`n✅ Étape 3: Vérification des tables"
$checkOutput = php artisan tinker << 'EOF'
try {
    $conversations = DB::table('conversations')->count();
    $messages = DB::table('messages')->count();
    echo "   ✓ Table 'conversations' existe\n";
    echo "   ✓ Table 'messages' existe\n";
    echo "   Conversations: $conversations\n";
    echo "   Messages: $messages\n";
} catch (Exception $e) {
    echo "   ✗ Tables non trouvées: " . $e->getMessage() . "\n";
    exit(1);
}
exit(0);
EOF

Write-Host $checkOutput

# 4. Afficher le résumé
Write-Host "`n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "✅ Configuration terminée!" -ForegroundColor Green
Write-Host ""
Write-Host "Prochaines étapes:" -ForegroundColor Yellow
Write-Host "  1. Démarrer le serveur: php artisan serve"
Write-Host "  2. Accéder au chat: http://localhost:8000/chat"
Write-Host "  3. Consulter la doc: Get-Content CHATBOT_DOCUMENTATION.md"
Write-Host ""
Write-Host "📚 Fichiers créés:" -ForegroundColor Yellow
Write-Host "  ✓ Migrations: database/migrations/2025_01_01_000001_create_conversations_table.php"
Write-Host "  ✓ Migrations: database/migrations/2025_01_01_000002_create_messages_table.php"
Write-Host "  ✓ Modèles: app/Models/Conversation.php"
Write-Host "  ✓ Modèles: app/Models/Message.php"
Write-Host "  ✓ Contrôleur: app/Http/Controllers/ChatController.php"
Write-Host "  ✓ Helper: app/Http/Controllers/ChatHelper.php"
Write-Host "  ✓ SQL: database/sql/conversations_and_messages.sql"
Write-Host "  ✓ Documentation: CHATBOT_DOCUMENTATION.md"
Write-Host "  ✓ Exemples: resources/js/chat-examples.js"
