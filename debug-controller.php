<?php
/**
 * Test rapide du ChatController
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;

echo "\n🧪 TEST DU CHATCONTROLLER\n";
echo "═════════════════════════════════════════\n\n";

try {
    $controller = new ChatController();
    $request = new Request();
    $request->merge([
        'message' => 'Bonjour, je m\'appelle Jean',
        'conversation_id' => null
    ]);

    echo "📤 Envoi du message...\n";
    $response = $controller->sendMessage($request);
    
    echo "✅ Réponse reçue:\n";
    echo $response->getContent() . "\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "═════════════════════════════════════════\n";
echo "✨ TEST RÉUSSI!\n";
