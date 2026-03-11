<?php
header("Content-Type: application/json");
require_once "../config/config.php";

spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$success = false;
$message = "";

$id = trim($_POST['id'] ?? "");
$nom = trim($_POST['nomEdit'] ?? "");
$telephone = trim($_POST['telephoneEdit'] ?? "");
$adresse = trim($_POST['adresseEdit'] ?? "");


if(empty($id)){
    echo json_encode(["success"=>false,"message"=>"L'id est requis"]);
    exit;
}

if(empty($nom)){
    echo json_encode(["success"=>false,"message"=>"Le nom est requis"]);
    exit;
}

if(empty($telephone)){
    echo json_encode(["success"=>false,"message"=>"Le numero de telephone est requise"]);
    exit;
}
if(empty($adresse)){
    echo json_encode(["success"=>false,"message"=>"L'adresse est requise"]);
    exit;
}



$con = new ClientManager($pdo);
$client = new Client((int)$id, $nom, $telephone,$adresse);

$result = $con->editClient($client);

if(!$result){
    echo json_encode(["success"=>false,"message"=>"Erreur lors de la modification du client"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Client modifié avec succès"
]);
