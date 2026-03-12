<?php
header("Content-Type: application/json");
require_once "../config/config.php";

try {
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
            GROUP BY a.id
            ORDER BY a.date_approvisionnement DESC
            LIMIT 10";
    
    $req = $pdo->query($sql);
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "approvisionnements" => $result
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur: " . $e->getMessage()
    ]);
}

