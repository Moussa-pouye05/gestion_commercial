<?php
session_start();
require_once "../config/config.php";
require_once "../classes/CommandeManager.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de commande manquant']);
        exit;
    }

    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
    $commandeManager = new CommandeManager($pdo);

    $commande = $commandeManager->getCommande((int) $data['id']);
    
    if (!$commande) {
        echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
        exit;
    }

    $details = $commandeManager->getCommandeDetails((int) $data['id']);
    
    // Get client info
    $clientReq = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
    $clientReq->execute([":id" => $commande->getIdClient()]);
    $client = $clientReq->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'commande' => [
            'id' => $commande->getId(),
            'date_commande' => $commande->getDateCommande(),
            'id_user' => $commande->getIdUser(),
            'etat' => $commande->getEtat(),
            'id_client' => $commande->getIdClient(),
            'total' => $commande->getTotal(),
            'client' => $client,
            'details' => $details['details'] ?? []
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

