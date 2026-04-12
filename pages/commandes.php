<style>
    .hide{
        display:none;
    }
</style>
<?php
  if(!$_SESSION['user']){
    header("Location: ../index.php");
  }
  ?>
<section class="mt-2 p-2">
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
<?php if($_SESSION['user']['role'] === "admin"):  ?>
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
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition px-4 py-2 border border-gray-100">

        <div class="flex items-center justify-between">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-red-100">
                <i class="fa-solid fa-cart-shopping text-orange-500 text-2xl"></i>
            </div>

            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">
                <!-- Annulée -->
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500">Commandes du jour</p>
            <h3 class="text-xl font-semibold text-gray-800 total-commande">0</h3>
        </div>

    </div>
    <!-- <div class=" flex-1 bg-[#fff] rounded-md flex items-center justify-between gap-4 px-4 
                         sm:px-6 md:px-8 lg:px-6">
                    <i class="fa-solid fa-cart-shopping text-orange-500 text-2xl"></i>
             <div>
                 <p class="text-sm text-gray-500">Commandes du jours</p>
                 <p class=" text-xl font-bold">0</p>
             </div>
    </div> -->
</div>
<?php endif?>
<?php if($_SESSION['user']['role'] === "vendeur"):  ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

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
            <h3 class="text text-gray-800" id="countEnCoursVendeur">0</h3>
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
            <h3 class="text-xl font-semibold text-gray-800" id="countClotureeVendeur">0</h3>
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
            <h3 class="text-xl font-semibold text-gray-800" id="countAnnuleeVendeur">0</h3>
        </div>

    </div>
    
</div>
<?php endif?>
<div class="fixed top-0 left-0 w-full h-full hidden items-center justify-center bg-black/50 backdrop-blur-sm  z-50 p-3 sm:p-5" id="modalAddCommande">

    <form class="flex flex-col w-full max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden" style="max-height:92vh;" id="saveCommande">

        <!-- ══════════ HEADER ══════════ -->
        <div class="flex-shrink-0 relative overflow-hidden" style="background: linear-gradient(135deg, #16a 0%, rgb(15, 18, 206) 100%); padding: 20px 22px 18px;">
            <!-- Cercles déco -->
            <div style="position:absolute; top:-30px; right:-30px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,0.07); pointer-events:none;"></div>
            <div style="position:absolute; bottom:-40px; left:20px; width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none;"></div>

            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.15);">
                            <i class="fas fa-cart-plus text-white text-sm"></i>
                        </div>
                        <span class="text-xs font-semibold tracking-widest uppercase text-green-100">Nouvelle commande</span>
                    </div>
                    <h2 class="text-xl font-bold text-white" id="formTitle">Créer une commande</h2>
                    <p class="text-xs mt-1" style="color:rgba(255,255,255,0.55);">Renseignez le client et les produits</p>
                </div>
                <button type="button" id="cancelAddCommande"
                    class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-lg transition"
                    style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:white;">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- ══════════ BODY SCROLLABLE ══════════ -->
        <div class="overflow-y-auto flex-1 p-5 space-y-6" style="background:#f8fafc;">

            <!-- CLIENT -->
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-green-50">
                        <i class="fas fa-user text-green-600 text-xs"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Client</span>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                    <input
                        type="text"
                        id="clientSearch"
                        class="client-search w-full rounded-lg border border-slate-200 pl-8 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        placeholder="Rechercher un client..."
                        autocomplete="off"
                        list="clientOptions"
                        required
                    >
                    <input type="hidden" class="client-id-input" id="clientIdInput">
                    <datalist id="clientOptions" class="client-datalist"></datalist>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 client-meta">Saisissez au moins 2 caractères</p>
            </div>

            <!-- PRODUITS -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-green-50">
                            <i class="fas fa-boxes text-green-600 text-xs"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Produits commandés</span>
                    </div>
                    <button type="button" id="addProduit"
                        class="flex items-center gap-1.5 text-xs font-semibold text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition">
                        <i class="fas fa-plus text-[10px]"></i>
                        Ajouter produit
                    </button>
                </div>

                <!-- Tableau scroll horizontal sur mobile -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" style="min-width:520px;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Désignation</th>
                                <th class="px-4 py-2.5 text-center text-[10px] font-semibold text-slate-400 uppercase tracking-wider w-28">Prix unit.</th>
                                <th class="px-4 py-2.5 text-center text-[10px] font-semibold text-slate-400 uppercase tracking-wider w-24">Quantité</th>
                                <th class="px-4 py-2.5 text-right text-[10px] font-semibold text-slate-400 uppercase tracking-wider w-32">Sous-total</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>

                        <tbody id="commandeBody" class="divide-y divide-slate-100">

                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5">
                                    <div class="space-y-1">
                                        <input
                                            type="text"
                                            class="produit-search w-full rounded-lg border border-slate-200 px-3 py-2 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                            placeholder="Rechercher un produit..."
                                            autocomplete="off"
                                        >
                                        <input type="hidden" class="produit-id-input">
                                        <datalist class="produit-datalist"></datalist>
                                        <div class="text-[10px] text-slate-400 produit-meta">Saisissez au moins 2 caractères</div>
                                    </div>
                                </td>

                                <td class="px-4 py-2.5">
                                    <input
                                        type="number"
                                        class="prix-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                        value="0" min="0"
                                    >
                                </td>

                                <td class="px-4 py-2.5">
                                    <input
                                        type="number"
                                        class="quantite-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                        value="1" min="1"
                                    >
                                </td>

                                <td class="px-4 py-2.5 text-right">
                                    <span class="sous-total font-semibold text-sm" style="font-family:'Courier New', monospace; color:#059669;">0 FCFA</span>
                                </td>

                                <td class="px-3 py-2.5 text-center">
                                    <button type="button" onclick="removeRow(this)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition mx-auto">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TOTAL -->
            <div class="flex justify-end">
                <div class="bg-white border border-slate-200 rounded-xl px-5 py-4 w-full sm:w-64 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sous-total</span>
                        <span class="text-sm font-medium text-slate-600 total-display" style="font-family:'Courier New', monospace;">0 FCFA</span>
                    </div>
                    <div class="border-t border-slate-100 pt-2 mt-1 flex justify-between items-center">
                        <!-- <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total TTC</span>
                        <span class="text-lg font-bold total-display" style="font-family:'Courier New', monospace; color:#059669;">0 FCFA</span> -->
                    </div>
                </div>
            </div>

        </div>

        <!-- ══════════ FOOTER ══════════ -->
        <div class="flex-shrink-0 flex items-center justify-end gap-2 px-5 py-4 border-t border-slate-100 bg-white">
            <!-- <button type="button" id="cancelAddCommande"
                class="px-4 py-2 text-sm font-semibold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                Annuler
            </button> -->
            <button type="submit"
                class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white rounded-xl transition"
                style="background: linear-gradient(135deg, #16a 0%, rgb(15, 18, 206) 100%); box-shadow: 0 4px 14px rgba(5,150,105,0.35);"
                onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'">
                <i class="fas fa-check text-xs"></i>
                Enregistrer la commande
            </button>
        </div>

    </form>
</div>
<?php if($_SESSION['user']['role'] === "admin"):  ?>
<div class="bg-white shadow-lg rounded-2xl p-4 mt-4 mb-4">
    
    <!-- Titre -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-slate-200">Liste des commandes</h2>
        <div class="flex items-center gap-2">
            <select name="etat" id="etatFilter" class="bg-gray-50 border border-gray-200 text-sm px-4 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition dark:bg-slate-700/50 dark:border-slate-600 dark:text-slate-200">
                <option value="">Toutes</option>
                <option value="en_cours">En cours</option>
                <option value="cloturee">Clôturées</option>
                <option value="annulee">Annulées</option>
            </select>
        </div>
    </div>
    <div id="paginationCommandes" class="flex gap-2 mt-4 justify-center mb-4"></div>

    <!-- Responsive wrapper -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">N°</th>
                    <th class="p-3 text-left">Client</th>
                    <th class="p-3 text-left">Vendeur</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Statut</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody id="commandesTable">


            </tbody>

        </table>
    </div>
</div>
<?php endif?>
</xai:function_call >




<?php if($_SESSION['user']['role'] === "vendeur"):  ?>
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
                <option value="cloturee">Clôturées</option>
                <option value="annulee">Annulées</option>
            </select>
            <!-- <select 
                name="days" 
                id="days"
                class="bg-gray-50 border border-gray-200 
                       text-sm px-4 py-2 rounded-xl 
                       focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">Derniers 30 jours</option>
                <option value="">Test</option>
            </select> -->
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


            </tbody>

        </table>
    </div>
</div>
<?php endif?>
</section>
