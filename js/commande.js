// Variables
let currentEditId = null;
let produitRowCounter = 0;
const produitCache = new Map();
const clientCache = new Map();
let defaultCommandeProduits = [];
let defaultCommandeClients = [];
const currentUserRole = document.body?.dataset.userRole || '';
const currentUserId = Number(document.body?.dataset.userId || 0);

// Modal elements
const modalAddCommande = document.getElementById("modalAddCommande");
const addCommande = document.getElementById("addCommande");
const cancelAddCommande = document.getElementById("cancelAddCommande");
const addProduitBtn = document.getElementById("addProduit");
const saveCommandeBtn = document.getElementById("saveCommande");
const searchInputCommande = document.getElementById("searchCommande");
const commandeBody = document.getElementById("commandeBody");
const totalDisplay = document.querySelector(".total-display");
const pendingCommandeId = (() => {
    const params = new URLSearchParams(window.location.search);
    const value = params.get("commande");
    return value ? parseInt(value, 10) : null;
})();
let pendingCommandeHandled = false;

// Initialize
document.addEventListener("DOMContentLoaded", function() {
    loadCommandes();
});

// Modal handlers
if (modalAddCommande && addCommande && cancelAddCommande) {
    addCommande.addEventListener("click", async () => {
        currentEditId = null;
        try {
            defaultCommandeClients = await searchClients("");
            defaultCommandeProduits = await searchProduits("");
        } catch (error) {
            console.error("Error preparing commande form:", error);
        }
        resetForm();
        loadClientsForSelect();
        modalAddCommande.classList.add("flex");
        modalAddCommande.classList.remove("hidden");
    });

    cancelAddCommande.addEventListener("click", () => {
        modalAddCommande.classList.add("hidden");
        modalAddCommande.classList.remove("flex");
    });
}

// Add product row
if (addProduitBtn) {
    addProduitBtn.addEventListener("click", () => {
        addProductRow();
    });
}

// Search functionality
if (searchInputCommande) {
    let searchTimer;
    searchInputCommande.addEventListener("input", (e) => {
        const value = e.target.value;
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            loadCommandes(value, currentCommandeEtat, 1);
        }, 300);
    });
}

const etatFilter = document.getElementById("etatFilter");
if (etatFilter) {
    etatFilter.addEventListener("change", (e) => {
        loadCommandes(currentCommandeSearch, e.target.value, 1);
    });
}

// Save commande
if (saveCommandeBtn) {
    saveCommandeBtn.addEventListener("submit", (e) => {
        e.preventDefault();
        saveCommande();
    });
}

// Functions
function resetForm() {
    const form = document.getElementById("saveCommande");
    if (form) {
        form.reset();
    }

    const clientMeta = document.querySelector(".client-meta");
    if (clientMeta) {
        clientMeta.textContent = "Saisissez au moins 2 caracteres";
    }

    const clientIdInput = document.querySelector(".client-id-input");
    if (clientIdInput) {
        clientIdInput.value = "";
    }
    
    // Reset table to one empty row
    commandeBody.innerHTML = buildProductRow();
    const firstRow = commandeBody.querySelector("tr");
    if (firstRow) {
        updateProduitDatalist(firstRow, defaultCommandeProduits);
    }
    updateTotal();
}

function addProductRow() {
    commandeBody.insertAdjacentHTML("beforeend", buildProductRow());
    const row = commandeBody.lastElementChild;
    if (row) {
        updateProduitDatalist(row, defaultCommandeProduits);
    }
}

function removeRow(btn) {
    const row = btn.closest("tr");
    const rows = commandeBody.querySelectorAll("tr");
    if (rows.length > 1) {
        row.remove();
        updateTotal();
    }
}

async function loadClientsForSelect() {
    updateClientDatalist(defaultCommandeClients);
}

async function searchClients(term = "") {
    const params = new URLSearchParams({
        page: "1",
        limit: "20",
        search: term
    });

    const response = await fetch(`../php/post_read_client.php?${params.toString()}`);
    const result = await response.json();
    return result.clients || [];
}

function updateClientDatalist(clients = []) {
    const datalist = document.querySelector(".client-datalist");
    if (!datalist) {
        return;
    }

    datalist.innerHTML = "";
    clients.forEach((client) => {
        clientCache.set(String(client.id), client);
        const option = document.createElement("option");
        option.value = `${client.nom} - ${client.telephone || "Sans telephone"}`;
        option.label = client.adresse || "";
        option.dataset.id = client.id;
        option.dataset.nom = client.nom;
        option.dataset.telephone = client.telephone || "";
        option.dataset.adresse = client.adresse || "";
        datalist.appendChild(option);
    });
}

function getSelectedClient() {
    const input = document.querySelector(".client-search");
    const datalist = document.querySelector(".client-datalist");
    const hiddenInput = document.querySelector(".client-id-input");

    if (!input || !datalist || !hiddenInput) {
        return null;
    }

    const selectedOption = Array.from(datalist.options).find(
        (option) => option.value.trim().toLowerCase() === input.value.trim().toLowerCase()
    );

    if (selectedOption) {
        return {
            id: selectedOption.dataset.id,
            nom: selectedOption.dataset.nom || "",
            telephone: selectedOption.dataset.telephone || "",
            adresse: selectedOption.dataset.adresse || ""
        };
    }

    if (hiddenInput.value && clientCache.has(hiddenInput.value)) {
        const cached = clientCache.get(hiddenInput.value);
        const cachedLabel = `${cached.nom} - ${cached.telephone || "Sans telephone"}`.trim().toLowerCase();
        if (cachedLabel === input.value.trim().toLowerCase()) {
            return cached;
        }
    }

    return null;
}

function setSelectedClient(client) {
    const input = document.querySelector(".client-search");
    const hiddenInput = document.querySelector(".client-id-input");
    const meta = document.querySelector(".client-meta");

    if (!input || !hiddenInput || !meta) {
        return;
    }

    if (!client) {
        hiddenInput.value = "";
        meta.textContent = "Aucun client valide selectionne";
        return;
    }

    clientCache.set(String(client.id), client);
    input.value = `${client.nom} - ${client.telephone || "Sans telephone"}`;
    hiddenInput.value = client.id;
    meta.textContent = client.adresse
        ? `Client: ${client.nom} | ${client.adresse}`
        : `Client: ${client.nom}`;
}

async function handleClientSearchInput(input) {
    const hiddenInput = document.querySelector(".client-id-input");
    const meta = document.querySelector(".client-meta");
    const matchedClient = getSelectedClient();
    if (matchedClient) {
        setSelectedClient(matchedClient);
        return;
    }

    if (hiddenInput) {
        hiddenInput.value = "";
    }

    const term = input.value.trim();
    if (term.length < 2) {
        updateClientDatalist(defaultCommandeClients);
        if (meta) {
            meta.textContent = "Saisissez au moins 2 caracteres";
        }
        return;
    }

    if (meta) {
        meta.textContent = "Recherche en cours...";
    }

    clearTimeout(input._clientSearchTimer);
    input._clientSearchTimer = setTimeout(async () => {
        try {
            const clients = await searchClients(term);
            updateClientDatalist(clients);
            if (meta) {
                meta.textContent = clients.length
                    ? `${clients.length} suggestion(s) disponibles`
                    : "Aucun client correspondant";
            }
        } catch (error) {
            console.error("Error searching clients:", error);
            if (meta) {
                meta.textContent = "Erreur lors de la recherche";
            }
        }
    }, 250);
}

function applyClientSelection() {
    const client = getSelectedClient();
    setSelectedClient(client);
}

function buildProductRow(detail = {}) {
    const datalistId = `produit-options-${++produitRowCounter}`;
    const produitNom = escapeHtml(detail.nom || "");
    const produitId = detail.id || "";
    const prix = detail.prix ?? 0;
    const quantite = detail.quantite ?? 1;
    const sousTotal = (Number(prix) || 0) * (Number(quantite) || 0);
    const metaText = detail.nom
        ? `Selectionne: ${escapeHtml(detail.nom)} | Prix catalogue: ${formatNumber(Number(prix) || 0)} FCFA`
        : "Saisissez au moins 2 caracteres";

    return `
        <tr>
            <td class="p-2">
                <div class="space-y-1">
                    <input
                        type="text"
                        class="w-full border rounded-md px-2 py-1 produit-search"
                        placeholder="Rechercher un produit..."
                        autocomplete="off"
                        list="${datalistId}"
                        value="${produitNom}"
                    >
                    <input type="hidden" class="produit-id-input" value="${produitId}">
                    <datalist id="${datalistId}" class="produit-datalist"></datalist>
                    <div class="text-[11px] text-gray-500 produit-meta">${metaText}</div>
                </div>
            </td>
            <td class="p-2">
                <input type="number" class="w-full border rounded-md px-2 py-1 prix-input" value="${prix}" min="0">
            </td>
            <td class="p-2">
                <input type="number" class="w-full border rounded-md px-2 py-1 quantite-input" value="${quantite}" min="1">
            </td>
            <td class="p-2 text-center font-medium sous-total">
                ${formatNumber(sousTotal)} FCFA
            </td>
            <td class="p-2 text-center">
                <button type="button" class="text-red-500" onclick="removeRow(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

async function searchProduits(term = "") {
    const params = new URLSearchParams({
        limit: "20",
        page: "1",
        search: term
    });

    const response = await fetch(`../php/post_read_produit.php?${params.toString()}`);
    const result = await response.json();
    return result.produits || [];
}

function updateProduitDatalist(row, produits = []) {
    const datalist = row.querySelector(".produit-datalist");
    if (!datalist) {
        return;
    }

    datalist.innerHTML = "";

    produits.forEach((produit) => {
        produitCache.set(String(produit.id), produit);

        const option = document.createElement("option");
        option.value = produit.nom;
        option.label = `${produit.code_barre || "Sans code"} | ${formatNumber(Number(produit.prix_vente) || 0)} FCFA`;
        option.dataset.id = produit.id;
        option.dataset.nom = produit.nom;
        option.dataset.prix = produit.prix_vente;
        option.dataset.codeBarre = produit.code_barre || "";
        datalist.appendChild(option);
    });
}

function getProduitFromRowSelection(row) {
    const produitInput = row.querySelector(".produit-search");
    const datalist = row.querySelector(".produit-datalist");
    const produitIdInput = row.querySelector(".produit-id-input");

    if (!produitInput || !datalist || !produitIdInput) {
        return null;
    }

    const selectedOption = Array.from(datalist.options).find(
        (option) => option.value.trim().toLowerCase() === produitInput.value.trim().toLowerCase()
    );

    if (selectedOption) {
        return {
            id: selectedOption.dataset.id,
            nom: selectedOption.dataset.nom || selectedOption.value,
            prix_vente: Number(selectedOption.dataset.prix) || 0,
            code_barre: selectedOption.dataset.codeBarre || ""
        };
    }

    if (produitIdInput.value && produitCache.has(produitIdInput.value)) {
        const cached = produitCache.get(produitIdInput.value);
        if (cached.nom.trim().toLowerCase() === produitInput.value.trim().toLowerCase()) {
            return cached;
        }
    }

    return null;
}

function setProduitOnRow(row, produit) {
    const produitInput = row.querySelector(".produit-search");
    const produitIdInput = row.querySelector(".produit-id-input");
    const prixInput = row.querySelector(".prix-input");
    const produitMeta = row.querySelector(".produit-meta");

    if (!produitInput || !produitIdInput || !prixInput || !produitMeta) {
        return;
    }

    if (!produit) {
        produitIdInput.value = "";
        prixInput.value = 0;
        produitMeta.textContent = "Aucun produit valide selectionne";
        updateSousTotal(row);
        updateTotal();
        return;
    }

    produitCache.set(String(produit.id), produit);
    produitInput.value = produit.nom || "";
    produitIdInput.value = produit.id;
    prixInput.value = Number(produit.prix_vente) || 0;
    produitMeta.textContent = `Selectionne: ${produit.nom} | Prix catalogue: ${formatNumber(Number(produit.prix_vente) || 0)} FCFA`;
    updateSousTotal(row);
    updateTotal();
}

async function handleProduitSearchInput(input) {
    const row = input.closest("tr");
    if (!row) {
        return;
    }

    const matchedProduit = getProduitFromRowSelection(row);
    if (matchedProduit) {
        setProduitOnRow(row, matchedProduit);
        return;
    }

    const produitIdInput = row.querySelector(".produit-id-input");
    const produitMeta = row.querySelector(".produit-meta");
    const prixInput = row.querySelector(".prix-input");
    const term = input.value.trim();

    if (produitIdInput) {
        produitIdInput.value = "";
    }

    if (prixInput) {
        prixInput.value = 0;
        updateSousTotal(row);
        updateTotal();
    }

    if (term.length < 2) {
        updateProduitDatalist(row, defaultCommandeProduits);
        if (produitMeta) {
            produitMeta.textContent = "Saisissez au moins 2 caracteres";
        }
        return;
    }

    if (produitMeta) {
        produitMeta.textContent = "Recherche en cours...";
    }

    clearTimeout(input._produitSearchTimer);
    input._produitSearchTimer = setTimeout(async () => {
        try {
            const produits = await searchProduits(term);
            updateProduitDatalist(row, produits);
            if (produitMeta) {
                produitMeta.textContent = produits.length
                    ? `${produits.length} suggestion(s) disponibles`
                    : "Aucun produit correspondant";
            }
        } catch (error) {
            console.error("Error searching produits:", error);
            if (produitMeta) {
                produitMeta.textContent = "Erreur lors de la recherche";
            }
        }
    }, 250);
}

function applyProduitSelection(input) {
    const row = input.closest("tr");
    if (!row) {
        return;
    }

    const produit = getProduitFromRowSelection(row);
    setProduitOnRow(row, produit);
}

// Event delegation for dynamic elements
document.addEventListener("input", function(e) {
    if (e.target.classList.contains("client-search")) {
        handleClientSearchInput(e.target);
    }

    if (e.target.classList.contains("produit-search")) {
        handleProduitSearchInput(e.target);
    }

    if (e.target.classList.contains("prix-input") || e.target.classList.contains("quantite-input")) {
        const row = e.target.closest("tr");
        updateSousTotal(row);
        updateTotal();
    }
});

document.addEventListener("change", function(e) {
    if (e.target.classList.contains("client-search")) {
        applyClientSelection();
    }

    if (e.target.classList.contains("produit-search")) {
        applyProduitSelection(e.target);
    }

    if (e.target.classList.contains("prix-input") || e.target.classList.contains("quantite-input")) {
        const row = e.target.closest("tr");
        updateSousTotal(row);
        updateTotal();
    }
});

document.addEventListener("focus", function(e) {
    if (e.target.classList.contains("client-search")) {
        const datalist = document.querySelector(".client-datalist");
        if (datalist && !datalist.options.length) {
            updateClientDatalist(defaultCommandeClients);
        }
    }

    if (e.target.classList.contains("produit-search")) {
        const row = e.target.closest("tr");
        const datalist = row?.querySelector(".produit-datalist");
        if (row && datalist && !datalist.options.length) {
            updateProduitDatalist(row, defaultCommandeProduits);
        }
    }
}, true);

document.addEventListener("blur", function(e) {
    if (e.target.classList.contains("client-search")) {
        applyClientSelection();
    }

    if (e.target.classList.contains("produit-search")) {
        applyProduitSelection(e.target);
    }
}, true);

function updateSousTotal(row) {
    if (!row) {
        return;
    }

    const prixInput = row.querySelector(".prix-input");
    const quantiteInput = row.querySelector(".quantite-input");
    const sousTotalNode = row.querySelector(".sous-total");

    if (!prixInput || !quantiteInput || !sousTotalNode) {
        return;
    }

    const prix = parseFloat(prixInput.value) || 0;
    const quantite = parseInt(quantiteInput.value) || 0;
    const sousTotal = prix * quantite;
    sousTotalNode.textContent = formatNumber(sousTotal) + " FCFA";
}

function updateTotal() {
    if (!commandeBody) {
        return;
    }

    const rows = commandeBody.querySelectorAll("tr");
    let total = 0;
    rows.forEach(row => {
        const prixInput = row.querySelector(".prix-input");
        const quantiteInput = row.querySelector(".quantite-input");
        if (!prixInput || !quantiteInput) {
            return;
        }

        const prix = parseFloat(prixInput.value) || 0;
        const quantite = parseInt(quantiteInput.value) || 0;
        total += prix * quantite;
    });
    
    const totalElement = document.querySelector(".total-display");
    if (totalElement) {
        totalElement.textContent = formatNumber(total) + " FCFA";
    } else {
        // Update the total display in the form
        const totalContainer = document.querySelector(".bg-gray-100.flex.justify-end.mt-6");
        if (totalContainer) {
            totalContainer.querySelector("span:last-child").textContent = formatNumber(total) + " FCFA";
        }
    }
}

async function saveCommande() {
    const clientIdInput = document.querySelector(".client-id-input");
    const id_client = clientIdInput ? clientIdInput.value : null;
    
    if (!id_client) {
        alert("Veuillez sélectionner un client");
        return;
    }
    
    // Collect products
    const rows = commandeBody.querySelectorAll("tr");
    const produitsData = [];
    
    rows.forEach(row => {
        const produitIdInput = row.querySelector(".produit-id-input");
        const id_produit = produitIdInput ? produitIdInput.value : null;
        const prix = parseFloat(row.querySelector(".prix-input").value) || 0;
        const quantite = parseInt(row.querySelector(".quantite-input").value) || 0;
        
        if (id_produit && quantite > 0) {
            produitsData.push({
                id_produit: parseInt(id_produit),
                prix: prix,
                quantite: quantite
            });
        }
    });
    
    if (produitsData.length === 0) {
        alert("Veuillez ajouter au moins un produit");
        return;
    }
    
    const data = {
        id_client: parseInt(id_client),
        produits: produitsData
    };
    
    let url = "../php/post_create_commande.php";
    let method = "POST";
    
    if (currentEditId) {
        url = "../php/post_update_commande.php";
        data.id = currentEditId;
    }
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(currentEditId ? "Commande mise à jour avec succès" : "Commande créée avec succès");
            modalAddCommande.classList.add("hidden");
            modalAddCommande.classList.remove("flex");
            loadCommandes();
        } else {
            alert(result.message || "Erreur lors de l'enregistrement");
        }
    } catch (error) {
        console.error("Error saving commande:", error);
        alert("Erreur lors de l'enregistrement");
    }
}

let currentCommandePage = 1;
let currentCommandeSearch = '';
let currentCommandeEtat = '';

async function loadCommandes(search = '', etat = '', page = 1) {
    const paginationEl = document.getElementById("paginationCommandes");
    
    currentCommandePage = page;
    currentCommandeSearch = search || '';
    currentCommandeEtat = etat || '';
    
    try {
        const params = new URLSearchParams({
            page: page,
            limit: 10,
            search: currentCommandeSearch,
            etat: currentCommandeEtat
        });
        
        const response = await fetch("../php/post_read_commande.php?" + params.toString());
        const result = await response.json();
        
        if (result.success) {
            displayCommandes(result.commandes || []);
            handlePendingCommandeFocus(result.commandes || []);
            updateCounts(result.counts || { en_cours: 0, cloturee: 0, annulee: 0 });
            
            // Render pagination
            if (paginationEl && result.total_pages > 1) {
                renderCommandesPagination(result.total_pages, result.current_page);
            } else if (paginationEl) {
                paginationEl.innerHTML = '';
            }
            
            totalCommande();
        }
    } catch (error) {
        console.error("Error loading commandes:", error);
    }
}

function handlePendingCommandeFocus(commandes = []) {
    if (!pendingCommandeId || pendingCommandeHandled) {
        return;
    }

    const targetCommande = commandes.find((commande) => Number(commande.id) === Number(pendingCommandeId));
    if (!targetCommande) {
        return;
    }

    pendingCommandeHandled = true;

    setTimeout(() => {
        const row = document.querySelector(`[data-commande-id="${pendingCommandeId}"]`);
        if (row) {
            row.classList.add("bg-blue-50");
            row.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        viewCommande(pendingCommandeId);
    }, 150);

    const url = new URL(window.location.href);
    url.searchParams.delete("commande");
    window.history.replaceState({}, "", url.toString());
}

function renderCommandesPagination(totalPages, currentPage) {
    const paginationEl = document.getElementById("paginationCommandes");
    if (!paginationEl) return;
    
    paginationEl.innerHTML = '';
    
    // Previous
    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = '&laquo; Préc';
    prevBtn.className = 'px-3 py-2 text-sm font-medium rounded-lg dark:bg-slate-600/50 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300 transition';
    prevBtn.disabled = currentPage <= 1;
    prevBtn.onclick = () => loadCommandes(currentCommandeSearch, currentCommandeEtat, currentPage - 1);
    paginationEl.appendChild(prevBtn);
    
    // Pages
    const delta = 2;
    const range = [];
    for (let i = Math.max(2, currentPage - delta); i <= Math.min(totalPages - 1, currentPage + delta); i++) {
        range.push(i);
    }
    
    if (currentPage - delta > 2) {
        const firstBtn = document.createElement('button');
        firstBtn.textContent = '1';
        firstBtn.className = 'px-3 py-2 text-sm font-medium rounded-lg dark:bg-slate-600/50 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
        firstBtn.onclick = () => loadCommandes(currentCommandeSearch, currentCommandeEtat, 1);
        paginationEl.appendChild(firstBtn);
        if (currentPage - delta > 3) paginationEl.appendChild(document.createTextNode('...'));
    }
    
    range.forEach(i => {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `px-3 py-2 text-sm font-medium rounded-lg ${i === currentPage ? 'bg-blue-600 text-white dark:bg-slate-500 shadow-md' : 'dark:bg-slate-600/50 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300'}`;
        btn.onclick = () => loadCommandes(currentCommandeSearch, currentCommandeEtat, i);
        paginationEl.appendChild(btn);
    });
    
    if (currentPage + delta < totalPages - 1) {
        if (currentPage + delta < totalPages - 2) paginationEl.appendChild(document.createTextNode('...'));
        const lastBtn = document.createElement('button');
        lastBtn.textContent = totalPages;
        lastBtn.className = 'px-3 py-2 text-sm font-medium rounded-lg dark:bg-slate-600/50 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
        lastBtn.onclick = () => loadCommandes(currentCommandeSearch, currentCommandeEtat, totalPages);
        paginationEl.appendChild(lastBtn);
    }
    
    // Next
    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = 'Suiv &raquo;';
    nextBtn.className = 'px-3 py-2 text-sm font-medium rounded-lg dark:bg-slate-600/50 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300 transition';
    nextBtn.disabled = currentPage >= totalPages;
    nextBtn.onclick = () => loadCommandes(currentCommandeSearch, currentCommandeEtat, currentPage + 1);
    paginationEl.appendChild(nextBtn);
}

function displayCommandes(commandes) {
    const tbody = document.getElementById("commandesTable");
    if (!tbody) return;
    
    tbody.innerHTML = "";
    
    commandes.forEach((cmd, index) => {
        const etatClass = getEtatClass(cmd.etat);
        const etatLabel = getEtatLabel(cmd.etat);
        const tr = document.createElement("tr");
        tr.className = "border-b hover:bg-gray-50";
        tr.dataset.commandeId = cmd.id;
        tr.innerHTML = `
            <td class="p-3 font-medium">CMD-${String(cmd.id).padStart(3, '0')}</td>
            <td class="p-3">${cmd.client_nom || 'N/A'}</td>
            ${currentUserRole === 'admin' ? `<td class="p-3">${cmd.user_nom || 'N/A'}</td>` : ''}
            <td class="p-3">${formatDate(cmd.date_commande)}</td>
            <td class="p-3 font-semibold text-blue-600">${formatNumber(cmd.total)} FCFA</td>
            <td class="p-3">
                <span class="bg-${etatClass}-100 text-${etatClass}-600 text-xs px-2 py-1 rounded">
                    ${etatLabel}
                </span>
            </td>
            <td class="p-3 flex justify-center gap-2">
                <button class="bg-blue-50 text-blue-600 p-2 rounded hover:bg-blue-100" onclick="viewCommande(${cmd.id})">
                    <i class="fa-solid fa-eye"></i>
                </button>
                ${cmd.etat === 'en_cours' ? `
                    <button class="bg-yellow-50 text-yellow-600 p-2 rounded hover:bg-yellow-100" onclick="editCommande(${cmd.id})">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="bg-green-50 text-green-600 p-2 rounded hover:bg-green-100" onclick="clotureCommande(${cmd.id})" title="Clôturer">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    <button class="bg-red-50 text-red-600 p-2 rounded hover:bg-red-100" onclick="annuleCommande(${cmd.id})" title="Annuler">
                        <i class="fa-solid fa-ban"></i>
                    </button>
                ` : ''}
                ${cmd.etat !== 'cloturee' ? `
                    <button class="bg-red-50 text-red-600 p-2 rounded hover:bg-red-100" onclick="deleteCommande(${cmd.id})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ` : ''}
                ${cmd.etat === 'cloturee' ? `
                    <button class="bg-purple-50 text-purple-600 p-2 rounded hover:bg-purple-100" onclick="viewFacture(${cmd.id})" title="Voir facture">
                        <i class="fa-solid fa-file-invoice"></i>
                    </button>
                ` : ''}
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    if (commandes.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${currentUserRole === 'admin' ? '7' : '6'}" class="p-4 text-center text-gray-500">Aucune commande trouvée</td></tr>`;
    }
}

function updateCounts(counts) {
    // Update the cards with actual counts by ID if present
    const enCoursEl = document.getElementById("countEnCours");
    const clotureeEl = document.getElementById("countCloturee");
    const annuleeEl = document.getElementById("countAnnulee");
    const enCoursVendeurEl = document.getElementById("countEnCoursVendeur");
    const clotureeVendeurEl = document.getElementById("countClotureeVendeur");
    const annuleeVendeurEl = document.getElementById("countAnnuleeVendeur");

    if (enCoursEl) {
        enCoursEl.textContent = counts.en_cours || 0;
    }
    if (clotureeEl) {
        clotureeEl.textContent = counts.cloturee || 0;
    }
    if (annuleeEl) {
        annuleeEl.textContent = counts.annulee || 0;
    }
    if (enCoursVendeurEl) {
        enCoursVendeurEl.textContent = counts.en_cours || 0;
    }
    if (clotureeVendeurEl) {
        clotureeVendeurEl.textContent = counts.cloturee || 0;
    }
    if (annuleeVendeurEl) {
        annuleeVendeurEl.textContent = counts.annulee || 0;
    }
}

function getEtatClass(etat) {
    switch(etat) {
        case 'en_cours': return 'indigo';
        case 'cloturee': return 'green';
        case 'annulee': return 'red';
        default: return 'gray';
    }
}

function getEtatLabel(etat) {
    switch(etat) {
        case 'en_cours': return 'En cours';
        case 'cloturee': return 'Clôturée';
        case 'annulee': return 'Annulée';
        default: return etat;
    }
}

function formatNumber(number) {
    return new Intl.NumberFormat('fr-FR').format(number);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Action functions
async function viewCommande(id) {
    try {
        const response = await fetch("../php/post_get_commande_details.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayCommandeDetails(result.commande);
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error loading commande:", error);
    }
}

async function editCommande(id) {
    currentEditId = id;
    
    try {
        const response = await fetch("../php/post_get_commande_details.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const cmd = result.commande;
            
            // Load form data
            defaultCommandeClients = await searchClients("");
            defaultCommandeProduits = await searchProduits("");
            await loadClientsForSelect();
            
            // Set client
            setSelectedClient({
                id: cmd.id_client,
                nom: cmd.client?.nom || "",
                telephone: cmd.client?.telephone || "",
                adresse: cmd.client?.adresse || ""
            });
            
            // Clear and populate products
            commandeBody.innerHTML = "";
            
            if (cmd.details && cmd.details.length > 0) {
                cmd.details.forEach((detail) => {
                    const produit = {
                        id: detail.id_produit,
                        nom: detail.produit_nom || "",
                        prix_vente: detail.prix,
                        code_barre: detail.code_barre || ""
                    };
                    produitCache.set(String(detail.id_produit), produit);
                    commandeBody.insertAdjacentHTML("beforeend", buildProductRow({
                        id: detail.id_produit,
                        nom: detail.produit_nom || "",
                        prix: detail.prix,
                        quantite: detail.quantite
                    }));
                });
                updateTotal();
            } else {
                commandeBody.innerHTML = buildProductRow();
            }
            
            // Show modal
            modalAddCommande.classList.add("flex");
            modalAddCommande.classList.remove("hidden");
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error("Error loading commande for edit:", error);
    }
}

async function clotureCommande(id) {
    if (!confirm("Êtes-vous sûr de vouloir clôturer cette commande? Le stock sera déduit et une facture sera générée.")) {
        return;
    }
    
    try {
        const response = await fetch("../php/post_cloture_commande.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            let successMessage = "Commande clôturée avec succès! Facture générée.";
            if (Array.isArray(result.low_stock_products) && result.low_stock_products.length > 0) {
                const produitsAlert = result.low_stock_products
                    .map((produit) => `${produit.nom} (${produit.quantite} restant(s), seuil ${produit.seuil})`)
                    .join("\n");
                successMessage += `\n\nAlerte stock bas:\n${produitsAlert}`;
            }
            alert(successMessage);
            loadCommandes();
            totalCommande();
            fidelite()
        } else {
            alert(result.message || "Erreur lors de la clôture");
        }
    } catch (error) {
        console.error("Error closing commande:", error);
        alert("Erreur lors de la clôture");
    }
}

async function annuleCommande(id) {
    if (!confirm("Êtes-vous sûr de vouloir annuler cette commande?")) {
        return;
    }
    
    try {
        const response = await fetch("../php/post_annule_commande.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert("Commande annulée avec succès");
            loadCommandes();
        } else {
            alert(result.message || "Erreur lors de l'annulation");
        }
    } catch (error) {
        console.error("Error canceling commande:", error);
        alert("Erreur lors de l'annulation");
    }
}

async function deleteCommande(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cette commande?")) {
        return;
    }
    
    try {
        const response = await fetch("../php/post_delete_commande.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert("Commande supprimée avec succès");
            loadCommandes();
        } else {
            alert(result.message || "Erreur lors de la suppression");
        }
    } catch (error) {
        console.error("Error deleting commande:", error);
        alert("Erreur lors de la suppression");
    }
}

function displayCommandeDetails(cmd) {
    document.body.style.overflow = 'hidden';

    const totalProduits = cmd.details?.reduce((sum, d) => sum + d.quantite, 0) || 0;

    if (!document.getElementById('cmd-detail-styles')) {
        const style = document.createElement('style');
        style.id = 'cmd-detail-styles';
        style.textContent = `
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

            #cmdDetailOverlay {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            @keyframes cmdOverlayIn {
                from { opacity: 0; }
                to   { opacity: 1; }
            }
            @keyframes cmdPanelIn {
                from { opacity: 0; transform: translateY(24px) scale(0.98); }
                to   { opacity: 1; transform: translateY(0)   scale(1);    }
            }
            @keyframes cmdRowIn {
                from { opacity: 0; transform: translateX(-8px); }
                to   { opacity: 1; transform: translateX(0);    }
            }

            #cmdDetailOverlay { animation: cmdOverlayIn 0.25s ease both; }

            #cmdDetailPanel { animation: cmdPanelIn 0.35s cubic-bezier(0.22,1,0.36,1) both; }

            .cmd-row-anim {
                animation: cmdRowIn 0.3s ease both;
            }

            .cmd-stat-card {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 12px;
                padding: 14px 16px;
                transition: box-shadow 0.2s, transform 0.2s;
            }
            .cmd-stat-card:hover {
                box-shadow: 0 6px 20px rgba(0,0,0,0.07);
                transform: translateY(-1px);
            }

            .cmd-table tbody tr {
                transition: background 0.15s;
            }
            .cmd-table tbody tr:hover {
                background: #f7f8fc;
            }

            .cmd-close-btn {
                width: 32px; height: 32px;
                display: flex; align-items: center; justify-content: center;
                border-radius: 8px;
                border: 1px solid rgba(255,255,255,0.2);
                background: rgba(255,255,255,0.1);
                cursor: pointer;
                transition: background 0.2s;
                color: white;
            }
            .cmd-close-btn:hover { background: rgba(255,255,255,0.22); }

            .cmd-badge-en_attente  { background:#fef3c7; color:#92400e; }
            .cmd-badge-en_cours    { background:#dbeafe; color:#1e40af; }
            .cmd-badge-livree      { background:#d1fae5; color:#065f46; }
            .cmd-badge-annulee     { background:#fee2e2; color:#991b1b; }
            .cmd-badge-default     { background:#f1f5f9; color:#475569; }

            .cmd-progress-bar {
                height: 4px;
                border-radius: 2px;
                background: #e2e8f0;
                overflow: hidden;
                margin-top: 6px;
            }
            .cmd-progress-fill {
                height: 100%;
                border-radius: 2px;
                background: linear-gradient(90deg, #3b82f6, #6366f1);
                transition: width 0.8s cubic-bezier(0.22,1,0.36,1);
            }

            @media (max-width: 480px) {
                #cmdDetailPanel { border-radius: 16px 16px 0 0 !important; }
                #cmdDetailOverlay { align-items: flex-end !important; padding: 0 !important; }
            }
        `;
        document.head.appendChild(style);
    }

    // Badge statut
    const badgeClass = {
        'en_attente': 'cmd-badge-en_attente',
        'en_cours':   'cmd-badge-en_cours',
        'livree':     'cmd-badge-livree',
        'annulee':    'cmd-badge-annulee',
    }[cmd.etat] || 'cmd-badge-default';

    const etatLabel = getEtatLabel(cmd.etat);

    // Couleur accent header selon statut
    const headerGrad = {
        'livree':     'linear-gradient(135deg, #059669 0%, #0d9488 100%)',
        'annulee':    'linear-gradient(135deg, #dc2626 0%, #9f1239 100%)',
        'en_cours':   'linear-gradient(135deg, #2563eb 0%, #4f46e5 100%)',
        'en_attente': 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
    }[cmd.etat] || 'linear-gradient(135deg, #1e293b 0%, #334155 100%)';

    // Lignes produits
    let produitsRows = '';
    if (cmd.details && cmd.details.length > 0) {
        cmd.details.forEach((detail, i) => {
            const pct = cmd.total > 0 ? Math.round((detail.sous_total / cmd.total) * 100) : 0;
            produitsRows += `
                <tr class="cmd-row-anim" style="animation-delay:${0.05 + i * 0.04}s; border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px 14px;">
                        <div style="font-weight:600; font-size:13px; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;" title="${detail.produit_nom || 'Produit'}">
                            ${detail.produit_nom || 'Produit'}
                        </div>
                        <div class="cmd-progress-bar" style="max-width:100px;">
                            <div class="cmd-progress-fill" style="width:${pct}%"></div>
                        </div>
                    </td>
                    <td style="padding: 10px 14px; text-align:center; font-size:12px; color:#64748b; font-family:'JetBrains Mono', monospace;">
                        ${formatNumber(detail.prix)}
                    </td>
                    <td style="padding: 10px 14px; text-align:center;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; min-width:28px; height:22px; background:#f1f5f9; border-radius:6px; font-size:12px; font-weight:600; color:#334155; padding: 0 7px;">
                            ${detail.quantite}
                        </span>
                    </td>
                    <td style="padding: 10px 14px; text-align:right; font-family:'JetBrains Mono', monospace; font-size:13px; font-weight:600; color:#3b82f6; white-space:nowrap;">
                        ${formatNumber(detail.sous_total)} <span style="font-size:10px; font-weight:400; color:#94a3b8;">FCFA</span>
                    </td>
                </tr>
            `;
        });
    } else {
        produitsRows = `
            <tr>
                <td colspan="4" style="padding: 32px; text-align:center; color:#94a3b8; font-size:13px;">
                    <i class="fas fa-box-open" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.5;"></i>
                    Aucun produit dans cette commande
                </td>
            </tr>
        `;
    }

    // Overlay
    const overlay = document.createElement('div');
    overlay.id = 'cmdDetailOverlay';
    overlay.style.cssText = `
        position:fixed; inset:0; z-index:9999;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(6px);
        display:flex; align-items:center; justify-content:center;
        padding: 16px;
    `;

    overlay.innerHTML = `
        <div id="cmdDetailPanel" style="
            background:#f8fafc;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 32px 64px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.08);
        ">

            <!-- HEADER -->
            <div style="background:${headerGrad}; padding: 20px 22px 18px; flex-shrink:0; position:relative; overflow:hidden;">
                <!-- Cercles décoratifs -->
                <div style="position:absolute; top:-30px; right:-30px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.07); pointer-events:none;"></div>
                <div style="position:absolute; bottom:-40px; left:20px; width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none;"></div>

                <div style="position:relative; display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <span style="font-size:10px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:rgba(255,255,255,0.6);">
                                Détail commande
                            </span>
                        </div>
                        <div style="font-family:'JetBrains Mono', monospace; font-size:22px; font-weight:500; color:#fff; letter-spacing:0.03em;">
                            CMD-${String(cmd.id).padStart(3, '0')}
                        </div>
                        <div style="margin-top:6px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span style="font-size:11px; color:rgba(255,255,255,0.65);">
                                <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>${formatDate(cmd.date_commande)}
                            </span>
                            <span style="font-size:11px; padding: 2px 8px; border-radius:20px; background:rgba(255,255,255,0.18); color:#fff; font-weight:500;">
                                ${etatLabel}
                            </span>
                        </div>
                    </div>
                    <button class="cmd-close-btn close-modal-btn">
                        <i class="fas fa-times" style="font-size:13px;"></i>
                    </button>
                </div>

                <!-- Montant total en avant -->
                <div style="margin-top:14px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.15); display:flex; align-items:baseline; gap:6px;">
                    <span style="font-size:11px; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.06em;">Total</span>
                    <span style="font-size:26px; font-weight:700; color:#fff; line-height:1;">${formatNumber(cmd.total)}</span>
                    <span style="font-size:12px; color:rgba(255,255,255,0.6); font-weight:500;">FCFA</span>
                </div>
            </div>

            <!-- BODY SCROLLABLE -->
            <div style="overflow-y:auto; flex:1; padding:18px;">

                <!-- Stats cards -->
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:18px;">
                    <div class="cmd-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Client</div>
                        <div style="font-size:13px; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${cmd.client?.nom || 'N/A'}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">${cmd.client?.telephone || '—'}</div>
                    </div>
                    <div class="cmd-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Articles</div>
                        <div style="font-size:22px; font-weight:700; color:#1e293b; line-height:1.1;">${totalProduits}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">${cmd.details?.length || 0} référence(s)</div>
                    </div>
                    <div class="cmd-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Statut</div>
                        <div style="margin-top:4px;">
                            <span class="${badgeClass}" style="font-size:11px; font-weight:600; padding: 3px 10px; border-radius:20px; display:inline-block;">
                                ${etatLabel}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Adresse client si dispo -->
                ${cmd.client?.adresse ? `
                <div style="background:#fff; border:1px solid #e8eaf0; border-radius:12px; padding:12px 14px; margin-bottom:18px; display:flex; align-items:flex-start; gap:10px;">
                    <div style="width:32px; height:32px; flex-shrink:0; background:#eff6ff; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-map-marker-alt" style="color:#3b82f6; font-size:13px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:2px;">Adresse du client</div>
                        <div style="font-size:13px; color:#334155;">${cmd.client.adresse}</div>
                    </div>
                </div>
                ` : ''}

                <!-- Tableau produits -->
                <div style="background:#fff; border:1px solid #e8eaf0; border-radius:14px; overflow:hidden;">
                    <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:600; color:#1e293b; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-boxes" style="color:#3b82f6; font-size:11px;"></i>
                            Produits commandés
                        </span>
                        <span style="font-size:11px; background:#f1f5f9; color:#64748b; padding:2px 10px; border-radius:20px; font-weight:500;">
                            ${cmd.details?.length || 0} article(s)
                        </span>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="cmd-table" style="width:100%; border-collapse:collapse; min-width:360px;">
                            <thead>
                                <tr style="background:#fafbfc;">
                                    <th style="padding:8px 14px; text-align:left; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Produit</th>
                                    <th style="padding:8px 14px; text-align:center; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Prix unit.</th>
                                    <th style="padding:8px 14px; text-align:center; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Qté</th>
                                    <th style="padding:8px 14px; text-align:right; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${produitsRows}
                            </tbody>
                        </table>
                    </div>

                    <!-- Total ligne footer tableau -->
                    <div style="padding:12px 14px; background:#f8fafc; border-top:2px solid #e8eaf0; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:600; color:#64748b;">${totalProduits} article(s) au total</span>
                        <div style="text-align:right;">
                            <div style="font-size:10px; color:#94a3b8; font-weight:500; text-transform:uppercase; letter-spacing:0.08em;">Montant total</div>
                            <div style="font-family:'JetBrains Mono', monospace; font-size:18px; font-weight:700; color:#1e293b;">
                                ${formatNumber(cmd.total)} <span style="font-size:11px; font-weight:400; color:#94a3b8;">FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div style="padding:14px 18px; border-top:1px solid #e8eaf0; background:#fff; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;">
                <button class="close-modal-btn" style="
                    padding: 9px 20px;
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    font-size: 13px; font-weight: 600;
                    color: #64748b;
                    background: #f1f5f9;
                    border: none; border-radius: 10px;
                    cursor: pointer; transition: background 0.2s;
                " onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    Fermer
                </button>
                
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Fermeture
    const closeModal = () => {
        overlay.style.animation = 'cmdOverlayIn 0.2s ease reverse both';
        document.body.style.overflow = '';
        setTimeout(() => overlay.remove(), 200);
    };

    overlay.querySelectorAll('.close-modal-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    // Escape key
    const onKey = (e) => { if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', onKey); } };
    document.addEventListener('keydown', onKey);
}

// Supprime l'ancien style en bas s'il existe
// Ne garde que cette fonction

// Ajoute cette animation dans ton CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

function closeFactureModal() {
    const modal = document.getElementById("factureModal");
    if (modal) {
        modal.remove();
    }
}

async function generatePDF(commandeId) {
    try {
        const response = await fetch("../php/post_generate_pdf.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id_commande: commandeId })
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `facture_${commandeId}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } else {
            alert("Erreur lors de la génération du PDF");
        }
    } catch (error) {
        console.error("Error generating PDF:", error);
        alert("Erreur lors de la génération du PDF");
    }
}

async function viewFacture(id) {
    try {
        const response = await fetch("../php/post_get_commande.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        
        if (result.success && result.commande.facture) {
            displayFacture(result.commande);
        } else {
            alert("Facture non trouvée");
        }
    } catch (error) {
        console.error("Error loading facture:", error);
    }
}


function displayFacture(cmd) {
    const fact = cmd.facture;

    document.body.style.overflow = 'hidden';

    const totalProduits = fact.details?.reduce((sum, d) => sum + d.quantite, 0) || 0;

    if (!document.getElementById('facture-detail-styles')) {
        const style = document.createElement('style');
        style.id = 'facture-detail-styles';
        style.textContent = `
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

            #factureDetailOverlay { font-family: 'Plus Jakarta Sans', sans-serif; }

            @keyframes factOverlayIn {
                from { opacity: 0; }
                to   { opacity: 1; }
            }
            @keyframes factPanelIn {
                from { opacity: 0; transform: translateY(24px) scale(0.98); }
                to   { opacity: 1; transform: translateY(0)   scale(1);    }
            }
            @keyframes factRowIn {
                from { opacity: 0; transform: translateX(-8px); }
                to   { opacity: 1; transform: translateX(0);    }
            }

            #factureDetailOverlay { animation: factOverlayIn 0.25s ease both; }
            #factureDetailPanel   { animation: factPanelIn 0.35s cubic-bezier(0.22,1,0.36,1) both; }

            .fact-row-anim { animation: factRowIn 0.3s ease both; }

            .fact-stat-card {
                background: #fff;
                border: 1px solid #e8eaf0;
                border-radius: 12px;
                padding: 14px 16px;
                transition: box-shadow 0.2s, transform 0.2s;
            }
            .fact-stat-card:hover {
                box-shadow: 0 6px 20px rgba(0,0,0,0.07);
                transform: translateY(-1px);
            }

            .fact-table tbody tr { transition: background 0.15s; }
            .fact-table tbody tr:hover { background: #f7f8fc; }

            .fact-close-btn {
                width: 32px; height: 32px;
                display: flex; align-items: center; justify-content: center;
                border-radius: 8px;
                border: 1px solid rgba(255,255,255,0.2);
                background: rgba(255,255,255,0.1);
                cursor: pointer;
                transition: background 0.2s;
                color: white;
            }
            .fact-close-btn:hover { background: rgba(255,255,255,0.22); }

            .fact-progress-bar {
                height: 4px; border-radius: 2px;
                background: #e2e8f0; overflow: hidden; margin-top: 6px;
            }
            .fact-progress-fill {
                height: 100%; border-radius: 2px;
                background: linear-gradient(90deg, #10b981, #059669);
                transition: width 0.8s cubic-bezier(0.22,1,0.36,1);
            }

            @media (max-width: 480px) {
                #factureDetailPanel   { border-radius: 16px 16px 0 0 !important; }
                #factureDetailOverlay { align-items: flex-end !important; padding: 0 !important; }
            }

@media print {
  /* Hide ALL page elements except facture */
  body > *:not(#factureDetailOverlay),
  #sidebar,
  header,
  main > *:not(#factureDetailOverlay),
  nav,
  .no-print { 
    display: none !important; 
  }
  
  /* Position facture as full printable page */
  #factureDetailOverlay {
    position: static !important;
    background: none !important;
    backdrop-filter: none !important;
    display: block !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    max-width: none !important;
    max-height: none !important;
    z-index: auto !important;
  }
  
  #factureDetailPanel {
    position: static !important;
    box-shadow: none !important;
    border: none !important;
    border-radius: 0 !important;
    max-width: none !important;
    max-height: none !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    min-height: 100vh !important;
    animation: none !important;
  }
  
  /* Hide modal footer buttons */
  .fact-no-print { 
    display: none !important; 
  }
  
  /* Print page setup */
  @page {
    margin: 1cm;
    size: A4;
  }
  
  body {
    margin: 0 !important;
    padding: 0 !important;
    font-size: 12pt !important;
  }
}
        `;
        document.head.appendChild(style);
    }

    // Lignes produits
    let produitsRows = '';
    if (fact.details && fact.details.length > 0) {
        fact.details.forEach((detail, i) => {
            const pct = cmd.total > 0 ? Math.round((detail.sous_total / cmd.total) * 100) : 0;
            produitsRows += `
                <tr class="fact-row-anim" style="animation-delay:${0.05 + i * 0.04}s; border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 14px;">
                        <div style="font-weight:600; font-size:13px; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;" title="${detail.produit_nom || 'Produit'}">
                            ${detail.produit_nom || 'Produit'}
                        </div>
                        <div class="fact-progress-bar" style="max-width:100px;">
                            <div class="fact-progress-fill" style="width:${pct}%"></div>
                        </div>
                    </td>
                    <td style="padding:10px 14px; text-align:center;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; min-width:28px; height:22px; background:#f1f5f9; border-radius:6px; font-size:12px; font-weight:600; color:#334155; padding:0 7px;">
                            ${detail.quantite}
                        </span>
                    </td>
                    <td style="padding:10px 14px; text-align:right; font-family:'JetBrains Mono', monospace; font-size:12px; color:#64748b;">
                        ${formatNumber(detail.montant)}
                    </td>
                    <td style="padding:10px 14px; text-align:right; font-family:'JetBrains Mono', monospace; font-size:13px; font-weight:600; color:#10b981; white-space:nowrap;">
                        ${formatNumber(detail.sous_total)} <span style="font-size:10px; font-weight:400; color:#94a3b8;">FCFA</span>
                    </td>
                </tr>
            `;
        });
    } else {
        produitsRows = `
            <tr>
                <td colspan="4" style="padding:32px; text-align:center; color:#94a3b8; font-size:13px;">
                    <i class="fas fa-box-open" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.5;"></i>
                    Aucun produit dans cette facture
                </td>
            </tr>
        `;
    }

    // Overlay
    const overlay = document.createElement('div');
    overlay.id = 'factureDetailOverlay';
    overlay.style.cssText = `
        position:fixed; inset:0; z-index:9999;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(6px);
        display:flex; align-items:center; justify-content:center;
        padding: 16px;
    `;

    overlay.innerHTML = `
        <div id="factureDetailPanel" style="
            background:#f8fafc;
            border-radius: 20px;
            width: 100%;
            max-width: 620px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 32px 64px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.08);
        ">

            <!-- HEADER vert (facture = payée) -->
            <div style="background: linear-gradient(135deg, #059669 0%, #0d9488 100%); padding:20px 22px 18px; flex-shrink:0; position:relative; overflow:hidden;">
                <!-- Cercles déco -->
                <div style="position:absolute; top:-30px; right:-30px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,0.07); pointer-events:none;"></div>
                <div style="position:absolute; bottom:-40px; left:20px; width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none;"></div>

                <div style="position:relative; display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                    <div style="flex:1; min-width:0;">
                        <!-- Entête société + badge FACTURE -->
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <div style="width:30px; height:30px; background:rgba(255,255,255,0.2); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-building" style="color:#fff; font-size:13px;"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px; font-weight:700; color:#fff; letter-spacing:0.01em;">GESTION COMMERCIAL</div>
                                    <div style="font-size:10px; color:rgba(255,255,255,0.6);">contact@gestion.com · +221 77 000 00 00</div>
                                </div>
                            </div>
                            <span style="margin-left:auto; font-size:10px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; background:rgba(255,255,255,0.18); color:#fff; padding:3px 10px; border-radius:20px;">
                                Facture
                            </span>
                        </div>

                        <!-- N° facture -->
                        <div style="font-family:'JetBrains Mono', monospace; font-size:22px; font-weight:500; color:#fff; letter-spacing:0.03em;">
                            ${fact.numero_facture}
                        </div>
                        <div style="margin-top:5px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span style="font-size:11px; color:rgba(255,255,255,0.65);">
                                <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>Émise le ${formatDate(fact.date_facture)}
                            </span>
                            <span style="font-size:11px; color:rgba(255,255,255,0.65);">
                                <i class="fas fa-link" style="margin-right:4px;"></i>CMD-${String(cmd.id).padStart(3, '0')}
                            </span>
                            <span style="font-size:11px; padding:2px 9px; border-radius:20px; background:rgba(255,255,255,0.2); color:#fff; font-weight:600;">
                                ✓ Payée
                            </span>
                        </div>
                    </div>
                    <button class="fact-close-btn close-facture-btn">
                        <i class="fas fa-times" style="font-size:13px;"></i>
                    </button>
                </div>

                <!-- Montant total -->
                <div style="margin-top:14px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.15); display:flex; align-items:baseline; gap:6px;">
                    <span style="font-size:11px; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.06em;">Montant total</span>
                    <span style="font-size:28px; font-weight:700; color:#fff; line-height:1;">${formatNumber(cmd.total)}</span>
                    <span style="font-size:12px; color:rgba(255,255,255,0.6); font-weight:500;">FCFA</span>
                </div>
            </div>

            <!-- BODY SCROLLABLE -->
            <div style="overflow-y:auto; flex:1; padding:18px;">

                <!-- Stat cards -->
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:18px;">
                    <div class="fact-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Facturé à</div>
                        <div style="font-size:13px; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${cmd.client?.nom || 'N/A'}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">${cmd.client?.telephone || '—'}</div>
                    </div>
                    <div class="fact-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Articles</div>
                        <div style="font-size:22px; font-weight:700; color:#1e293b; line-height:1.1;">${totalProduits}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">${fact.details?.length || 0} référence(s)</div>
                    </div>
                    <div class="fact-stat-card">
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Date cmd</div>
                        <div style="font-size:12px; font-weight:600; color:#1e293b; line-height:1.3;">${formatDate(cmd.date_commande)}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Réf: CMD-${String(cmd.id).padStart(3,'0')}</div>
                    </div>
                </div>

                <!-- Adresse client -->
                ${cmd.client?.adresse ? `
                <div style="background:#fff; border:1px solid #e8eaf0; border-radius:12px; padding:12px 14px; margin-bottom:18px; display:flex; align-items:flex-start; gap:10px;">
                    <div style="width:32px; height:32px; flex-shrink:0; background:#ecfdf5; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-map-marker-alt" style="color:#10b981; font-size:13px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:2px;">Adresse du client</div>
                        <div style="font-size:13px; color:#334155;">${cmd.client.adresse}</div>
                    </div>
                </div>
                ` : ''}

                <!-- Tableau produits -->
                <div style="background:#fff; border:1px solid #e8eaf0; border-radius:14px; overflow:hidden; margin-bottom:16px;">
                    <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:600; color:#1e293b; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-boxes" style="color:#10b981; font-size:11px;"></i>
                            Lignes de facturation
                        </span>
                        <span style="font-size:11px; background:#f1f5f9; color:#64748b; padding:2px 10px; border-radius:20px; font-weight:500;">
                            ${fact.details?.length || 0} article(s)
                        </span>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="fact-table" style="width:100%; border-collapse:collapse; min-width:380px;">
                            <thead>
                                <tr style="background:#fafbfc;">
                                    <th style="padding:8px 14px; text-align:left; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Désignation</th>
                                    <th style="padding:8px 14px; text-align:center; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Qté</th>
                                    <th style="padding:8px 14px; text-align:right; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Prix unit.</th>
                                    <th style="padding:8px 14px; text-align:right; font-size:10px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.08em;">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${produitsRows}
                            </tbody>
                        </table>
                    </div>

                    <!-- Récap totaux -->
                    <div style="padding:14px; background:#f8fafc; border-top:2px solid #e8eaf0;">
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px; max-width:260px; margin-left:auto;">
                            <div style="display:flex; justify-content:space-between; width:100%; font-size:13px;">
                                <span style="color:#64748b;">Sous-total HT</span>
                                <span style="font-family:'JetBrains Mono', monospace; font-weight:500; color:#1e293b;">${formatNumber(cmd.total)} FCFA</span>
                            </div>
                            <div style="width:100%; height:1px; background:#e2e8f0;"></div>
                            <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                                <span style="font-size:13px; font-weight:700; color:#1e293b;">Total TTC</span>
                                <div style="text-align:right;">
                                    <span style="font-family:'JetBrains Mono', monospace; font-size:20px; font-weight:700; color:#059669;">${formatNumber(cmd.total)}</span>
                                    <span style="font-size:11px; color:#94a3b8; margin-left:3px;">FCFA</span>
                                </div>
                            </div>
                            <!-- Tampon payé -->
                            <div style="margin-top:4px; border:2px solid #10b981; border-radius:8px; padding:4px 16px; color:#059669; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; opacity:0.85;">
                                ✓ Payée
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="fact-no-print" style="padding:14px 18px; border-top:1px solid #e8eaf0; background:#fff; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;">
                <button class="close-facture-btn" style="
                    padding:9px 20px;
                    font-family:'Plus Jakarta Sans', sans-serif;
                    font-size:13px; font-weight:600;
                    color:#64748b; background:#f1f5f9;
                    border:none; border-radius:10px;
                    cursor:pointer; transition:background 0.2s;
                " onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    Fermer
                </button>
                <button onclick="window.print()" style="
                    padding:9px 20px;
                    font-family:'Plus Jakarta Sans', sans-serif;
                    font-size:13px; font-weight:600;
                    color:#fff;
                    background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
                    border:none; border-radius:10px;
                    cursor:pointer;
                    box-shadow: 0 4px 14px rgba(5,150,105,0.35);
                    transition:opacity 0.2s, transform 0.2s;
                    display:flex; align-items:center; gap:6px;
                " onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'">
                    <i class="fas fa-print" style="font-size:12px;"></i>
                    Imprimer
                </button>
            </div>

        </div>
    `;

    document.body.appendChild(overlay);

    // Fermeture
    const closeModal = () => {
        overlay.style.animation = 'factOverlayIn 0.2s ease reverse both';
        document.body.style.overflow = '';
        setTimeout(() => overlay.remove(), 200);
    };

    overlay.querySelectorAll('.close-facture-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    const onKey = (e) => {
        if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', onKey); }
    };
    document.addEventListener('keydown', onKey);
}

function closeFactureModal() {
    const modal = document.querySelector(".factureModal");
    if (modal) {
        modal.remove();
    }
}

async function generatePDF(id_commande) {
    try {
        const response = await fetch("../php/post_generate_pdf.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id_commande: id_commande })
        });
        
        if (response.ok) {
            // Le PDF devrait être téléchargé automatiquement
            // Mais comme c'est un téléchargement, on ne peut pas vérifier le contenu JSON
            console.log("PDF generation request sent");
        } else {
            const errorData = await response.json();
            alert("Erreur lors de la génération du PDF: " + (errorData.error || "Erreur inconnue"));
        }
    } catch (error) {
        console.error("Error generating PDF:", error);
        alert("Erreur lors de la génération du PDF");
    }
}
document.addEventListener("DOMContentLoaded",() =>{
    totalCommandeVendeur();
})
async function totalCommandeVendeur(){
    try {
        const response = await fetch("../php/post_commande_vend.php");
        const data = await response.text();
    } catch (error) {
        console.log("Erreur:" + error)
    }
}
