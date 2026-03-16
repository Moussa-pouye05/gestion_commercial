<?php
//session_start();
header('Content-Type: application/json');
require_once "../config/config.php";
require_once "../classes/ProduitManager.php";
require_once "../classes/Produit.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

try {
    $produitManager = new ProduitManager($pdo);
    $prods = $produitManager->getProduit();
    $result = [];
    foreach($prods['produits'] as $p){
        $result[] = [
            "id" => $p->getId(),
            "image" => $p->getPhoto(),
            "nom" => $p->getNom(),
            "prix_vente" => $p->getPrixVente(),
            "prix_achat" => $p->getPrixAchat(),
            "quantite" => $p->getQuantite(),
            "stock_min" => 100,
            "id_categorie" => $p->getCatId(),
            "code_barre" => $p->getCodeBarre()
        ];
    }
    echo json_encode([
        "produits" => $result
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
