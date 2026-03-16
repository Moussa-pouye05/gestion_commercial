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
                id="searchCommande" 
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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

    <!-- En cours -->
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
            <!-- <div>count-xl font-semiboldEnCours</div>  -->
            <h3 class="text text-gray-800" id="countEnCours">0</h3>
        </div>

    </div>


    <!-- Clôturées -->
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
            <h3 class="text-xl font-semibold text-gray-800" id="countCloturee">0</h3>
        </div>
        

    </div>


    <!-- Annulées -->
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
            <h3 class="text-xl font-semibold text-gray-800" id="countAnnulee">0</h3>
        </div>

    </div>
    <div class=" flex-1 bg-[#fff] rounded-md flex items-center justify-between gap-4 px-4 
                         sm:px-6 md:px-8 lg:px-6">
                    <i class="fa-solid fa-cart-shopping text-orange-500 text-2xl"></i>
             <div>
                 <p class="text-sm text-gray-500">Commandes du jours</p>
                 <p class="total-commande text-xl font-bold">456</p>
             </div>
    </div>
</div>
<div class="absolute top-0 left-0 w-full h-full hidden items-center justify-center bg-black/50 z-50"  id="modalAddCommande">
            <form class="bg-white p-6 rounded-xl shadow-md max-w-5xl mx-auto" id="saveCommande">

    <h2 class="text-xl font-semibold mb-6" id="formTitle">Nouvelle commande</h2>

    <!-- Client -->
    <div class="grid md:grid-cols-1 gap-4 mb-6">
        <div>
            <label class="text-sm text-gray-600">Client</label>
            <select class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 client-select" id="clientSelect" required>
                <option value="">Choisir un client</option>
            </select>
        </div>
    </div>

    <!-- Produits -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Produit</th>
                    <th class="p-2">Prix</th>
                    <th class="p-2">Quantité</th>
                    <th class="p-2">Sous-total</th>
                    <th class="p-2"></th>
                </tr>
            </thead>

            <tbody id="commandeBody">

                <tr>
                    <td class="p-2">
                        <select class="w-full border rounded-md px-2 py-1 produit-select">
                            <option value="">Produit</option>
                        </select>
                    </td>

                    <td class="p-2">
                        <input type="number" class="w-full border rounded-md px-2 py-1 prix-input" value="0" min="0">
                    </td>

                    <td class="p-2">
                        <input type="number" class="w-full border rounded-md px-2 py-1 quantite-input" value="1" min="1">
                    </td>

                    <td class="p-2 text-center font-medium sous-total">
                        0 FCFA
                    </td>

                    <td class="p-2 text-center">
                        <button type="button" class="text-red-500" onclick="removeRow(this)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>
    </div>

    <!-- Ajouter produit -->
    <div class="mt-4">
        <button type="button" id="addProduit"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + Ajouter produit
        </button>
    </div>

    <!-- Total -->
    <div class="flex justify-end mt-6">
        <div class="bg-gray-100 p-4 rounded-lg w-60">
            <p class="flex justify-between text-sm">
                <span>Total</span>
                <span class="font-semibold total-display">0 FCFA</span>
            </p>
        </div>
    </div>

    <!-- Boutons -->
    <div class="flex justify-end gap-3 mt-6">
        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg" id="cancelAddCommande">
            Annuler
        </button>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Enregistrer la commande
        </button>
    </div>

</form>
        </div>
<div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">10 derniers commandes</h2>
            <select 
                name="etat" 
                id="etatFilter"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Toutes Commandes</option>
                <option value="en_cours">En cours</option>
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
        <table class="w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">N°</th>
                    <th class="p-3 text-left">Client</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Statut</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody id="commandesTable">

                <!-- <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">CMD-001</td>

                    <td class="p-3">Moussa Diop</td>

                    <td class="p-3">18/03/2026</td>

                    <td class="p-3 font-semibold text-blue-600">
                        25 000 FCFA
                    </td>

                    <td class="p-3">
                        <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded">
                            Payée
                        </span>
                    </td>

                    <td class="p-3 flex justify-center gap-2">

                        <button class="bg-blue-50 text-blue-600 p-2 rounded hover:bg-blue-100">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button class="bg-yellow-50 text-yellow-600 p-2 rounded hover:bg-yellow-100">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="bg-red-50 text-red-600 p-2 rounded hover:bg-red-100">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </td>
                </tr> -->

            </tbody>

        </table>
    </div>
</div>
    <!--  -->
</section>