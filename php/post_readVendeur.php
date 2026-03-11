<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$con = new UserManager($pdo);
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = trim($_GET['search'] ?? "");
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$vendeur = $con->loadVendeur($limit, $offset, $search);
/* total vendeurs */
$totalVendeurs = $con->countVendeurs($search);
$totalPages = ceil($totalVendeurs / $limit);

$result = [];

if (!empty($vendeur["success"]) && isset($vendeur["vendeurs"])) {
    foreach($vendeur["vendeurs"] as $c){
        $result[] = [
            "id" => $c->getId(),
            "profile" => $c->getProfile(),
            "nom" => $c->getNom(),
            "email" => $c->getEmail(),
            "telephone" => $c->getTelephone(),
            "role" => $c->getRole()
        ];
    }
}

echo json_encode([
    "vendeurs" =>$result,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
exit;
