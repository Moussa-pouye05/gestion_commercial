<nav>

  <!-- sidebar -->
  <div
    class="sidebar fixed top-0  h-full w-0 sm:w-0 md:w-[20%] lg:w-[18%] overflow-hidden
    bg-white z-10 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.19)]
      transition-all duration-300"
  >
    <div class="flex items-center justify-between pt-8 px-6">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-business-time text-xl text-blue-400"></i>
        <div class="text-xl md:text-md font-bold text-slate-600">
          <span class="text-red-500">G</span>-STOCK
        </div>
      </div>

      <!-- bouton fermer (mobile) -->
      <button id="closeSidebar" class="md:hidden text-slate-500 text-xl ml-4">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <ul class="mt-10">
      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/accueil_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-house"></i>Accueil
        </a>
      </li>
      <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/vendeur_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-file-signature"></i>Vendeurs
        </a>
      </li>
      <?php endif?>
      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/produit_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-box"></i>Produits
        </a>
      </li>
        <?php if(!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin'):?>
      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/fournisseur_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-truck"></i>Fournisseurs
        </a>
      </li>
          <?php endif?>

      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/client_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-users"></i>Clients
        </a>
      </li>

      <li class="px-6 py-3 hover:bg-gray-200 hover:border-slate-500 hover:border-l-4 transition duration-300 cursor-pointer">
        <a href="../view/commande_view.php" class="flex items-center gap-2 text-slate-600">
          <i class="fa-solid fa-cart-shopping"></i>Commandes & Factures
        </a>
      </li>
    </ul>

    <hr class="my-4">

    <div class="px-6 mt-4 flex items-center gap-2 text-slate-600 cursor-pointer hover:text-red-500 transition duration-300">
      <i class="fa-solid fa-right-from-bracket"></i>
      <a href="../pages/deconnection.php">Déconnexion</a>
    </div>
  </div>
</nav>