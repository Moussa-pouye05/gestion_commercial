<?php
function requireRole($role){
    if(!isset($_SESSION['user']) || $_SESSION['user']['role']!==$role){
        header("location: ./accueil_view.php");
        exit;
    }
}