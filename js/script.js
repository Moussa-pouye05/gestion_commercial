// const dashboard = document.querySelector(".dashboard");
// const navLinks = document.querySelectorAll(".nav");
// navLinks.forEach(link => {
//    // link.addEventListener("click", (e) => {
//         console.log(link);
//         const page = e.target.textContent;
//         dashboard.textContent = page;
//    // })
// });
//menu
const menu = document.querySelector(".menu");
const sidbare = document.querySelector(".sidebar");
const closeId = document.querySelector("#closeSidebar");
const section = document.querySelector("section");

if (menu && sidbare && closeId && section) {
    closeId.addEventListener("click", () => {
       sidbare.classList.add("w-0");
       sidbare.classList.add("sm:w-0");
       sidbare.classList.remove("w-[50%]");
       sidbare.classList.remove("sm:w-[40%]");
    });

    menu.addEventListener("click", () => {
       sidbare.classList.remove("w-0");
       sidbare.classList.remove("sm:w-0");
       sidbare.classList.add("w-[50%]");
       sidbare.classList.add("sm:w-[40%]");
    });

    section.addEventListener("click", (e) =>{
        if(!sidbare.contains(e.target)){
            sidbare.classList.add("w-0");
            sidbare.classList.add("sm:w-0");
            sidbare.classList.remove("w-[50%]");
            sidbare.classList.remove("sm:w-[40%]");
        }
    });
}

// Dashboard helpers
function formatNumberFR(value) {
    return new Intl.NumberFormat('fr-FR').format(Number(value) || 0);
}

let revenueChart = null;
let categoryChart = null;

async function loadDashboard() {
    const dashboardPanel = document.getElementById('tab-dashboard');
    if (!dashboardPanel) return;

    const dateFrom = document.getElementById('dateFrom')?.value || '';
    const dateTo = document.getElementById('dateTo')?.value || '';

    const params = new URLSearchParams();
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);

    try {
        const response = await fetch(`../php/post_dashboard_data.php?${params.toString()}`);
        const result = await response.json();
        if (!result.success) {
            console.error('Erreur dashboard', result.message);
            return;
        }

        const totals = result.totals || {};
        const kpiNodes = dashboardPanel.querySelectorAll('.kpi-value');
        const revenueNode = dashboardPanel.querySelector('#kpi-revenue');
        const commandsNode = dashboardPanel.querySelector('#kpi-commands');

        if (revenueNode) revenueNode.textContent = `${formatNumberFR(totals.revenue || 0)} FCFA`;
        if (commandsNode) commandsNode.textContent = formatNumberFR(totals.commands || 0);

        const renderRows = (id, rows, template) => {
            const container = document.getElementById(id);
            if (!container) return;
            container.innerHTML = '';
            if (!Array.isArray(rows) || rows.length === 0) {
                const cols = typeof template === 'string' ? (template.indexOf('<td') >= 0 ? 5 : 4) : 4;
                container.innerHTML = `<tr><td colspan="${cols}" class="px-4 py-3 text-center text-gray-500">Aucun résultat</td></tr>`;
                return;
            }
            rows.forEach((row, idx) => {
                container.insertAdjacentHTML('beforeend', template(row, idx + 1));
            });
        };

        renderRows('recent-orders-body', result.recent_commands || [], (row) => `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
              <td class="px-4 py-3 font-mono text-gray-700">CMD-${String(row.id).padStart(3,'0')}</td>
              <td class="px-4 py-3 text-gray-600">${row.client_nom || 'N/A'}</td>
              <td class="px-4 py-3 text-gray-600">${row.vendeur_nom || 'N/A'}</td>
              <td class="px-4 py-3 text-right text-green-600 font-medium">${formatNumberFR(row.total || 0)} FCFA</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs rounded-full ${row.etat === 'cloturee' ? 'bg-green-100 text-green-600' : row.etat === 'annulee' ? 'bg-red-100 text-red-600' : 'bg-indigo-100 text-indigo-600'}">${row.etat || 'N/A'}</span></td>
              <td class="px-4 py-3 text-gray-500">${new Date(row.date_commande).toLocaleDateString('fr-FR')}</td>
            </tr>`);

        renderRows('top-orders-body', result.top_commands || [], (row, idx) => `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-4 py-3 font-mono text-gray-700">CMD-${String(row.id).padStart(3,'0')}</td>
              <td class="px-4 py-3 text-gray-600">${row.client_nom || 'N/A'}</td>
              <td class="px-4 py-3 text-gray-500">${new Date(row.date_commande).toLocaleDateString('fr-FR')}</td>
              <td class="px-4 py-3 text-right text-green-600 font-medium">${formatNumberFR(row.total || 0)} FCFA</td>
            </tr>`);

        renderRows('top-sellers-body', result.top_sellers || [], (row, idx) => {
            const perf = Number(row.performance || 0);
            return `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-700 font-medium">${idx}</td>
              <td class="px-4 py-3 text-gray-600">${row.nom || 'N/A'}</td>
              <td class="px-4 py-3 text-right text-gray-600">${formatNumberFR(row.commandes || 0)}</td>
              <td class="px-4 py-3 text-right text-gray-600">${formatNumberFR(perf.toFixed(0))} FCFA</td>
              <td class="px-4 py-3 text-right text-green-600 font-medium">${formatNumberFR(row.montant_total || 0)} FCFA</td>
            </tr>`;
        });

        renderRows('top-clients-body', result.top_clients || [], (row, idx) => `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-700 font-medium">${idx}</td>
              <td class="px-4 py-3 text-gray-600">${row.nom || 'N/A'}</td>
              <td class="px-4 py-3 text-center text-gray-600">${formatNumberFR(row.commandes || 0)}</td>
              <td class="px-4 py-3 text-right text-green-600 font-medium">${formatNumberFR(row.montant_total || 0)} FCFA</td>
            </tr>`);

        renderRows('top-products-body', result.top_products || [], (row, idx) => `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-700 font-medium">${idx}</td>
              <td class="px-4 py-3 text-gray-600">${row.nom || 'N/A'}</td>
              <td class="px-4 py-3 text-gray-600">${row.categorie || 'N/A'}</td>
              <td class="px-4 py-3 text-right text-gray-600">${formatNumberFR(row.qte_vendue || 0)}</td>
              <td class="px-4 py-3 text-right text-green-600 font-medium">${formatNumberFR(row.ca || 0)} FCFA</td>
            </tr>`);

        const stockAlertsBody = document.getElementById('stock-alerts-body');
        if (stockAlertsBody) {
            stockAlertsBody.innerHTML = '';
            if (!Array.isArray(result.stock_alerts) || result.stock_alerts.length === 0) {
                stockAlertsBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Aucun produit critique</td></tr>';
            } else {
                result.stock_alerts.forEach((row) => {
                    const status = row.quantite <= 0 ? 'Rupture' : 'Stock bas';
                    stockAlertsBody.insertAdjacentHTML('beforeend', `
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-700">${row.nom || 'N/A'}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 ${row.quantite <= 0 ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600'} rounded-full text-xs font-medium">${formatNumberFR(row.quantite || 0)}</span></td>
                            <td class="px-4 py-3 text-center"><span class="text-${row.quantite <= 0 ? 'red' : 'orange'}-500 text-sm flex items-center justify-center gap-1"><i class="fas fa-exclamation-circle"></i> ${status}</span></td>
                        </tr>`);
                });
            }
        }

        // Update clients and products KPIs
        const clientsKpi = document.querySelector('#kpi-clients');
        if (clientsKpi) clientsKpi.textContent = formatNumberFR(result.clients_count || 0);
        const productsKpi = document.querySelector('#kpi-products');
        if (productsKpi) productsKpi.textContent = formatNumberFR(result.products_count || 0);

        // Charts
        if (revenueChart) revenueChart.destroy();
        const ctxRevenue = document.getElementById('revenueChart');
        if (ctxRevenue) {
          const ctx2d = ctxRevenue.getContext('2d');
          if (result.ca_by_day && result.ca_by_day.length > 0) {
            revenueChart = new Chart(ctx2d, {
              type: 'line',
              data: {
                labels: result.ca_by_day.map(d => new Date(d.jour).toLocaleDateString('fr-FR')),
                datasets: [{
                  label: 'Évolution CA',
                  data: result.ca_by_day.map(d => parseFloat(d.total_ca || 0)),
                  borderColor: function(ctx) {
                    const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, '#3B82F6');
                    gradient.addColorStop(0.5, '#60A5FA');
                    gradient.addColorStop(1, '#93C5FD');
                    return gradient;
                  },
                  backgroundColor: 'rgba(59, 130, 246, 0.08)',
                  tension: 0.5,
                  fill: true,
                  borderWidth: 3,
                  pointBackgroundColor: '#3B82F6',
                  pointBorderColor: '#ffffff',
                  pointBorderWidth: 2,
                  pointRadius: 6,
                  pointHoverRadius: 8
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false },
                scales: {
                  x: { 
                    grid: { 
                      color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)'
                    } 
                  },
                  y: {
                    beginAtZero: true,
                    grid: { 
                      color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)' 
                    },
                    ticks: { 
                      callback: (v) => formatNumberFR(v) + ' FCFA',
                      color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                    }
                  }
                },
                plugins: {
                  legend: { 
                    display: false,
                    labels: {
                      color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                    }
                  },
                  tooltip: {
                    backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(51,65,85,0.95)' : 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                      label: (ctx) => `${ctx.dataset.label}: ${formatNumberFR(ctx.parsed.y)} FCFA`
                    }
                  }
                },
                animation: {
                  tension: { duration: 1000, from: 1, to: 0.5 }
                }
              }
            });
          }
        }

const ctxCategory = document.getElementById('categoryChart');
  const legendEl = document.getElementById('category-legend');

  if (!ctxCategory || !result.category_ca) return;

  // 🎨 Couleurs
  const colors = [
    '#3B82F6','#10B981','#F59E0B','#EF4444',
    '#8B5CF6','#EC4899','#14B8A6','#F97316',
    '#6366F1','#84CC16'
  ];

  // 🔄 Détruire ancien graphique
  if (categoryChart) {
    categoryChart.destroy();
  }

  // 📊 Données
  const labels = result.category_ca.map(c => c.category_name);
  const dataValues = result.category_ca.map(c => parseFloat(c.ca || 0));

  // =========================
  // 📈 GRAPH DOUGHNUT
  // =========================
  categoryChart = new Chart(ctxCategory, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: dataValues,
        backgroundColor: colors.slice(0, dataValues.length),
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(51,65,85,0.95)' : 'rgba(0,0,0,0.8)',
          titleColor: document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#fff',
          bodyColor: document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#fff',
          callbacks: {
            label: function(ctx) {
              const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
              const percent = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
              return `${ctx.label}: ${formatNumberFR(ctx.parsed)} FCFA (${percent}%)`;
            }
          }
        }
      }
    },
    plugins: [{
      id: 'centerText',
      beforeDraw(chart) {
        const { width, height, ctx } = chart;
        const total = chart.data.datasets[0].data.reduce((a,b)=>a+b,0);

        ctx.save();
        ctx.font = "bold 16px sans-serif";
        ctx.fillStyle = document.documentElement.classList.contains('dark') ? "#F9FAFB" : "#111";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(formatNumberFR(total) + " FCFA", width / 2, height / 2);
        ctx.restore();
      }
    }]
  });

  // =========================
  // 📌 LEGEND PERSONNALISÉE
  // =========================
  if (legendEl) {
    legendEl.innerHTML = result.category_ca.map((c, i) => `
      <div class="flex items-center justify-between gap-3 py-1 px-3 rounded-lg hover:bg-gray-50 transition">

        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full"
               style="background-color: ${colors[i % colors.length]}"></div>
          <span class="text-sm text-gray-700">${c.category_name}</span>
        </div>

        <span class="text-sm font-bold text-green-600">
          ${formatNumberFR(c.ca)} FCFA
        </span>

      </div>
    `).join('');
  }

    } catch (err) {
        console.error('Erreur dashboard: ', err);
    }
}

function applyFilter() { loadDashboard(); }
function resetFilter() {
    const from = document.getElementById('dateFrom');
    const to = document.getElementById('dateTo');
    const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000);
    if (from) from.value = thirtyDaysAgo.toISOString().split('T')[0];
    if (to) to.value = new Date().toISOString().split('T')[0];
    loadDashboard();
}

document.addEventListener('DOMContentLoaded', function() {
  const from = document.getElementById('dateFrom');
  const to = document.getElementById('dateTo');
  if (from && to) {
    const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000);
    from.value = thirtyDaysAgo.toISOString().split('T')[0];
    to.value = new Date().toISOString().split('T')[0];
  }
});

window.addEventListener('DOMContentLoaded', () => {
    resetFilter();
});


