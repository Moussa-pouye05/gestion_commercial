<?php
header("Content-Type: application/json");
require_once "../config/config.php";

spl_autoload_register(function ($class) {
    require_once "../classes/" . $class . ".php";
});

$reponse = ["success" => false, "message" => ""];

$fournisseurId = (int) ($_POST['fournisseur'] ?? 0);
$produitsData = $_POST['produits'] ?? "[]";

if ($fournisseurId <= 0) {
    $reponse['message'] = "Le fournisseur est requis";
    echo json_encode($reponse);
    exit;
}

$produits = json_decode($produitsData, true);

if (!is_array($produits) || count($produits) === 0) {
    $reponse['message'] = "Aucun produit n'a été sélectionné";
    echo json_encode($reponse);
    exit;
}

try {
    $pdo->beginTransaction();

    // Create approvisionnement record
    $stmt = $pdo->prepare("INSERT INTO approvisionnements (date_approvisionnement,id_fournisseur,status ,id_admin) 
                           VALUES (NOW(),:fournisseur,  'recu', :admin)");
    $stmt->execute([
        ":fournisseur" => $fournisseurId,
        ":admin" => $_SESSION['user']['id'] ?? 1
    ]);
    $approvisionnementId = $pdo->lastInsertId();

    // Process each product
    foreach ($produits as $produit) {
        $produitId = (int) ($produit['id'] ?? 0);
        $quantite = (int) ($produit['quantite'] ?? 0);
        $prixAchat = (float) ($produit['prix_achat'] ?? 0);

        if ($produitId <= 0 || $quantite <= 0) {
            continue;
        }

        // Insert detail approvisionnement
        $detailStmt = $pdo->prepare("INSERT INTO detailappro 
                                     (quantite,prix_achat,id_produit, id_appro  ) 
                                     VALUES (:qte, :prix, :id_prod, :id_appro)");
        $detailStmt->execute([
            ":id_appro" => $approvisionnementId,
            ":id_prod" => $produitId,
            ":qte" => $quantite,
            ":prix" => $prixAchat
        ]);

        // Update product quantity (add to existing)
        $updateStmt = $pdo->prepare("UPDATE poduits 
                                     SET quantite = quantite + :quantite,
                                         prix_achat = :prix_achat 
                                     WHERE id = :id");
        $updateStmt->execute([
            ":quantite" => $quantite,
            ":prix_achat" => $prixAchat,
            ":id" => $produitId
        ]);
    }

    $pdo->commit();

    $reponse = [
        "success" => true,
        "message" => "Approvisionnement ajouté avec succès",
        "id" => $approvisionnementId
    ];
} catch (PDOException $e) {
    $pdo->rollBack();
    $reponse = [
        "success" => false,
        "message" => "Erreur lors de l'approvisionnement: " . $e->getMessage()
    ];
}

echo json_encode($reponse);

