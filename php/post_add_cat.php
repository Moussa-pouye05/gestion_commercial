<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$nom = trim($_POST['nom'] ?? "");

$reponse = ["success" => false,"message" => ""];
if( empty($nom) ){
    $reponse['message'] = "Le nom est requis";
}else{
    $categorie = new Categorie(null,$nom);
    $con = new ProduitManager($pdo);
    $reponse = $con->createCat($categorie);
}

echo json_encode($reponse);