<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$con = new ClientManager($pdo);
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = trim($_GET['search'] ?? "");
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$vendeur = $con->loadClient($limit, $offset, $search);
/* total vendeurs */
$totalVendeurs = $con->countClients($search);
$totalPages = ceil($totalVendeurs / $limit);

$result = [];

if (!empty($vendeur["success"]) && isset($vendeur["clients"])) {
    foreach($vendeur["clients"] as $c){
        $result[] = [
            "id" => $c->getId(),
            "nom" => $c->getNom(),
            "telephone" => $c->getTelephone(),
            "adresse" => $c->getAdresse()
        ];
    }
}

echo json_encode([
    "clients" =>$result,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
exit;
