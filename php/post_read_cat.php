<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ProduitManager($pdo);
$cats = $con->getCategorie();
$result = [];
foreach($cats['categorie'] as $cat){
    $result[] = [
        "id" => $cat->getId(),
        "nom" => $cat->getNom()
    ];
}

echo json_encode([
    "categorie" => $result
]);