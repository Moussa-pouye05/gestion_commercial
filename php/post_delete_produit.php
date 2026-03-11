<?php
header("Content-Type: application/json");
require_once "../config/config.php";

spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$reponse = ["success" => false, "message" => ""];

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $reponse['message'] = "ID invalide";
    echo json_encode($reponse);
    exit;
}

try {
    $con = new ProduitManager($pdo);
    $result = $con->deleteProduit($id);
    echo json_encode($result);
} catch (PDOException $e) {
    $reponse['message'] = "Erreur lors de la suppression: " . $e->getMessage();
    echo json_encode($reponse);
}

