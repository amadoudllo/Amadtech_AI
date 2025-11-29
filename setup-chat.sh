#!/bin/bash
# ============================================
# Script de Configuration du Chatbot Groq
# ============================================
# Ce script configure automatiquement le système de chat
# Exécution: bash setup-chat.sh

echo "🚀 Configuration du Chatbot Groq..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# 1. Vérifier que les variables d'environnement sont définies
echo "✅ Étape 1: Vérification du .env"
if grep -q "GROQ_API_KEY" .env; then
    echo "   ✓ GROQ_API_KEY trouvée"
else
    echo "   ✗ GROQ_API_KEY manquante dans .env"
    exit 1
fi

# 2. Exécuter les migrations
echo ""
echo "✅ Étape 2: Exécution des migrations"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "   ✓ Migrations exécutées avec succès"
else
    echo "   ✗ Erreur lors des migrations"
    exit 1
fi

# 3. Vérifier les tables
echo ""
echo "✅ Étape 3: Vérification des tables"
php artisan tinker << 'EOF'
try {
    $conversations = DB::table('conversations')->count();
    $messages = DB::table('messages')->count();
    echo "   ✓ Table 'conversations' existe\n";
    echo "   ✓ Table 'messages' existe\n";
} catch (Exception $e) {
    echo "   ✗ Tables non trouvées: " . $e->getMessage() . "\n";
    exit(1);
}
exit(0);
EOF

# 4. Test de connexion à l'API Groq (optionnel)
echo ""
echo "✅ Étape 4: Test de l'API Groq (optionnel)"
read -p "   Voulez-vous tester la connexion à l'API Groq? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan tinker << 'EOF'
use Illuminate\Support\Facades\Http;

$apiKey = env('GROQ_API_KEY');
$model = env('GROQ_MODEL', 'mixtral-8x7b-32768');

echo "   Test de connexion...\n";
echo "   API Key: " . substr($apiKey, 0, 10) . "***\n";
echo "   Modèle: $model\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->timeout(10)->post(
        'https://api.groq.com/openai/v1/chat/completions',
        [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => 'Bonjour'],
            ],
            'max_completion_tokens' => 100,
        ]
    );

    if ($response->ok()) {
        echo "   ✓ Connexion réussie!\n";
    } else {
        echo "   ✗ Erreur: " . $response->status() . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Erreur: " . $e->getMessage() . "\n";
}
exit(0);
EOF
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Configuration terminée!"
echo ""
echo "Prochaines étapes:"
echo "  1. Démarrer le serveur: php artisan serve"
echo "  2. Accéder au chat: http://localhost:8000/chat"
echo "  3. Consulter la doc: cat CHATBOT_DOCUMENTATION.md"
