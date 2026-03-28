<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$type = $_POST['type'] ?? '';
$data = json_decode($_POST['data'] ?? '{}', true) ?? [];

if (empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Type de notification manquant']);
    exit;
}

require_once '../config/database.php';
require_once '../functions/auth.php';
require_role('admin'); // Admin seulement

// Log notification (admin dashboard peut lire)
$log_data = [
    'type' => $type,
    'data' => json_encode($data),
    'created_at' => date('Y-m-d H:i:s'),
    'vendeur_id' => $_SESSION['user']['id'] ?? null
];

$stmt = $pdo->prepare("INSERT INTO admin_notifications (type, data, created_at, vendeur_id) VALUES (?, ?, ?, ?)");
if ($stmt->execute($log_data)) {
    echo json_encode(['success' => true, 'message' => 'Notification envoyée']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur enregistrement']);
}
?>

