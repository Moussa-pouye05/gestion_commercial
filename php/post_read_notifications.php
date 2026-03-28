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
    echo json_encode([
        'success' => true,
        'notifications' => [],
        'unread_count' => 0
    ]);
    exit;
}

try {
    $manager = new NotificationManager($pdo);
    $manager->ensureTable();
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

    echo json_encode([
        'success' => true,
        'notifications' => $manager->getAdminNotifications($limit),
        'unread_count' => $manager->countUnread()
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du chargement des notifications: ' . $e->getMessage()
    ]);
}
