<style>
    .hide{
        display:none;
    }
</style>
<?php
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
  ?>
<section class="mt-2">
        <h2 class="text-xl font-semibold text-gray-700">Gestion des Commandes</h2>
        <div class="text-[10px] text-slate-500">Gerez et Suivez l'etat de toutes les commandes stock</div>
<div class="bg-white p-4 rounded-2xl shadow-sm mb-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center bg-gray-50 border border-gray-200 
                    rounded-xl px-4 py-2 w-full lg:w-1/3 
                    focus-within:ring-2 focus-within:ring-blue-400 
                    transition">

            <i class="fa-solid fa-magnifying-glass text-gray-400 mr-3"></i>

            <input 
                type="text" 
                id="search" 
                placeholder="Rechercher une commande..."
                class="bg-transparent w-full text-sm focus:outline-none"
            >
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button 
                id="addCommande"
                class="flex items-center gap-2  bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                Nouvelle commande
            </button>

        </div>

    </div>

</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

    <!-- 🟡 En cours -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition px-4 py-2 border border-gray-100">

        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-100">
                <i class="fa-solid fa-hourglass-half text-indigo-600 text-sm"></i>
            </div>

            <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-medium">
                En cours
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Commandes</p>
            <h3 class="text-xl font-semibold text-gray-800">24</h3>
        </div>

    </div>


    <!-- 🟢 Clôturées -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition px-4 py-2 border border-gray-100">

        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-green-100">
                <i class="fa-solid fa-circle-check text-green-600 text-sm"></i>
            </div>

            <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-medium">
                Clôturée
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Commandes</p>
            <h3 class="text-xl font-semibold text-gray-800">130</h3>
        </div>

    </div>


    <!-- 🔴 Annulées -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition px-4 py-2 border border-gray-100">

        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-red-100">
                <i class="fa-solid fa-ban text-red-600 text-sm"></i>
            </div>

            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">
                Annulée
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Commandes</p>
            <h3 class="text-xl font-semibold text-gray-800">6</h3>
        </div>

    </div>

</div>
<div class="absolute top-0 left-0 w-full h-full flex items-center justify-center hide bg-[rgba(0,0,0,0.19)]" id="modalAddCommande">
            <form action="">
                <div class="bg-white p-6 rounded-md shadow-lg w-[400px]">
                    <h2 class="text-xl font-semibold mb-4">Ajouter un commande</h2>
                            
                            <select name="" id="" class="w-full mb-4 rounded-md border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                <option value="">Client</option>
                            </select>
                            <input type="number" placeholder="Quantité..." class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">

                            <select name="" id="" class="w-full mb-4 rounded-md border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                <option value="">Produit</option>
                            </select>
                         
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelAddCommande" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
<div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">10 derniers commandes</h2>
            <select 
                name="commande" 
                id="commande"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Toutes Commandes</option>
                <option value="">Test</option>
            </select>
            <select 
                name="days" 
                id="days"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Derniers 30 jours</option>
                <option value="">Test</option>
            </select>
    </div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Produis</th>
                    <th class="px-6 py-3">Totals</th>
                    <th class="px-6 py-3">Etat</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td>
                        
                        02/12/25
                    </td>
                    <td class="px-6 py-2 font-medium text-gray-900">
                        Getzner
                    </td>
                    <td class="px-6 py-2">
                        1128 F CFA
                    </td>
                    <td class="px-6 py-2">
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-50 text-gray-600 font-medium">
                           en cour
                        </span>
                    </td>
                    <td class="px-6 py-2">
                      <div class="flex items-center gap-2">
                  
                          <!-- Détail -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-50 text-blue-600  hover:bg-blue-100 transition">
                              <i class="fa-solid fa-eye text-sm"></i>
                          </button>
                  
                          <!-- Modifier -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                              <i class="fa-solid fa-pen text-sm"></i>
                          </button>
                  
                          <!-- Supprimer -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                              <i class="fa-solid fa-trash text-sm"></i>
                          </button>
                  
                      </div>
                     </td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
    <h2 class="text-xl font-semibold text-gray-700">Factures clients</h2>
    <div class="text-[10px] text-slate-500">Gerez les factures de vos clients</div>
    <div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">10 derniers Facture</h2>
            <select 
                name="commande" 
                id="commande"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Toutes Factures</option>
                <option value="">Test</option>
            </select>
    </div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">N Facture</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">clients</th>
                    <th class="px-6 py-3">Produits</th>
                    <th class="px-6 py-3">Montants</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td>
                        
                       1222
                    </td>
                    <td class="px-6 py-2 font-medium text-gray-900">
                         02/12/25
                    </td>
                    <td class="px-6 py-2">
                        Modou
                    </td>
                    <td class="px-6 py-2">
                        Getzner
                    </td>
                    <td class="px-6 py-2">
                        22 000 F CFA
                    </td>
                    <td class="px-6 py-2">
                      <div class="flex items-center gap-2">
                  
                          <!-- Détail -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-50 text-blue-600  hover:bg-blue-100 transition">
                              <i class="fa-solid fa-download"></i>
                          </button>
                  
                          <!-- Modifier -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                              <i class="fa-solid fa-pen text-sm"></i>
                          </button>
                  
                          <!-- Supprimer -->
                          <button class="w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                              <i class="fa-solid fa-trash text-sm"></i>
                          </button>
                  
                      </div>
                     </td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
</section>