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
$whereDate = ""; // Can add date filter later if needed
$params = []; // For date if added

if (!empty($vendeur["success"]) && isset($vendeur["vendeurs"])) {
    foreach($vendeur["vendeurs"] as $c){
        // Calculate performance for vendeur role only
        $perf = ['commandes' => 0, 'montant_total' => 0, 'performance' => 0];
        if ($c->getRole() === 'vendeur') {
            $sqlPerf = "SELECT COUNT(c.id) AS commandes, COALESCE(SUM(c.total),0) AS montant_total, 
                       COALESCE(SUM(c.total)/NULLIF(COUNT(c.id),0),0) AS performance
                       FROM commandes c WHERE c.id_user = :id " . $whereDate;
            $stmtPerf = $pdo->prepare($sqlPerf);
            $stmtPerf->bindValue(':id', $c->getId());
            foreach($params as $k => $v) $stmtPerf->bindValue($k, $v);
            $stmtPerf->execute();
            $perfData = $stmtPerf->fetch(PDO::FETCH_ASSOC);
            $perf = [
                'commandes' => (int)($perfData['commandes'] ?? 0),
                'montant_total' => (float)($perfData['montant_total'] ?? 0),
                'performance' => (float)($perfData['performance'] ?? 0)
            ];
        }
        $result[] = [
            "id" => $c->getId(),
            "profile" => $c->getProfile(),
            "nom" => $c->getNom(),
            "email" => $c->getEmail(),
            "telephone" => $c->getTelephone(),
            "role" => $c->getRole(),
            "perf_commandes" => $perf['commandes'],
            "perf_montant" => $perf['montant_total'],
            "perf_avg" => $perf['performance']
        ];
    }
}

echo json_encode([
    "vendeurs" =>$result,
    "totalPages" => $totalPages,
    "currentPage" => $page
]);
exit;
