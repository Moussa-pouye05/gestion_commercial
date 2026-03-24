<nav>

  <!-- Sidebar Modernisée -->
  <div id="sidebar" class="sidebar fixed left-0 top-0 z-50 h-full w-0 overflow-hidden border-r border-gray-100 bg-white shadow-2xl transition-all duration-300 dark:border-slate-600 dark:bg-slate-700 sm:w-0 md:w-[20%] lg:w-[18%]">
    
    <!-- En-tête avec logo et bouton de fermeture -->
    <div class="relative pt-6 px-5 pb-4">
      <!-- Bouton de fermeture (croix) - positionné en haut à droite, visible uniquement sur mobile -->
      <button id="close-sidebar-btn" type="button" class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:text-slate-400 dark:hover:bg-slate-600 dark:hover:text-slate-200 md:hidden">
        <i class="fa-solid fa-times text-lg"></i>
      </button>
      
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
          <i class="fa-solid fa-chart-line text-white text-lg"></i>
        </div>
        <div>
          <div class="text-xl font-bold tracking-tight">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">G</span>
            <span class="text-gray-800 dark:text-slate-100">STOCK</span>
          </div>
          <p class="mt-0.5 text-xs text-gray-400 dark:text-slate-400">Gestion commerciale</p>
        </div>
      </div>
    </div>

    <!-- Menu Principal -->
    <div class="px-3 flex-1 overflow-y-auto mt-2" style="height: calc(100vh - 180px);">
      <p class="mb-3 px-4 text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500">Menu principal</p>
      <ul class="space-y-1">
        
        <!-- Accueil - Admin -->
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
        <li>
          <a href="../view/accueil_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-chart-line text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Tableau de bord</span>
            <?php if(basename($_SERVER['PHP_SELF']) == 'accueil_view.php'): ?>
            <span class="ml-auto w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
            <?php endif; ?>
          </a>
        </li>
        <?php endif ?>
        
        <!-- Accueil - Vendeur -->
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'vendeur'):?>
        <li>
          <a href="../view/accueil_vendeur_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-chart-simple text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Tableau de bord</span>
          </a>
        </li>
        <?php endif ?>
        
        <!-- Vendeurs - Admin seulement -->
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
        <li>
          <a href="../view/vendeur_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-users-viewfinder text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Vendeurs</span>
          </a>
        </li>
        <?php endif?>
        
        <!-- Produits -->
        <li>
          <a href="../view/produit_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-box text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Produits</span>
          </a>
        </li>
        
        <!-- Fournisseurs - Admin seulement -->
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
        <li>
          <a href="../view/fournisseur_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-truck-fast text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Fournisseurs</span>
          </a>
        </li>
        <?php endif?>
        
        <!-- Clients -->
        <li>
          <a href="../view/client_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-user-group text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Clients</span>
          </a>
        </li>
        
        <!-- Commandes & Factures -->
        <li>
          <a href="../view/commande_view.php" 
             class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-gray-600 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-600/50 dark:hover:text-blue-400">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition group-hover:bg-blue-100 dark:bg-slate-600 dark:group-hover:bg-slate-500">
              <i class="fa-solid fa-cart-shopping text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="font-medium">Commandes & Factures</span>
          </a>
        </li>
      </ul>

      <!-- Séparateur avec ligne décorative -->
      <div class="relative my-6 px-4">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-200 dark:border-slate-600"></div>
        </div>
        <div class="relative flex justify-center text-xs">
          <span class="bg-white px-2 text-gray-400 dark:bg-slate-700 dark:text-slate-500">Actions rapides</span>
        </div>
      </div>

      <!-- Section Actions Rapides - Conditionnelle -->
      <div class="grid grid-cols-2 gap-2 px-3">
        <!-- Nouvelle commande - Visible pour tous -->
        <a href="../view/commande_view.php?action=add" 
           class="group flex items-center justify-center gap-2 rounded-xl border border-green-100 bg-gradient-to-r from-green-50 to-emerald-50 p-2.5 text-xs font-medium text-green-600 transition hover:border-green-200 hover:shadow-md dark:border-emerald-900/50 dark:from-emerald-950/40 dark:to-emerald-900/30 dark:text-emerald-300">
          <i class="fa-solid fa-plus-circle text-sm group-hover:scale-110 transition-transform"></i>
          <span>Nouvelle commande</span>
        </a>
        
        <!-- Nouveau produit - Visible uniquement pour l'admin -->
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
        <a href="../view/produit_view.php?action=add" 
           class="group flex items-center justify-center gap-2 rounded-xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-2.5 text-xs font-medium text-blue-600 transition hover:border-blue-200 hover:shadow-md dark:border-indigo-900/50 dark:from-indigo-950/40 dark:to-blue-950/30 dark:text-blue-300">
          <i class="fa-solid fa-plus-circle text-sm group-hover:scale-110 transition-transform"></i>
          <span>Nouveau produit</span>
        </a>
        <?php else: ?>
        <!-- Pour le vendeur, un autre raccourci utile -->
        <a href="../view/client_view.php?action=add" 
           class="group flex items-center justify-center gap-2 rounded-xl border border-purple-100 bg-gradient-to-r from-purple-50 to-pink-50 p-2.5 text-xs font-medium text-purple-600 transition hover:border-purple-200 hover:shadow-md dark:border-purple-900/50 dark:from-purple-950/40 dark:to-pink-950/30 dark:text-purple-300">
          <i class="fa-solid fa-user-plus text-sm group-hover:scale-110 transition-transform"></i>
          <span>Nouveau client</span>
        </a>
        <?php endif; ?>
      </div>
      
      <!-- Info supplémentaire pour le vendeur (optionnel) -->
      <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'vendeur'):?>
      <div class="mt-4 px-3">
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-3 dark:border-amber-900/50 dark:bg-amber-950/30">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-bolt text-xs text-amber-500 dark:text-amber-400"></i>
            <p class="text-xs text-amber-700 dark:text-amber-200">Conseil : Créez rapidement des commandes pour vos clients</p>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Footer Sidebar - Déconnexion -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-gray-100 bg-white p-4 dark:border-slate-600 dark:bg-slate-700">
      <a href="../pages/deconnection.php" 
         class="group flex items-center gap-3 rounded-xl px-4 py-3 text-red-500 transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-950/30">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 transition group-hover:bg-red-100 dark:bg-red-950/40 dark:group-hover:bg-red-900/40">
          <i class="fa-solid fa-right-from-bracket text-lg group-hover:translate-x-1 transition-transform"></i>
        </div>
        <span class="font-medium">Déconnexion</span>
      </a>
    </div>
  </div>
</nav>

<style>
  /* Style pour la sidebar */
  .sidebar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
  }
  
  .sidebar::-webkit-scrollbar {
    width: 4px;
  }
  
  .sidebar::-webkit-scrollbar-track {
    background: #f1f5f9;
  }
  
  .sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  
  .sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  
  /* Style pour le lien actif */
  .nav-link.active {
    background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%);
    color: #2563eb;
  }
  
  .nav-link.active div {
    background-color: #bfdbfe !important;
  }
  
  .nav-link.active i {
    color: #2563eb;
  }
  
  /* Animation hover */
  .nav-link:hover div {
    background-color: #dbeafe;
  }

  .dark .nav-link:hover div {
    background-color: rgb(71 85 105 / 0.65);
  }

  .dark .nav-link.active {
    background: linear-gradient(135deg, rgba(30, 64, 175, 0.35) 0%, rgba(67, 56, 202, 0.22) 100%);
    color: #93c5fd;
  }

  .dark .nav-link.active div {
    background-color: rgb(51 65 85) !important;
  }

  .dark .nav-link.active i {
    color: #93c5fd;
  }

  .dark .sidebar {
    scrollbar-color: #64748b #334155;
  }

  .dark .sidebar::-webkit-scrollbar-track {
    background: #334155;
  }

  .dark .sidebar::-webkit-scrollbar-thumb {
    background: #64748b;
  }
  
  /* Assurer que la sidebar est visible sur grand écran avec tes proportions */
  @media (min-width: 768px) {
    .sidebar {
      width: 20% !important;
    }
  }
  
  @media (min-width: 1024px) {
    .sidebar {
      width: 18% !important;
    }
  }
  
  /* Croix cachée sur desktop */
  @media (min-width: 768px) {
    #close-sidebar-btn {
      display: none;
    }
  }
  
  /* Transition fluide */
  .sidebar {
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
</style>

<script>
  // Récupérer les éléments
  const sidebare = document.getElementById('sidebar');
  const closeBtn = document.getElementById('close-sidebar-btn');
  
  // Fermeture de la sidebar sur mobile
  if (closeBtn) {
    closeBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      if (window.innerWidth < 768) {
        sidebare.style.width = '0';
      }
    });
  }
  
  // Gestion du redimensionnement pour garder les proportions
  function updateSidebarWidth() {
    if (window.innerWidth >= 1024) {
      sidebare.style.width = '18%';
    } else if (window.innerWidth >= 768) {
      sidebare.style.width = '20%';
    } else {
      // Sur mobile, la largeur est gérée par ton toggle existant
      if (sidebare.style.width !== '280px') {
        sidebare.style.width = '0';
      }
    }
  }
  
  window.addEventListener('resize', updateSidebarWidth);
  updateSidebarWidth();
  
  // Marquer le lien actif en fonction de la page courante
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    const currentPage = window.location.pathname.split('/').pop();
    
    // Vérifier si le lien correspond à la page actuelle
    if (href && href.includes(currentPage)) {
      link.classList.add('active');
    }
    
    // Pour la page d'accueil (cas spécifique)
    if (currentPage === 'accueil_view.php' && href.includes('accueil_view.php')) {
      link.classList.add('active');
    }
    if (currentPage === 'accueil_vendeur_view.php' && href.includes('accueil_vendeur_view.php')) {
      link.classList.add('active');
    }
  });
</script>