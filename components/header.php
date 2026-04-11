<?php
  if(!$_SESSION['user']){
    header("Location: ../index.php");
  }
?>
<!-- header.php -->
<header>
  <div class="fixed top-0 z-20 right-0 left-0 flex  h-16 items-center justify-between border-b border-slate-200/80 bg-white px-4 shadow-lg transition-all duration-300 dark:border-slate-600/80 dark:bg-slate-700 dark:shadow-slate-950/20 md:left-[280px] md:w-[calc(100%-280px)]">
    
    <!-- Menu Toggle Mobile -->
    <button id="menu-toggle" type="button" class="flex h-10 w-10 items-center justify-center rounded-lg transition hover:bg-gray-100 dark:hover:bg-slate-600 md:hidden">
      <i class="fa-solid fa-bars text-xl text-gray-600 dark:text-slate-300"></i>
    </button>
    
    <!-- Titre dynamique -->
    <div class="hidden md:block">
      <h1 class="text-lg font-semibold text-gray-800 dark:text-slate-100">
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
    <!-- <div class="hidden w-80 items-center rounded-lg bg-gray-50 px-3 py-2 dark:bg-slate-600/40 md:flex">
      <i class="fa-solid fa-search text-gray-400 dark:text-slate-400"></i>
      <input type="text" placeholder="Rechercher..." class="ml-2 w-full border-none bg-transparent text-sm text-gray-600 outline-none placeholder:text-slate-500 dark:text-slate-200">
    </div> -->
    
    <!-- Actions Utilisateur -->
    <div class="flex items-center gap-3">
      
      <!-- Bouton Mode Sombre/Clair -->
      <button id="theme-toggle" type="button" title="Thème clair / sombre" class="relative flex h-10 w-10 items-center justify-center rounded-lg transition hover:bg-gray-100 dark:hover:bg-slate-600">
        <span class="absolute inset-0 flex items-center justify-center dark:hidden" aria-hidden="true">
          <i class="fa-solid fa-moon text-xl text-slate-600"></i>
        </span>
        <span class="absolute inset-0 hidden items-center justify-center dark:flex" aria-hidden="true">
          <i class="fa-solid fa-sun text-xl text-amber-400"></i>
        </span>
      </button>
      
      <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
      <!-- Notifications -->
      <div class="relative">
        <button id="notif-btn" type="button" data-role="<?= htmlspecialchars($_SESSION['user']['role'] ?? 'vendeur') ?>" class="relative flex h-10 w-10 items-center justify-center rounded-lg transition hover:bg-gray-100 dark:hover:bg-slate-600">
          <i class="fa-solid fa-bell text-xl text-gray-600 dark:text-slate-300"></i>
          <span id="notif-dot" class="absolute top-1 right-1 hidden h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
          <span id="notif-count" class="absolute -right-1 -top-1 hidden min-w-[18px] rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white"></span>
        </button>
        
        <!-- Dropdown Notifications -->
        <div id="notif-dropdown" class="absolute right-0 z-30 mt-2 hidden w-80 rounded-xl border border-gray-100 bg-white shadow-2xl dark:border-slate-600 dark:bg-slate-700">
          <div class="flex items-center justify-between border-b border-gray-100 p-3 dark:border-slate-600">
            <h3 class="font-semibold text-gray-800 dark:text-slate-100">Notifications</h3>
            <span class="text-[11px] text-slate-400">Admin</span>
          </div>
          <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
          <div id="notif-empty" class="hidden p-4 text-sm text-slate-500">
            Aucune notification pour le moment.
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <!-- Séparateur -->
      <div class="mx-1 h-8 w-px bg-gray-200 dark:bg-slate-600"></div>
      
      <!-- Profil -->
      <div class="relative">
        <div class="flex items-center gap-3 cursor-pointer group" id="profile-btn">
          <div class="relative">
            <img src="<?= htmlspecialchars($_SESSION['user']['photo_profil'] ?? 'https://ui-avatars.com/api/?background=3B82F6&color=fff&name=' . urlencode($_SESSION['user']['nom'] ?? 'User')) ?>" 
                 alt="profil" 
                 class="w-10 h-10 rounded-full object-cover border-2 border-blue-500 group-hover:scale-105 transition-transform">
            <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500 dark:border-slate-700"></div>
          </div>
          <div class="hidden sm:block">
            <p class="text-sm font-semibold text-gray-700 dark:text-slate-200"><?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?></p>
            <p class="text-xs text-gray-400 dark:text-slate-400"><?= ($_SESSION['user']['role'] ?? 'vendeur') === 'admin' ? 'Administrateur' : 'Vendeur' ?></p>
          </div>
          <i class="fa-solid fa-chevron-down hidden text-xs text-gray-400 dark:text-slate-500 sm:block"></i>
        </div>
        
        <!-- Dropdown Profil -->
        <div id="profile-dropdown" class="absolute right-0 z-30 mt-2 hidden w-64 rounded-xl border border-gray-100 bg-white shadow-2xl dark:border-slate-600 dark:bg-slate-700">
          <div class="border-b border-gray-100 p-4 dark:border-slate-600">
            <div class="flex items-center gap-3">
              <img src="<?= htmlspecialchars($_SESSION['user']['photo_profil'] ?? 'https://ui-avatars.com/api/?background=3B82F6&color=fff&name=' . urlencode($_SESSION['user']['nom'] ?? 'User')) ?>" 
                   class="h-12 w-12 rounded-full object-cover">
              <div>
                <p class="font-semibold text-gray-800 dark:text-slate-100"><?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?></p>
                <p class="text-xs text-gray-500 dark:text-slate-400"><?= $_SESSION['user']['email'] ?? '' ?></p>
              </div>
            </div>
          </div>
          <div class="py-2">
            <a href="#" onclick="alert('Fonctionnalité Mon Profil à implémenter')" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50 dark:text-slate-300 dark:hover:bg-slate-600/50">
              <i class="fa-solid fa-user"></i> Mon profil
            </a>
            <a href="#" onclick="alert('Fonctionnalité Paramètres à implémenter')" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50 dark:text-slate-300 dark:hover:bg-slate-600/50">
              <i class="fa-solid fa-gear"></i> Paramètres
            </a>
          </div>
          <div class="border-t border-gray-100 py-2 dark:border-slate-600">
            <a href="../pages/deconnection.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-500 transition hover:bg-red-50 dark:hover:bg-red-950/30">
              <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<script>   
  // Toggle menu mobile
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      if (sidebar.style.width === '280px') {
        sidebar.style.width = '0';
      } else {
        sidebar.style.width = '280px';
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
  
</script>
