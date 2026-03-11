<?php
// $content = le fichier à afficher dans <main>
//npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <title><?= $title ?? "Dashboard" ?></title>
</head>
<body class="bg-gray-100">
  <?php include "../components/nav.php"; ?>
  <?php include "../components/header.php"; ?>
  <main class="w-full md:w-[80%] ml-0 md:ml-[20%] lg:w-[82%] lg:ml-[18%] mt-12 py-[22px] px-2 h-screen bg-gray-100">
      <?php include $content; ?>
  </main>
  <script src="../js/script.js"></script>
  <script src="../js/commande.js"></script>
  <script src="../js/vendeur.js"></script>
  <script src="../js/client.js"></script>
  <script src="../js/gestion_user.js"></script>
  <script src="../js/gestion_client.js"></script>
  <script src="../js/gestion_fourn.js"></script>
  <script src="../js/gestion_produit.js"></script>
 
</body>
</html>