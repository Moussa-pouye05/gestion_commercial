<?php

session_start();
require_once "../config/config.php";
require_once "../classes/CommandeManager.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de commande manquant'
    ]);
    exit;
}

//$pdo = getConnection();

try {

    $commandeManager = new CommandeManager($pdo);


    $result = $commandeManager->clotureCommande((int)$id);

    echo json_encode($result);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}