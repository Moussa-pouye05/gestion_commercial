<?php
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
?>
<!-- header.php -->
<header>
  <div class="fixed top-0 z-20 right-0 left-0 md:left-[280px] h-16 bg-white shadow-lg flex items-center justify-between px-4 transition-all duration-300">
    
    <!-- Menu Toggle Mobile -->
    <button id="menu-toggle" class="md:hidden w-10 h-10 rounded-lg hover:bg-gray-100 transition flex items-center justify-center">
      <i class="fa-solid fa-bars text-xl text-gray-600"></i>
    </button>
    
    <!-- Titre dynamique -->
    <div class="hidden md:block">
      <h1 class="text-lg font-semibold text-gray-800">
        <?php 
          $page = basename($_SERVER['PHP_SELF'], '.php');
          $titles = [
            'accueil_view' => 'Tableau de bord',
            'accueil_vendeur_view' => 'Tableau de bord Vendeur',
            'vendeur_view' => 'Gestion des vendeurs',
            'produit_view' => 'Gestion des produits',
            'fournisseur_view' => 'Gestion des fournisseurs',
            'client_view' => 'Gestion des clients',
            'commande_view' => 'Commandes & Factures'
          ];
          echo $titles[$page] ?? 'Gestion Commerciale';
        ?>
      </h1>
    </div>
    
    <!-- Barre de recherche (optionnelle) -->
    <div class="hidden md:flex items-center bg-gray-50 rounded-lg px-3 py-2 w-80">
      <i class="fa-solid fa-search text-gray-400"></i>
      <input type="text" placeholder="Rechercher..." class="bg-transparent border-none outline-none text-sm ml-2 w-full text-gray-600">
    </div>
    
    <!-- Actions Utilisateur -->
    <div class="flex items-center gap-3">
      
      <!-- Bouton Mode Sombre/Clair -->
      <button id="theme-toggle" class="w-10 h-10 rounded-lg hover:bg-gray-100 transition flex items-center justify-center relative">
        <i class="fa-solid fa-sun text-yellow-500 text-xl hidden dark:block"></i>
        <i class="fa-solid fa-moon text-gray-600 text-xl block dark:hidden"></i>
      </button>
      
      <!-- Notifications -->
      <div class="relative">
        <button id="notif-btn" class="w-10 h-10 rounded-lg hover:bg-gray-100 transition flex items-center justify-center relative">
          <i class="fa-solid fa-bell text-gray-600 text-xl"></i>
          <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        </button>
        
        <!-- Dropdown Notifications -->
        <div id="notif-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-30">
          <div class="p-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Notifications</h3>
          </div>
          <div class="max-h-80 overflow-y-auto">
            <div class="p-3 hover:bg-gray-50 transition cursor-pointer">
              <p class="text-sm text-gray-600">Nouvelle commande #CMD-015</p>
              <p class="text-xs text-gray-400 mt-1">Il y a 5 minutes</p>
            </div>
            <div class="p-3 hover:bg-gray-50 transition cursor-pointer">
              <p class="text-sm text-gray-600">Stock faible: iPhone 13</p>
              <p class="text-xs text-gray-400 mt-1">Il y a 1 heure</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Séparateur -->
      <div class="h-8 w-px bg-gray-200 mx-1"></div>
      
      <!-- Profil -->
      <div class="relative">
        <div class="flex items-center gap-3 cursor-pointer group" id="profile-btn">
          <div class="relative">
            <img src="<?= htmlspecialchars($_SESSION['user']['photo_profil'] ?? 'https://ui-avatars.com/api/?background=3B82F6&color=fff&name=' . urlencode($_SESSION['user']['nom'] ?? 'User')) ?>" 
                 alt="profil" 
                 class="w-10 h-10 rounded-full object-cover border-2 border-blue-500 group-hover:scale-105 transition-transform">
            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div class="hidden sm:block">
            <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?></p>
            <p class="text-xs text-gray-400"><?= ($_SESSION['user']['role'] ?? 'vendeur') === 'admin' ? 'Administrateur' : 'Vendeur' ?></p>
          </div>
          <i class="fa-solid fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
        </div>
        
        <!-- Dropdown Profil -->
        <div id="profile-dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-30">
          <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
              <img src="<?= htmlspecialchars($_SESSION['user']['photo_profil'] ?? 'https://ui-avatars.com/api/?background=3B82F6&color=fff&name=' . urlencode($_SESSION['user']['nom'] ?? 'User')) ?>" 
                   class="w-12 h-12 rounded-full object-cover">
              <div>
                <p class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?></p>
                <p class="text-xs text-gray-500"><?= $_SESSION['user']['email'] ?? '' ?></p>
              </div>
            </div>
          </div>
          <div class="py-2">
            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
              <i class="fa-solid fa-user"></i> Mon profil
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
              <i class="fa-solid fa-gear"></i> Paramètres
            </a>
          </div>
          <div class="border-t border-gray-100 py-2">
            <a href="../pages/deconnection.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition">
              <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<style>
  /* Mode sombre pour le header (si nécessaire) */
  body.dark-mode header .bg-white {
    background-color: #1e293b;
  }
  body.dark-mode header .text-gray-800,
  body.dark-mode header .text-gray-700,
  body.dark-mode header .text-gray-600 {
    color: #e2e8f0;
  }
  body.dark-mode header .bg-gray-50 {
    background-color: #0f172a;
  }
  body.dark-mode header .border-gray-100 {
    border-color: #334155;
  }
  body.dark-mode header .hover\:bg-gray-100:hover {
    background-color: #334155;
  }
</style>

<script>
  // Toggle menu mobile
  const menuToggle = document.getElementById('menu-toggle');
  const sidbare = document.getElementById('sidebar');
  
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      if (sidbare.style.width === '280px') {
        sidbare.style.width = '0';
      } else {
        sidbare.style.width = '280px';
      }
    });
  }
  
  // Dropdown notifications
  const notifBtn = document.getElementById('notif-btn');
  const notifDropdown = document.getElementById('notif-dropdown');
  
  if (notifBtn) {
    notifBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      notifDropdown.classList.toggle('hidden');
      if (profileDropdown) profileDropdown.classList.add('hidden');
    });
  }
  
  // Dropdown profil
  const profileBtn = document.getElementById('profile-btn');
  const profileDropdown = document.getElementById('profile-dropdown');
  
  if (profileBtn) {
    profileBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      profileDropdown.classList.toggle('hidden');
      if (notifDropdown) notifDropdown.classList.add('hidden');
    });
  }
  
  // Fermer les dropdowns en cliquant ailleurs
  document.addEventListener('click', function(e) {
    if (notifDropdown && !notifBtn?.contains(e.target)) {
      notifDropdown.classList.add('hidden');
    }
    if (profileDropdown && !profileBtn?.contains(e.target)) {
      profileDropdown.classList.add('hidden');
    }
  });
  
  // Mode sombre/clair
  function setTheme(theme) {
    if (theme === 'dark') {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    } else {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    }
  }
  
  function toggleTheme() {
    if (document.body.classList.contains('dark-mode')) {
      setTheme('light');
    } else {
      setTheme('dark');
    }
  }
  
  const themeToggle = document.getElementById('theme-toggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', toggleTheme);
  }
  
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    setTheme('dark');
  } else if (savedTheme === 'light') {
    setTheme('light');
  }
</script>