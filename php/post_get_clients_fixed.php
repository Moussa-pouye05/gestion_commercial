<?php
header('Content-Type: application/json');
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

// if (!isset($_SESSION['user'])) {
//     echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
//     exit;
// }

try {
    $clientManager = new ClientManager($pdo);
    $cls = $clientManager->getClient();
    $result = [];
    foreach($cls['clients'] as $c){
        $result[] = [
            "id" => $c->getId(),
            "nom" => $c->getNom(),
            "telephone" => $c->getTelephone(),
            "adresse" => $c->getAdresse()
        ];
    }

    echo json_encode([
        "clients" => $result
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
