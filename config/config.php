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