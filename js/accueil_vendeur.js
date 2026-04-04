function formatDashboardAmount(value) {
    return new Intl.NumberFormat('fr-FR').format(Number(value) || 0);
}

function getDashboardEtatBadge(etat) {
    if (etat === 'cloturee') return 'bg-green-100 text-green-600';
    if (etat === 'annulee') return 'bg-red-100 text-red-600';
    return 'bg-indigo-100 text-indigo-600';
}

async function loadVendeurDashboard(startDate = null, endDate = null) {
    const dashboard = document.getElementById('vendeur-dashboard');
    if (!dashboard) return;

    let url = '../php/post_dashboard_vendeur.php';
    const params = new URLSearchParams();
    if (startDate) params.set('start_date', startDate);
    if (endDate) params.set('end_date', endDate);
    if ([...params].length) url += `?${params.toString()}`;

    try {
        const response = await fetch(url);
        const result = await response.json();

        if (!result.success) {
            console.error('Erreur dashboard vendeur', result.message);
            return;
        }

        const seller = result.seller || {};
        const totals = result.totals || {};
        const statusCounts = result.status_counts || {};

        const vendeurName = document.getElementById('vendeur-name');
        const vendeurMeta = document.getElementById('vendeur-meta');
        if (vendeurName) vendeurName.textContent = seller.nom || 'Vendeur';
        if (vendeurMeta) vendeurMeta.textContent = [seller.email, seller.telephone].filter(Boolean).join(' | ') || 'Informations indisponibles';

        const setText = (id, value) => {
            const node = document.getElementById(id);
            if (node) node.textContent = value;
        };

        setText('dashboard-period', `Période : ${startDate || 'Tous'}${endDate ? ' à ' + endDate : ''}`);
        setText('vendeur-revenue', `${formatDashboardAmount(totals.revenue || 0)} FCFA`);
        setText('vendeur-commands', formatDashboardAmount(totals.commands || 0));
        setText('vendeur-clients', formatDashboardAmount(totals.clients || 0));
        setText('vendeur-items', formatDashboardAmount(totals.items || 0));
        setText('vendeur-en-cours', formatDashboardAmount(statusCounts.en_cours || 0));
        setText('vendeur-cloturees', formatDashboardAmount(statusCounts.cloturee || 0));
        setText('vendeur-annulees', formatDashboardAmount(statusCounts.annulee || 0));

        const recentOrders = document.getElementById('vendeur-recent-orders');
        if (recentOrders) {
            const rows = result.recent_commands || [];
            if (rows.length === 0) {
                recentOrders.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">Aucune commande enregistree</td></tr>';
            } else {
                recentOrders.innerHTML = rows.map((row) => `
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-3 font-medium text-slate-700">CMD-${String(row.id).padStart(3, '0')}</td>
                        <td class="px-3 py-3 text-slate-600">${row.client_nom || 'N/A'}</td>
                        <td class="px-3 py-3 text-slate-500">${new Date(row.date_commande).toLocaleDateString('fr-FR')}</td>
                        <td class="px-3 py-3 text-right font-semibold text-slate-700">${formatDashboardAmount(row.total || 0)} FCFA</td>
                        <td class="px-3 py-3 text-center">
                            <span class="rounded-full px-2 py-1 text-xs ${getDashboardEtatBadge(row.etat)}">${row.etat || 'N/A'}</span>
                        </td>
                    </tr>
                `).join('');
            }
        }

        const topProducts = document.getElementById('vendeur-top-products');
        if (topProducts) {
            const rows = result.top_products || [];
            if (rows.length === 0) {
                topProducts.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Aucune vente cloturee pour le moment</td></tr>';
            } else {
                topProducts.innerHTML = rows.map((row) => `
                    <tr class="border-b border-slate-100">
                        <td class="px-3 py-3 font-medium text-slate-700">${row.nom || 'N/A'}</td>
                        <td class="px-3 py-3 text-slate-600">${row.categorie || 'N/A'}</td>
                        <td class="px-3 py-3 text-right text-slate-600">${formatDashboardAmount(row.qte_vendue || 0)}</td>
                        <td class="px-3 py-3 text-right font-semibold text-slate-700">${formatDashboardAmount(row.ca || 0)} FCFA</td>
                    </tr>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Erreur chargement dashboard vendeur:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const startDateInput = document.getElementById('filter-start');
    const endDateInput = document.getElementById('filter-end');
    const filterButton = document.getElementById('filter-dashboard');
    const resetButton = document.getElementById('reset-dashboard');

    const loadSelected = () => {
        const start = startDateInput && startDateInput.value ? startDateInput.value : null;
        const end = endDateInput && endDateInput.value ? endDateInput.value : null;
        loadVendeurDashboard(start, end);
    };

    if (filterButton) {
        filterButton.addEventListener('click', () => {
            loadSelected();
        });
    }
    if (resetButton) {
        resetButton.addEventListener('click', () => {
            if (startDateInput) startDateInput.value = '';
            if (endDateInput) endDateInput.value = '';
            loadSelected();
        });
    }

    loadSelected();
});
