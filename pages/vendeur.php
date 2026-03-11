<style> 
    <?php
    require('auth.php');
    requireRole('admin');
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
    ?>
</style>
<section class="mt-2">
        <h2 class="text-xl font-semibold text-gray-700">Vendeur</h2>
        <div class="text-[10px] text-slate-500">Gerez vos relations vendeurs</div>
<div class="bg-white p-4 rounded-2xl shadow-sm mb-6 mt-2">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center bg-gray-50 border border-gray-200 
                    rounded-xl px-4 py-2 w-full lg:w-1/3 
                    focus-within:ring-2 focus-within:ring-blue-400 
                    transition">

                <i class="fa-solid fa-magnifying-glass text-gray-400 mr-3"></i>

            <input 
                type="text" 
                id="search" 
                placeholder="Rechercher un vendeur..."
                class="bg-transparent w-full text-sm focus:outline-none"
            >
            </div>
        <div class="flex flex-wrap items-center gap-3">
            <button 
                id="addVendeur"
                class="flex items-center gap-2  bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                Nouveau vendeur
            </button>

        </div>

    </div>

</div>
<div class="delete-vendeur w-full px-2 py-2 bg-green-500 rounded-md text-center text-white hidden">Supprimer avec succes</div>
<div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    <!-- Titre -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-700">Listes des vendeurs</h2>
            <div class="text-[10px] text-slate-500">Gerez vos vendeurs</div>
        </div>
        
    </div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Profile</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Contact</th>
                    <th class="px-6 py-3">Performance</th>
                    <th class="px-6 py-3">Action</th>
                    
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                
            </tbody>

        </table>
    </div>
</div>
<div class="no-found text-sm text-slate-500"></div>
<div id="pagination" class="flex gap-2 mt-4"></div>
<div class="fixed top-0 left-0 w-full h-full items-center justify-center hidden bg-black/50 z-50" id="modalAddVendeur">
            <form action="" id="form_client" method="POST" enctype="multipart/form-data" >
                <div class="bg-white p-6 rounded-md shadow-lg w-[400px]">
                    <h2 class="text-xl font-semibold mb-4">Ajouter un vendeur</h2>
                    <div class="flex gap-2">
                        <div class="w-full">
                            <label for="nom" class="block text-gray-700 font-bold mb-2">Nom d'utilisateur</label>
                            <input type="nom" placeholder="Nom d'utilisateur..." name="nom" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                            <input type="email" placeholder="Ex:pouye@gmail.com..." name="email" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <label for="password" class="block text-gray-700 font-bold mb-2">Mot de passe</label>
                            <input type="password" placeholder=".............." name="password" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        </div>
                        <div class="w-full">
                            <label for="profile_picture" class="block text-gray-700 font-bold mb-2">Photo de profil</label>
                            <input type="file" name="profile_picture" accept="image/*" class="w-full px-4 py-1 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <label for="telephone" class="block text-gray-700 font-bold mb-2">Telephone</label>
                            <input type="text" name="telephone"  placeholder="Ex: 770000000" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <label for="telephone" class="block text-gray-700 font-bold mb-2">Role</label>
                            <select name="role" id="" class="w-full mb-4 rounded-md border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                <option value="admin">Admin</option>
                                <option value="vendeur">Vendeur</option>
                            </select>
                            
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelAddVendeur" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">Ajouter</button>
                    </div>
                    <div class="error_connect text-sm text-red-500 font-bold"></div>
                <div class="succes_connect text-sm text-green-500 font-bold"></div>
                </div>
            </form>
        </div>
<div class="fixed top-0 left-0 w-full h-full hidden items-center justify-center  bg-black/50 z-50" id="modalEditVendeur">
            <form action="" id="form_vendeur" method="POST" enctype="multipart/form-data" >
                <div class="bg-white p-6 rounded-md shadow-lg w-[400px]">
                    <h2 class="text-xl font-semibold mb-4">Modifier un vendeur</h2>
                    <!-- <div class="flex gap-2 direction-column"> -->
                        
                            <label for="nom" class="block text-gray-700 font-bold mb-2">Nom d'utilisateur</label>
                            <input type="text" placeholder="Nom d'utilisateur..." name="nom" id="nom" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <input type="hidden" placeholder="Nom d'utilisateur..." name="id" id="id" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                            <input type="email" placeholder="Ex:pouye@gmail.com..." name="email" id="email" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        
                            <label for="profile_picture" class="block text-gray-700 font-bold mb-2">Photo de profil</label>
                            <input type="file" name="profile_picture" accept="image/*" class="w-full px-4 py-1 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <input type="hidden" name="old_profile" id="old_profile">
                            <label for="telephone" class="block text-gray-700 font-bold mb-2">Telephone</label>
                            <input type="text" name="telephone" id="telephone"  placeholder="Ex: 770000000" class="w-full px-4 py-2 mb-4 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <!-- <label for="telephone" class="block text-gray-700 font-bold mb-2">Role</label> -->
                        
                            
                        
                    <!-- </div> -->
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelEditVendeur" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition">Modifier</button>
                    </div>
                    <div class="error_edit text-sm text-red-500 font-bold"></div>
                <div class="succes_edit text-sm  font-bold"></div>
                </div>
                
            </form>
        </div>
        <!-- Modal Suppression -->
<div id="deleteModal" class="fixed top-0 left-0 w-full h-full bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-96 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            Confirmer la suppression
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Êtes-vous sûr de vouloir supprimer ce vendeur ?
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelDelete"
                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                Annuler
            </button>
            <button id="confirmDelete"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Supprimer
            </button>
        </div>
    </div>
</div>
</section>
