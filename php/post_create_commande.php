<?php
session_start();
require_once "../config/config.php";
require_once "../classes/Commande.php";
require_once "../classes/CommandeManager.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id_client']) || empty($data['id_client'])) {
        echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner un client']);
        exit;
    }

    if (!isset($data['produits']) || empty($data['produits'])) {
        echo json_encode(['success' => false, 'message' => 'Veuillez ajouter au moins un produit']);
        exit;
    }

    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
    $commandeManager = new CommandeManager($pdo);

    $date_commande = date('Y-m-d H:i:s');
    $id_user = $_SESSION['user']['id'];
    $etat = 'en_cours';
    $id_client = (int) $data['id_client'];
    $total = 0;

    // Calculate total
    foreach ($data['produits'] as $produit) {
        $total += $produit['quantite'] * $produit['prix'];
    }

    $commande = new Commande(
        null,
        $date_commande,
        $id_user,
        $etat,
        $id_client,
        $total
    );

    $details = [];
    foreach ($data['produits'] as $produit) {
        $details[] = [
            'quantite' => (int) $produit['quantite'],
            'prix' => (float) $produit['prix'],
            'sous_total' => (float) ($produit['quantite'] * $produit['prix']),
            'id_produit' => (int) $produit['id_produit']
        ];
    }

    $result = $commandeManager->createCommande($commande, $details);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

