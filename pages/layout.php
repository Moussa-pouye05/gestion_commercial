<?php
// $content = le fichier à afficher dans <main>
//npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
session_start();
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function () {
      try {
        var t = localStorage.getItem('theme');
        if (t === 'dark') {
          document.documentElement.classList.add('dark');
          document.documentElement.style.colorScheme = 'dark';
        } else if (t === 'light') {
          document.documentElement.classList.remove('dark');
          document.documentElement.style.colorScheme = 'light';
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          document.documentElement.classList.add('dark');
          document.documentElement.style.colorScheme = 'dark';
        }
      } catch (e) {}
    })();
  </script>
  <link rel="stylesheet" href="../src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <title><?= $title ?? "Dashboard" ?></title>
</head>
<body class="min-h-full bg-slate-100 text-slate-900 antialiased transition-colors duration-200 dark:bg-slate-800 dark:text-slate-100">
  <?php include "../components/nav.php"; ?>
  <?php include "../components/header.php"; ?>

  <main class="relative ml-0 mt-12 min-h-screen w-full bg-slate-100 px-2 py-[22px] text-inherit transition-colors duration-200 dark:bg-slate-800 md:ml-[20%] md:w-[80%] lg:ml-[18%] lg:w-[82%]">
      <?php include $content; ?>
  </main>

  <script src="../js/script.js"></script>
  <script src="../js/notifications.js"></script>
  <script src="../js/accueil_vendeur.js"></script>

  <script src="../js/commande.js"></script>

  <script src="../js/vendeur.js"></script>
  <script src="../js/client.js"></script>
  <script src="../js/gestion_user.js"></script>
  <script src="../js/gestion_client.js"></script>
  <script src="../js/gestion_fourn.js"></script>
  <script src="../js/gestion_produit.js"></script>
  <script src="../public/js/chart.js"></script>

  <script>
    (function () {
      function setTheme(theme) {
        var isDark = theme === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        try {
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (e) {}
      }

      function toggleTheme() {
        setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
      }

      var saved = localStorage.getItem('theme');
      if (saved === 'dark' || saved === 'light') {
        setTheme(saved);
      }

      var themeToggle = document.getElementById('theme-toggle');
      if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
      }
    })();
  </script>
</body>
</html>
