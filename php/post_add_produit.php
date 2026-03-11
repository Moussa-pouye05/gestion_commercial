<?php
header("Content-Type: application/json");
require_once "../config/config.php";

spl_autoload_register(function ($class) {
    require_once "../classes/" . $class . ".php";
});

$nom = trim($_POST['nom'] ?? "");
$prix_vente = trim($_POST['prix_vente'] ?? "");
$prix_achat = trim($_POST['prix_achat'] ?? "");
$quantite = trim($_POST['quantite'] ?? "");
$catId = (int) ($_POST['categorie'] ?? 0);
$codeBarre = trim($_POST['code_barre'] ?? "");

$reponse = ["success" => false, "message" => ""];

if ($nom === "") {
    $reponse['message'] = "Le nom est requis";
} elseif ($prix_achat === "" || !is_numeric($prix_achat)) {
    $reponse['message'] = "Le prix d'achat est invalide";
} elseif ($prix_vente === "" || !is_numeric($prix_vente)) {
    $reponse['message'] = "Le prix de vente est invalide";
} elseif ($quantite === "" || !is_numeric($quantite)) {
    $reponse['message'] = "La quantite est invalide";
} elseif ($catId <= 0) {
    $reponse['message'] = "Categorie non valide";
} elseif (!isset($_FILES["profile_picture"]) || $_FILES["profile_picture"]["error"] !== 0) {
    $reponse["message"] = "L'image du produit est requise";
} else {
    $uploadDir = "../uploads/profiles/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES["profile_picture"]["tmp_name"];
    $originalName = $_FILES["profile_picture"]["name"];
    $size = $_FILES["profile_picture"]["size"];

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        $reponse["message"] = "Format invalide. Formats acceptes: jpg, jpeg, png, webp";
        echo json_encode($reponse);
        exit;
    }

    if ($size > 2 * 1024 * 1024) {
        $reponse["message"] = "Image trop grande (max 2MB)";
        echo json_encode($reponse);
        exit;
    }

    $newFileName = uniqid("profile_", true) . "." . $ext;
    $profile = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $profile)) {
        $reponse["message"] = "Erreur lors de l'envoi de l'image";
        echo json_encode($reponse);
        exit;
    }

    $con = new ProduitManager($pdo);
    $produit = new Produit(
        null,
        $profile,
        $nom,
        (float) $prix_vente,
        (float) $prix_achat,
        (int) $quantite,
        $catId,
        $codeBarre
    );

    $reponse = $con->createProduit($produit);
}

echo json_encode($reponse);
