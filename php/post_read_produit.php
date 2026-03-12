<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$limit = 8;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$search = trim($_GET['search'] ?? "");
$categorieId = isset($_GET['categorie']) ? (int) $_GET['categorie'] : 0;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

$con = new ProduitManager($pdo);
$produits = $con->loadProduit($limit, $offset, $search, $categorieId);
$totalProduits = $con->countProduits($search, $categorieId);
$totalPages = (int) ceil($totalProduits / $limit);

$result = [];
if (!empty($produits["success"]) && isset($produits["produits"])) {
    foreach ($produits['produits'] as $p) {
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
}

echo json_encode([
    "produits" => $result,
    "totalPages" => $totalPages,
    "currentPage" => $page,
    "totalProduits" => $totalProduits
]);
