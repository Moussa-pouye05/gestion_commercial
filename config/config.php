<?php
$host = "localhost";
$user = "root";
$dbname = "gestion_stock";
$password = "12345";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$password);
    
} catch (PDOException $th) {
    die("Erreur".$th->getMessage());
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'G-STOCK');
}

if (!defined('APP_MAIL_FROM')) {
    define('APP_MAIL_FROM', 'no-reply@gestion-commerciale.local');
}
