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

try {
    $manager = new NotificationManager($pdo);
    $manager->ensureTable();
    $manager->markAllAsRead();

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour des notifications: ' . $e->getMessage()
    ]);
}
