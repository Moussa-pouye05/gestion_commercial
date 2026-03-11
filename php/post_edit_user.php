<?php
header("Content-Type: application/json");
require_once "../config/config.php";

spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$success = false;
$message = "";

$id = trim($_POST['id'] ?? "");
$nom = trim($_POST['nom'] ?? "");
$email = trim($_POST['email'] ?? "");
$password = trim($_POST['password'] ?? "");
$telephone = trim($_POST['telephone'] ?? "");
$old_profile = trim($_POST['old_profile'] ?? "");
$role = trim($_POST['role'] ?? "");

if(empty($id)){
    echo json_encode(["success"=>false,"message"=>"L'id est requis"]);
    exit;
}

if(empty($nom)){
    echo json_encode(["success"=>false,"message"=>"Le nom est requis"]);
    exit;
}

if(empty($email)){
    echo json_encode(["success"=>false,"message"=>"L'email est requis"]);
    exit;
}

if(empty($telephone)){
    echo json_encode(["success"=>false,"message"=>"L'adresse est requise"]);
    exit;
}

// ✅ par défaut : garder l'ancien profil
$profile = $old_profile;

// ✅ si nouvelle image envoyée
if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {

    $uploadDir = "../uploads/profiles/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES["profile_picture"]["tmp_name"];
    $originalName = $_FILES["profile_picture"]["name"];
    $size = $_FILES["profile_picture"]["size"];

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["success"=>false,"message"=>"Format invalide (jpg, jpeg, png, webp)"]);
        exit;
    }

    if ($size > 2 * 1024 * 1024) {
        echo json_encode(["success"=>false,"message"=>"Image trop grande (max 2MB)"]);
        exit;
    }

    $newFileName = uniqid("profile_", true) . "." . $ext;

    // ✅ chemin web à stocker en DB
    $profile = $uploadDir . $newFileName;

    // ✅ chemin serveur pour move_uploaded_file
   // $destination = "../" . $profile;

    if (!move_uploaded_file($tmpName, $profile)) {
        echo json_encode(["success"=>false,"message"=>"Erreur lors de l'envoi de l'image"]);
        exit;
    }
}

$con = new UserManager($pdo);
$vendeur = new User((int)$id, $profile, $nom, $email, $password, $telephone,$role);

$result = $con->editVendeur($vendeur);

if(!$result){
    echo json_encode(["success"=>false,"message"=>"Erreur lors de la modification du client"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Client modifié avec succès"
]);
