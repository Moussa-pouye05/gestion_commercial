<style>
        .hid{
                display: none;
        }
</style>
<?php
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
  ?>
<section class="mt-2">
        <div class="text-2xl font-bold text-slate-600 my-2">Clients/</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 ">
             <div class="h-20 flex-1 rounded-md flex items-center justify-between px-4 
                         sm:px-6 md:px-8 lg:px-8 gap-4 bg-[#fff]">
               <i class="fa-solid fa-users text-blue-500 text-2xl"></i>
               <div>
                     <p class="text-sm text-gray-500">Total clients</p>
                     <p class="text-xl font-bold">1,234</p>
               </div>
             </div>    
             <div class="h-20 flex-1 bg-[#fff] rounded-md flex items-center justify-between gap-4 px-4 
                         sm:px-6 md:px-8 lg:px-6">
                <i class="fa-solid fa-user-check text-green-500 text-2xl"></i>
                <div>
                     <p class="text-sm text-gray-500">Clients actifs</p>
                     <p class="text-xl font-bold">987</p>
                </div>
             </div>    
             <div class="h-20 flex-1 bg-[#fff] rounded-md flex items-center justify-between gap-4 px-4 
                         sm:px-6 md:px-8 lg:px-6">
                    <i class="fa-solid fa-cart-shopping text-orange-500 text-2xl"></i>
             <div>
                 <p class="text-sm text-gray-500">Commandes</p>
                 <p class="text-xl font-bold">456</p>
             </div>
             </div>    
             <div class="h-20 flex-1 bg-[#fff] rounded-md flex items-center justify-between gap-4 px-4 
                         sm:px-6 md:px-8 lg:px-6">
                    <i class="fa-solid fa-star text-yellow-400 text-2xl"></i>
             <div>
                 <p class="text-sm text-gray-500">Fidélité</p>
                 <p class="text-xl font-bold">85%</p>
             </div>
             </div>    
        </div>
        <!-- <div class="flex items-center justify-between my-4">
                <input 
                type="text" 
                id="searchClient" 
                placeholder="Rechercher un client..."
                class="bg-transparent w-full text-sm focus:outline-none ring-1 ring-slate-600"
            >
                <button id="addClient" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Ajouter un client</button>
            </div> -->
<div class="bg-white p-4 rounded-2xl shadow-sm mb-6 mt-2">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center bg-gray-50 border border-gray-200 
                    rounded-xl px-4 py-2 w-full lg:w-1/3 
                    focus-within:ring-2 focus-within:ring-blue-400 
                    transition">

                <i class="fa-solid fa-magnifying-glass text-gray-400 mr-3"></i>

            <input 
                type="text" 
                id="searchClient" 
                placeholder="Rechercher un Client..."
                class="bg-transparent w-full text-sm focus:outline-none"
            >
            </div>
        <div class="flex flex-wrap items-center gap-3">
            <button 
                id="addClient"
                class="flex items-center gap-2  bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                Nouveau client
            </button>

        </div>

    </div>

</div>
<div class="delete-client w-full px-2 py-2 bg-green-500 rounded-md text-center text-white hidden">Supprimer avec succes</div>
<div class="bg-white shadow-lg rounded-2xl p-4 mt-4">
    
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-700">Liste des clients</h2>
    </div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                
                    <th class="px-6 py-3">Nom</th>
                    <th class="px-6 py-3">Téléphone</th>
                    <th class="px-6 py-3">Adresse</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            
            </tbody>

        </table>
    </div>
</div>
<div class="no-found-client text-sm text-slate-500 text-center"></div>
<div id="paginationClient" class="flex gap-2 mt-4"></div>
<div class="active fixed h-full w-full left-0 top-0 bg-black/50 items-center z-50 justify-center hidden transition-all duration-300" id="modal">
    <div class="bg-white p-6 rounded-lg w-[400px] transition-all duration-300 scale-95" id="modalContent">
        <h2 class="text-xl font-bold mb-4">Ajouter un client</h2>
        <form id="formClient" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="nom" name="nom" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="telephone" class="block text-sm font-medium text-gray-700">Telephone</label>
                <input type="text" id="telephone" name="telephone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Ajouter</button>
            </div>
            <div class="succes_connect font-bold ml-2 text-green-500"></div>
            <div class="error_connect font-bold ml-2 text-red-500"></div>
        </form>
    </div>
</div>
<div class="active fixed h-full w-full left-0 top-0 bg-black/50 items-center z-50 justify-center hidden transition-all duration-300" id="modalEditClient">
    <div class="bg-white p-6 rounded-lg w-[400px] transition-all duration-300 scale-95" id="modalContent">
        <h2 class="text-xl font-bold mb-4">Modifier un client</h2>
        <form id="formClientEdit" enctype="multipart/form-data">
            <div class="mb-4">
                
                <input type="hidden" id="id" name="id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="nomEdit" name="nomEdit" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="telephone" class="block text-sm font-medium text-gray-700">Telephone</label>
                <input type="text" id="telephoneEdit" name="telephoneEdit" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                <input type="text" id="adresseEdit" name="adresseEdit" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" id="closeModalClient" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Modifier</button>
            </div>
            <div class="succes_edit_client font-bold ml-2"></div>
            <!-- <div class="error_edit_client font-bold ml-2 text-red-500"></div> -->
        </form>
    </div>
</div>
<div id="deleteModalClient" class="fixed top-0 left-0 w-full h-full bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-96 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            Confirmer la suppression
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Êtes-vous sûr de vouloir supprimer ce vendeur ?
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelDeleteClient"
                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                Annuler
            </button>
            <button id="confirmDeleteClient"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Supprimer
            </button>
        </div>
    </div>
</div>

</section>