<?php
header("Content-Type: application/json");
require_once "../config/config.php";

try {
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search_fournisseur = isset($_GET['search_fournisseur']) ? trim($_GET['search_fournisseur']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(DISTINCT a.id) as total FROM approvisionnements a 
             LEFT JOIN fournisseurs f ON a.id_fournisseur = f.id 
             " . ($search_fournisseur ? "WHERE f.nom LIKE '%" . $pdo->quote($search_fournisseur) . "%'" : "");
$countStmt = $pdo->query($countSql);
$totalCount = $countStmt->fetchColumn();
$totalPages = ceil($totalCount / $limit);

$whereClause = $search_fournisseur ? "WHERE f.nom LIKE '%" . $pdo->quote($search_fournisseur) . "%'" : '';
$sql = "SELECT 
            a.id,
            a.date_approvisionnement,
            a.status,
            f.nom as fournisseur_nom,
            GROUP_CONCAT(CONCAT(p.nom, ' (', ad.quantite, ')') SEPARATOR ', ') as produits,
            SUM(ad.quantite) as total_quantite
        FROM approvisionnements a
        LEFT JOIN fournisseurs f ON a.id_fournisseur = f.id
        LEFT JOIN detailappro ad ON a.id = ad.id_appro
        LEFT JOIN poduits p ON ad.id_produit = p.id
        $whereClause
        GROUP BY a.id
        ORDER BY a.date_approvisionnement DESC
        LIMIT $limit OFFSET $offset";
    
    $req = $pdo->query($sql);
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "approvisionnements" => $result,
        "current_page" => $page,
        "total_pages" => $totalPages,
        "total_count" => $totalCount,
        "per_page" => $limit
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur: " . $e->getMessage()
    ]);
}

