<?php
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/connexion.php");
    exit;
}
?>
<section class="mt-2">
    <div id="vendeur-dashboard" class="space-y-6 bg-gradient-to-br from-slate-50 to-blue-50/30 p-4 md:p-6 rounded-2xl">
    
    <!-- EN-TÊTE AMÉLIORÉ -->
    <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 md:flex-row md:items-center md:justify-between hover:shadow-[0_20px_40px_-15px_rgba(59,130,246,0.2)] transition-all duration-300">
        <div class="relative">
            <div class="absolute -top-2 -left-2 w-20 h-20 bg-blue-500/5 rounded-full blur-2xl"></div>
            <p class="text-sm font-medium text-blue-600 flex items-center gap-1">
                <i class="fas fa-store text-xs"></i>
                Espace vendeur
            </p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mt-1">Tableau de bord personnel</h1>
            <p class="text-sm text-slate-500 mt-1 flex items-center gap-1">
                <i class="far fa-clock text-blue-400"></i>
                Suivez vos ventes, vos clients et vos commandes en temps réel
            </p>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-slate-50 to-white px-5 py-4 text-sm text-slate-600 border border-slate-200 shadow-sm min-w-[200px]">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <div class="font-semibold text-slate-800" id="vendeur-name">Chargement...</div>
                    <div class="text-xs text-slate-500 flex items-center gap-1" id="vendeur-meta">
                        <i class="fas fa-circle text-[6px] text-green-500"></i>
                        Vos informations seront affichées ici
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-4 md:p-5 border border-slate-200 shadow-sm mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Vue d'ensemble par date</h3>
                <p id="dashboard-period" class="text-xs text-slate-500">Période : aujourd'hui</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex items-center gap-2">
                    <label for="filter-start" class="text-xs text-slate-500">Début</label>
                    <input id="filter-start" type="date" class="border border-slate-200 rounded-md px-2 py-1 text-sm" />
                </div>
                <div class="flex items-center gap-2">
                    <label for="filter-end" class="text-xs text-slate-500">Fin</label>
                    <input id="filter-end" type="date" class="border border-slate-200 rounded-md px-2 py-1 text-sm" />
                </div>
                <button id="filter-dashboard" class="rounded-md bg-blue-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-blue-700">Filtrer</button>
                <button id="reset-dashboard" class="rounded-md border border-slate-300 text-slate-600 px-3 py-1.5 text-xs font-semibold hover:bg-slate-100">Réinitialiser</button>
            </div>
        </div>
    </div>

    <!-- STATS CARTES AVEC DESIGN MODERNE -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Carte 1 - Chiffre d'affaires -->
        <div class="group rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-[0_20px_40px_-15px_rgba(34,197,94,0.2)] hover:border-green-200 transition-all duration-300">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <!-- <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full font-medium">+12%</span> -->
            </div>
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-slate-800" id="vendeur-revenue">0 FCFA</p>
            <div class="mt-3 flex items-center gap-1 text-xs text-slate-400">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>Commandes clôturées par vos soins</span>
            </div>
            <div class="mt-3 h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full w-3/4 bg-gradient-to-r from-green-500 to-green-400 rounded-full"></div>
            </div>
        </div>

        <!-- Carte 2 - Commandes -->
        <div class="group rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-[0_20px_40px_-15px_rgba(59,130,246,0.2)] hover:border-blue-200 transition-all duration-300">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <!-- <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full font-medium">+5</span> -->
            </div>
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Commandes</p>
            <p class="text-2xl font-bold text-slate-800" id="vendeur-commands">0</p>
            <div class="mt-3 flex items-center gap-1 text-xs text-slate-400">
                <i class="fas fa-clock text-blue-500"></i>
                <span>Total de vos commandes enregistrées</span>
            </div>
            <div class="mt-3 flex gap-1">
                <div class="h-1.5 flex-1 rounded-full bg-blue-500"></div>
                <div class="h-1.5 flex-1 rounded-full bg-blue-400"></div>
                <div class="h-1.5 flex-1 rounded-full bg-blue-300"></div>
                <div class="h-1.5 flex-1 rounded-full bg-blue-200"></div>
            </div>
        </div>

        <!-- Carte 3 - Clients servis -->
        <div class="group rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-[0_20px_40px_-15px_rgba(168,85,247,0.2)] hover:border-purple-200 transition-all duration-300">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <!-- <span class="text-xs px-2 py-1 bg-purple-100 text-purple-600 rounded-full font-medium">+8</span> -->
            </div>
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Clients servis</p>
            <p class="text-2xl font-bold text-slate-800" id="vendeur-clients">0</p>
            <div class="mt-3 flex items-center gap-1 text-xs text-slate-400">
                <i class="fas fa-user-check text-purple-500"></i>
                <span>Clients distincts associés à vos ventes</span>
            </div>
            <div class="mt-3 flex -space-x-2">
                <div class="w-6 h-6 rounded-full bg-purple-100 border-2 border-white flex items-center justify-center text-[10px] font-medium text-purple-600">JD</div>
                <div class="w-6 h-6 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-[10px] font-medium text-blue-600">MM</div>
                <div class="w-6 h-6 rounded-full bg-green-100 border-2 border-white flex items-center justify-center text-[10px] font-medium text-green-600">PD</div>
                <div class="w-6 h-6 rounded-full bg-amber-100 border-2 border-white flex items-center justify-center text-[10px] font-medium text-amber-600">+5</div>
            </div>
        </div>

        <!-- Carte 4 - Articles vendus -->
        <div class="group rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-[0_20px_40px_-15px_rgba(249,115,22,0.2)] hover:border-orange-200 transition-all duration-300">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-box text-lg"></i>
                </div>
                <!-- <span class="text-xs px-2 py-1 bg-orange-100 text-orange-600 rounded-full font-medium">+24%</span> -->
            </div>
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Articles vendus</p>
            <p class="text-2xl font-bold text-slate-800" id="vendeur-items">0</p>
            <div class="mt-3 flex items-center gap-1 text-xs text-slate-400">
                <i class="fas fa-cube text-orange-500"></i>
                <span>Quantités clôturées</span>
            </div>
            <div class="mt-3 grid grid-cols-4 gap-1">
                <div class="h-1.5 rounded-full bg-orange-500"></div>
                <div class="h-1.5 rounded-full bg-orange-400"></div>
                <div class="h-1.5 rounded-full bg-orange-300"></div>
                <div class="h-1.5 rounded-full bg-orange-200"></div>
            </div>
        </div>
    </div>

    <!-- ÉTAT DES COMMANDES + DERNIÈRES COMMANDES -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- État des commandes -->
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-lg transition-all">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-500"></i>
                État des commandes
            </h2>
            <div class="mt-6 space-y-3">
                <div class="group/item flex items-center justify-between rounded-xl bg-gradient-to-r from-slate-50 to-white px-4 py-3 border border-slate-100 hover:border-indigo-200 hover:shadow-sm transition">
                    <span class="text-sm text-slate-600 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        En cours
                    </span>
                    <span class="font-semibold text-indigo-600 text-lg" id="vendeur-en-cours">0</span>
                </div>
                <div class="group/item flex items-center justify-between rounded-xl bg-gradient-to-r from-slate-50 to-white px-4 py-3 border border-slate-100 hover:border-green-200 hover:shadow-sm transition">
                    <span class="text-sm text-slate-600 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Clôturées
                    </span>
                    <span class="font-semibold text-green-600 text-lg" id="vendeur-cloturees">0</span>
                </div>
                <div class="group/item flex items-center justify-between rounded-xl bg-gradient-to-r from-slate-50 to-white px-4 py-3 border border-slate-100 hover:border-red-200 hover:shadow-sm transition">
                    <span class="text-sm text-slate-600 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Annulées
                    </span>
                    <span class="font-semibold text-red-600 text-lg" id="vendeur-annulees">0</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <!-- <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>Total</span>
                    <span class="font-medium text-slate-700" id="vendeur-total-commandes">0</span>
                </div> -->
            </div>
        </div>

        <!-- Dernières commandes -->
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-lg transition-all lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clock text-blue-500"></i>
                    Dernières commandes
                </h2>
                <a href="../view/commande_view.php" class="group/link text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    Voir toutes
                    <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 rounded-lg">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Commande</th>
                            <th class="px-4 py-3 text-left font-medium">Client</th>
                            <th class="px-4 py-3 text-left font-medium">Date</th>
                            <th class="px-4 py-3 text-right font-medium">Montant</th>
                            <th class="px-4 py-3 text-center font-medium">État</th>
                        </tr>
                    </thead>
                    <tbody id="vendeur-recent-orders" class="divide-y divide-slate-100">
                        <!-- Les lignes seront injectées par JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TOP PRODUITS -->
    <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.15)] border border-slate-100 hover:shadow-lg transition-all">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas fa-crown text-amber-500"></i>
                Vos produits les plus vendus
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-amber-50 text-amber-600 px-3 py-1 rounded-full font-medium">Top 5</span>
                <span class="text-xs text-slate-400">ce mois</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 rounded-lg">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Produit</th>
                        <th class="px-4 py-3 text-left font-medium">Catégorie</th>
                        <th class="px-4 py-3 text-right font-medium">Quantité</th>
                        <th class="px-4 py-3 text-right font-medium">Chiffre d'affaires</th>
                    </tr>
                </thead>
                <tbody id="vendeur-top-products" class="divide-y divide-slate-100">
                    <!-- Les lignes seront injectées par JS -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            <a id="link-full-ranking" href="../view/commande_view.php" class="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Voir le classement complet
                <i class="fas fa-chart-simple"></i>
            </a>
        </div>
    </div>

    <!-- STYLES POUR LES TABLEAUX DYNAMIQUES -->
    <style>
        /* Style pour les lignes de tableau */
        #vendeur-recent-orders tr, #vendeur-top-products tr {
            transition: all 0.2s;
        }
        #vendeur-recent-orders tr:hover, #vendeur-top-products tr:hover {
            background-color: #f8fafc;
        }
        /* Badges de statut */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-badge.en-cours {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .status-badge.cloturee {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .status-badge.annulee {
            background-color: #fee2e2;
            color: #dc2626;
        }
    </style>

    <!-- SCRIPT POUR AJOUTER LA DATE DYNAMIQUE (optionnel) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter la date du jour dans l'en-tête si besoin
            const metaDiv = document.getElementById('vendeur-meta');
            if (metaDiv) {
                const today = new Date().toLocaleDateString('fr-FR', { 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric' 
                });
                metaDiv.innerHTML = `<i class="fas fa-circle text-[6px] text-green-500 mr-1"></i> ${today}`;
            }
        });
    </script>
</div>
</section>
