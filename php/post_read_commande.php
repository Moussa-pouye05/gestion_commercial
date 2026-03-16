<?php
session_start();
require_once "../config/config.php";
require_once "../classes/Commande.php";
require_once "../classes/CommandeManager.php";



if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
    $commandeManager = new CommandeManager($pdo);

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $etat = isset($_GET['etat']) ? $_GET['etat'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $result = $commandeManager->loadCommande($limit, $offset, $search, $etat);
    
    // Get counts by status
    $counts = $commandeManager->getCountByStatus();
    //$result['counts'] = $counts;

    $commandes = [];
    foreach($result['commandes'] as $cmd){
        $commandes[] = [
            "id" => $cmd->getId(),
            "date_commande" => $cmd->getDateCommande(),
            "id_user" => $cmd->getIdUser(),
            "etat" => $cmd->getEtat(),
            "id_client" => $cmd->getIdClient(),
            "total" => $cmd->getTotal(),
            // Ajouter les infos client et user
            "client_nom" => $cmd->client_nom ?? 'N/A',
            "client_telephone" => $cmd->client_telephone ?? '',
            "client_adresse" => $cmd->client_adresse ?? '',
            "user_nom" => $cmd->user_nom ?? ''
        ];
    }
    
   
    echo json_encode([
        "success" => true,
        "commandes" => $commandes,
        "counts" => $counts
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

