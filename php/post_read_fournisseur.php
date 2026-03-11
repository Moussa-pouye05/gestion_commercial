<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$con = new FournisseurManager($pdo);
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = trim($_GET['search'] ?? "");
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$fournisseur = $con->loadFournisseur($limit, $offset, $search);
/* total vendeurs */
$totalFournisseurs = $con->countFournisseurs($search);
$totalPages = ceil($totalFournisseurs / $limit);

$result = [];

if (!empty($fournisseur["success"]) && isset($fournisseur["fournisseurs"])) {
    foreach($fournisseur["fournisseurs"] as $c){
        $result[] = [
            "id" => $c->getId(),
            "nom" => $c->getNom(),
            "telephone" => $c->getTelephone(),
            "adresse" => $c->getAdresse()
        ];
    }
}

echo json_encode([
    "fournisseurs" =>$result,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
exit;
