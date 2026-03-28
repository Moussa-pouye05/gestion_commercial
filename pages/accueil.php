<section class="mt-2">
<div id="tab-dashboard" class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">
  
  <!-- Period Filter -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.12)] transition-shadow duration-300">
    <div>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Vue d'ensemble</h2>
      <p class="text-gray-500 text-xs sm:text-sm mt-1">Gérez et analysez vos performances commerciales.</p>
    </div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
      <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <div class="w-full sm:w-auto">
          <label class="block text-[10px] uppercase tracking-wider text-gray-500 mb-1">Du</label>
          <input type="date" id="dateFrom" onchange="applyFilter()" 
            class="w-full sm:w-auto bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition shadow-sm">
        </div>
        <div class="w-full sm:w-auto">
          <label class="block text-[10px] uppercase tracking-wider text-gray-500 mb-1">Au</label>
          <input type="date" id="dateTo" onchange="applyFilter()" 
            class="w-full sm:w-auto bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition shadow-sm">
        </div>
      </div>
      <button onclick="resetFilter()" 
        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gray-100 text-gray-600 text-sm hover:bg-gray-200 hover:text-gray-800 transition-all duration-200 font-medium shadow-sm hover:shadow">
        Réinitialiser
      </button>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" id="kpi-row">
    <!-- JS filled -->
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-100 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.1)] transition-all duration-300">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
          <i class="fas fa-chart-line text-green-500 text-lg"></i>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase tracking-wider">Revenus Totaux</p>
          <p id="kpi-revenue" class="text-gray-800 text-xl font-bold">0 FCFA</p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-100 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.1)] transition-all duration-300">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
          <i class="fas fa-shopping-cart text-blue-500 text-lg"></i>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase tracking-wider">Total Commandes</p>
          <p id="kpi-commands" class="text-gray-800 text-xl font-bold">0</p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-100 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.1)] transition-all duration-300">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
          <i class="fas fa-users text-yellow-500 text-lg"></i>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase tracking-wider">Clients</p>
          <p id="kpi-clients" class="text-gray-800 text-xl font-bold">0</p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-100 shadow-[0_4px_12px_-2px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.1)] transition-all duration-300">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
          <i class="fas fa-box text-purple-500 text-lg"></i>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase tracking-wider">Produits</p>
          <p id="kpi-products" class="text-gray-800 text-xl font-bold">0</p>
        </div>
      </div>
    </div>
  </div>

<!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="section-card lg:col-span-2">
          <p class="section-title">Évolution du Chiffre d'Affaires</p>
          <canvas id="revenueChart"></canvas>
        </div>
        <div class="section-card">
          <p class="section-title">Répartition par Catégorie</p>
          <canvas id="categoryChart"></canvas>
          <div id="category-legend" class="mt-3 space-y-1.5 text-xs"></div>
        </div>
      </div>

  <!-- Recent Orders + Stock Alerts -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Admin Profile Section -->
<!-- <div class="lg:col-span-3">
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
        <div class="flex flex-col lg:flex-row gap-6 lg:items-center justify-between">
          <div class="flex items-center gap-4">
            
            
          </div>
          
        </div>
      </div>
    </div> -->

    <div class="section-card lg:col-span-2 bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-clock text-blue-500"></i>
        10 Dernières Commandes
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendeur</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody id="recent-orders-body">
            
          </tbody>
        </table>
      </div>
    </div>
    <div class="section-card bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-amber-500"></i>
        Produits en Rupture
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Alerte</th>
            </tr>
          </thead>
          <tbody id="stock-alerts-body">
           
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Products + Top Sellers -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="section-card bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-crown text-amber-500"></i>
        Top 8 Produits les Plus Vendus
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rang</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produit</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catégorie</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantité</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">CA</th>
            </tr>
          </thead>
          <tbody id="top-products-body">
           
          </tbody>
        </table>
      </div>
    </div>
    <div class="section-card bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-star text-amber-500"></i>
        Top 10 Meilleurs Vendeurs
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rang</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendeur</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Commandes</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Performance</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">CA</th>
            </tr>
          </thead>
          <tbody id="top-sellers-body">
           
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Clients + Top Orders -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="section-card bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-trophy text-amber-500"></i>
        Top 10 Meilleurs Clients
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rang</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Commandes</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">CA Total</th>
            </tr>
          </thead>
          <tbody id="top-clients-body">
           
          </tbody>
        </table>
      </div>
    </div>
    <div class="section-card bg-white rounded-xl border border-gray-200 p-4 sm:p-5 shadow-[0_4px_15px_-3px_rgba(0,0,0,0.1)]">
      <p class="section-title text-gray-800 font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-medal text-amber-500"></i>
        Top 10 Meilleures Commandes
      </p>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 bg-gray-50/50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">N°</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant</th>
            </tr>
          </thead>
          <tbody id="top-orders-body">
           
          </tbody>
        </table>
      </div>
    </div>
  </div>

<style>
.section-card {
  background: white;
  border-radius: 1rem;
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
  box-shadow: 0 4px 15px -3px rgba(0,0,0,0.1);
  position: relative;
  height: 600px;
}
.section-title {
  font-size: 1.125rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 1rem;
}
#revenueChart, #categoryChart {
  height: 340px !important;
  width: 99% !important;
}
#category-legend {
  font-size: 0.875rem;
}
table td, table th {
  @apply px-2 py-1 sm:px-4 sm:py-3 max-w-[120px] sm:max-w-[150px] truncate;
}
@media (max-width: 640px) {
  table td, table th { max-width: 80px; font-size: 0.75rem; }
}
</style>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.3/chart.umd.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.js"></script> -->
</div>
</section>
    
