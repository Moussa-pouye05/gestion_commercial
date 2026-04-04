<?php

session_start();
require_once "../config/config.php";
require_once "../classes/CommandeManager.php";
require_once "../classes/NotificationManager.php";

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
    $userId = $_SESSION['user']['role'] === 'admin' ? null : (int) $_SESSION['user']['id'];
    $currentCommande = $commandeManager->getCommande((int) $id, $userId);
    if (!$currentCommande) {
        echo json_encode([
            'success' => false,
            'message' => 'Commande introuvable'
        ]);
        exit;
    }

    if (($_SESSION['user']['role'] ?? '') === 'admin' && (int) $currentCommande->getIdUser() !== (int) ($_SESSION['user']['id'] ?? 0)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vous ne pouvez pas valider une commande créée par un autre utilisateur'
        ]);
        exit;
    }

    $result = $commandeManager->clotureCommande((int)$id, $userId);

    if (!empty($result['success']) && (($_SESSION['user']['role'] ?? '') === 'vendeur')) {
        try {
            $notificationManager = new NotificationManager($pdo);
            $notificationManager->ensureTable();

            $reference = 'CMD-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $total = (float) $currentCommande->getTotal();

            $notificationManager->createAdminNotification('commande_vendeur', [
                'title' => 'Commande validée par le vendeur',
                'message' => sprintf(
                    '%s a validé la commande %s pour un montant de %s FCFA.',
                    $_SESSION['user']['nom'] ?? 'Un vendeur',
                    $reference,
                    number_format($total, 0, ',', ' ')
                ),
                'reference' => $reference,
                'commande_id' => (int) $id,
                'vendeur_nom' => $_SESSION['user']['nom'] ?? 'Vendeur',
                'total' => $total
            ], (int) $_SESSION['user']['id']);
        } catch (Throwable $e) {
            error_log('Notification validation commande vendeur: ' . $e->getMessage());
        }
    }

    if (!empty($result['success']) && !empty($result['low_stock_products']) && is_array($result['low_stock_products'])) {
        try {
            $notificationManager = new NotificationManager($pdo);
            $notificationManager->ensureTable();

            $produits = array_map(function ($produit) {
                return sprintf(
                    '%s (%d restant(s), seuil %d)',
                    $produit['nom'] ?? 'Produit',
                    (int) ($produit['quantite'] ?? 0),
                    (int) ($produit['seuil'] ?? 0)
                );
            }, $result['low_stock_products']);

            $notificationManager->createAdminNotification('stock_bas', [
                'title' => 'Alerte stock bas',
                'message' => 'Produits à surveiller: ' . implode(', ', $produits),
                'products' => $result['low_stock_products'],
                'commande_id' => (int) $id
            ], (int) ($_SESSION['user']['id'] ?? 0));
        } catch (Throwable $e) {
            error_log('Notification stock bas: ' . $e->getMessage());
        }
    }

    echo json_encode($result);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
