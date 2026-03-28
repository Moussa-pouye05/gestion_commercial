<?php
session_start();
require_once "../config/config.php";
require_once "../classes/Commande.php";
require_once "../classes/CommandeManager.php";
require_once "../classes/NotificationManager.php";

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

    if (!empty($result['success']) && (($_SESSION['user']['role'] ?? '') === 'vendeur')) {
        try {
            $notificationManager = new NotificationManager($pdo);
            $notificationManager->ensureTable();
            $reference = 'CMD-' . str_pad((string) ($result['id'] ?? 0), 3, '0', STR_PAD_LEFT);
            $notificationManager->createAdminNotification('commande_vendeur', [
                'title' => 'Nouvelle commande créée',
                'message' => sprintf(
                    '%s a créé la commande %s pour un montant de %s FCFA.',
                    $_SESSION['user']['nom'] ?? 'Un vendeur',
                    $reference,
                    number_format($total, 0, ',', ' ')
                ),
                'reference' => $reference,
                'commande_id' => $result['id'] ?? null,
                'vendeur_nom' => $_SESSION['user']['nom'] ?? 'Vendeur',
                'total' => $total
            ], (int) $_SESSION['user']['id']);
        } catch (Throwable $e) {
            error_log('Notification commande vendeur: ' . $e->getMessage());
        }
    }

    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

