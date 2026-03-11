<style>
        .hide{
                display: none;
        }
</style>
<?php
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
  ?>
<section class="mt-2">
    <h2 class="text-xl font-semibold text-gray-700">Gestion des Produits</h2>
    <div class="text-[10px] text-slate-500">Suivi des produits et de stocks</div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <!-- Total Produits -->
    <div class="bg-white py-2 px-4 rounded-xl shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100">
                <i class="fa-solid fa-boxes-stacked text-blue-600 text-sm"></i>
            </div>
            <span class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded">
                +12%
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Total Produits</p>
            <h3 class="total-produit text-lg font-semibold text-gray-800">0</h3>
        </div>
    </div>


    <!-- Stock Faible -->
    <div class="bg-white py-2 px-4 rounded-xl shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            </div>
            <!-- <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded">
                8
            </span> -->
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Stock Faible</p>
            <h3 class="stock-faible text-lg font-semibold text-gray-800">0</h3>
        </div>
    </div>


    <!-- Catégories -->
    <div class="bg-white py-2 px-4 rounded-xl shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100">
            <i class="fa-solid fa-layer-group text-purple-600 text-sm"></i>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Catégories</p>
            <h3 class="categorie text-lg font-semibold text-gray-800">0</h3>
        </div>
    </div>


    <!-- Valeur Stock -->
    <div class="bg-white py-2 px-4 rounded-xl shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100">
            <i class="fa-solid fa-coins text-yellow-600 text-sm"></i>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Valeur Stock</p>
            <h3 class="somme-produit text-lg font-semibold text-gray-800">00 FCFA</h3>
        </div>
    </div>

</div>
<div class="bg-white p-4 rounded-2xl shadow-sm mb-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <!-- 🔎 Search -->
        <div class="flex items-center bg-gray-50 border border-gray-200 
                    rounded-xl px-4 py-2 w-full lg:w-1/3 
                    focus-within:ring-2 focus-within:ring-blue-400 
                    transition">

            <i class="fa-solid fa-magnifying-glass text-gray-400 mr-3"></i>

            <input 
                type="text" 
                id="search" 
                placeholder="Rechercher un produit..."
                class="bg-transparent w-full text-sm focus:outline-none"
            >
        </div>

        <!-- 🎛 Actions -->
        <div class="flex flex-wrap items-center gap-3">

            <!-- Select -->
            <select 
                name="filterCategorie" 
                id="filterCategorie"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Toutes categories</option>
                
            </select>

            <!-- Ajouter Catégorie -->
            <button 
                id="addCategorie"
                class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-100 transition shadow-sm">
                <i class="fa-solid fa-layer-group text-gray-500"></i>
                Catégorie
            </button>
            <button 
                id="addProduit"
                class="flex items-center gap-2  bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                Produit
            </button>
            <button 
                id="addStock"
                class="flex items-center gap-2  bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-md transition">
                <i class="fa-solid fa-archive"></i>
                Stocker
            </button>

        </div>

    </div>

</div>
        
        <div class="fixed top-0 left-0 w-full h-full bg-[rgba(0,0,0,0.19)] z-100 flex items-center justify-center hide" id="modalAddCat">
            <form action="" id="addCategorieForm">
                <div class="bg-white p-6 rounded-md shadow-lg w-[400px]">
                    <h2 class="text-xl font-semibold mb-4">Ajouter une categorie</h2>
                    <input type="text" name="nom" placeholder="Nom de la categorie" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelAddCategorie" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">Ajouter</button>
                    </div>
                    <div class="msg-create-cat font-bold text-sm"></div>
                </div>
                
            </form>
            
        </div>
<div id="modalEditProduit" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">

    <form class="bg-white w-[520px] rounded-xl shadow-xl p-2" id="formEditProduit">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-semibold text-gray-800">Modifier produit</h2>
            </div>

            <div class="flex flex-col items-center mb-6">
                <label for="imageInputEdit" class="cursor-pointer flex flex-col items-center justify-center w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition">
                    <span class="text-gray-400 text-sm">Ajouter image</span>
                    <img id="previewImageEdit" class="w-full h-full object-cover rounded-lg hidden">
                </label>
                <input type="file" id="imageInputEdit" name="profile_picture_edit" accept="image/*" class="hidden">
            </div>
            <input type="hidden" name="old_profile" id="old_profile">
            <div class="grid grid-cols-2 gap-4">
                
                <div>
                    <label class="text-sm text-gray-600">Nom du produit</label>
                    <input type="text" name="nom_edit" id="nom_edit" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Categorie</label>
                    <select name="categorie_edit" id="categorie_edit" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">Choisir categorie</option>
                        ${categoryOptions}
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Prix d'achat</label>
                    <input type="number" name="prix_achat_edit" id="prix_achat_edit" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Prix de vente</label>
                    <input type="number" name="prix_vente_edit" id="prix_vente_edit" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div class="col-span-2">
                    <label class="text-sm text-gray-600">Quantite en stock</label>
                    <input type="number" name="quantite_edit" id="quantite_edit"  placeholder="0" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-gray-600">Cote barre</label>
                    <input type="text" name="code_edit" id="code_edit"  placeholder="0" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div>
                
                    <input type="hidden" name="id" id="id" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="cancelEditProduit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Modifier produit</button>
            </div>
            <div class="msg-produit-edit text-sm font-bold mt-2"></div>
        </form>

</div>
<div id="modalAddProduit" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">

</div>    
        <div class="fixed top-0 left-0 w-full h-full  items-center justify-center hidden bg-black/50 z-50" id="modalAddStock">
            <form action="">
                <div class="bg-white p-6 rounded-md shadow-lg w-[400px]">
                    <h2 class="text-xl font-semibold mb-4">Ajouter un stock</h2>
                    <div class="flex gap-2 ">
                            <div class="col-span-2">
                                <label class="text-sm text-gray-600">Produits</label>
                                <select name="" id="" class="w-full mb-4 rounded-md border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                 <option value="">T-shirt</option>
                                </select>
                            </div>
                            
                            <div class="col-span-2">
                                <label class="text-sm text-gray-600">Quantité</label>
                                <input type="number" placeholder="Quantité..." class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            </div>
                            
                            <div class="col-span-2">
                                <label class="text-sm text-gray-600">Prix Total</label>
                                <input type="text" placeholder="Prix total..." class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm text-gray-600">Fournisseur</label>
                                <select name="" id="" class="w-full mb-4 rounded-md border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                  <option value="">Fournisseur</option>
                                </select>
                            </div>
                            
                            
                        
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelAddStock" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mt-4" id="card">
    
</div>
<div class="mt-4 flex flex-wrap items-center justify-end gap-2" id="paginationProduit"></div>
<hr class="mt-6">
<div class="text-sm text-slate-500 mt-4" id="produitCountInfo">Affichage des produits</div>

<div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-700">Approvisionnement Récent</h2>
            <div class="text-[10px] text-slate-500">Suivi des commandes fournisseurs et livraisons</div>
        </div>
        
    </div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">ID & Date</th>
                    <th class="px-6 py-3">Produis</th>
                    <th class="px-6 py-3">Fournisseus</th>
                    <th class="px-6 py-3">Quantite</th>
                    <th class="px-6 py-3">Etat</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td>
                        022
                        02/12/25
                    </td>
                    <td class="px-6 py-2 font-medium text-gray-900">
       