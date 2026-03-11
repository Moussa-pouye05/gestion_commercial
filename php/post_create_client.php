<?php
//header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$nom = trim($_POST['nom'] ?? "");
$telephone = trim($_POST['telephone'] ?? "");
$adresse = trim($_POST['adresse'] ?? "");

$reponse = ["success" => false,"message" => ""];
if( empty($nom)){
    $reponse["message"] = "Le nom est requis";
}elseif(empty($telephone)){
    $reponse["message"] = "Le numero de telephone est requis";
}elseif(empty($adresse)){
    $reponse['message'] = "L'adresse est requis";
}else{
    
    $client = new Client(null,$nom,$telephone,$adresse);
    $clientManager = new ClientManager($pdo);
    $reponse = $clientManager->createClient($client);
    
}
//return json
echo json_encode($reponse);
