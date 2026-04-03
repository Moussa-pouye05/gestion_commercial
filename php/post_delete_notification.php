<?php
session_start();
header('Content-Type: application/json');

require_once "../config/config.php";
require_once "../classes/NotificationManager.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Accès réservé à l\'administrateur']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$notificationId = isset($_POST['notification_id']) ? (int) $_POST['notification_id'] : 0;

if ($notificationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Notification invalide']);
    exit;
}

try {
    $manager = new NotificationManager($pdo);
    $manager->ensureTable();
    $deleted = $manager->deleteNotification($notificationId);

    echo json_encode([
        'success' => $deleted,
        'message' => $deleted ? 'Notification supprimée' : 'Impossible de supprimer la notification'
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la suppression de la notification: ' . $e->getMessage()
    ]);
}
