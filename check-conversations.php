<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=amadtech_ai', 'root', '');
$conversations = $db->query('SELECT id, title, user_id FROM conversations ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
echo "Conversations trouvées: " . count($conversations) . "\n\n";
foreach ($conversations as $conv) {
    echo "ID: " . $conv['id'] . " | Titre: " . $conv['title'] . " | User: " . ($conv['user_id'] ?? 'NULL') . "\n";
    $msgCount = $db->query('SELECT COUNT(*) FROM messages WHERE conversation_id = ' . $conv['id'])->fetch(PDO::FETCH_NUM)[0];
    echo "  Messages: $msgCount\n\n";
}
?>
