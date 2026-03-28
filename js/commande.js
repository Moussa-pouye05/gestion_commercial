// Variables
let currentEditId = null;
let produitRowCounter = 0;
const produitCache = new Map();
const clientCache = new Map();
let defaultCommandeProduits = [];
let defaultCommandeClients = [];

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
    const prix = parseFloat(row.querySelector(".prix-input").value) || 0;
    const quantite = parseInt(row.querySelector(".quantite-input").value) || 0;
    const sousTotal = prix * quantite;
    row.querySelector(".sous-total").textContent = formatNumber(sousTotal) + " FCFA";
}

function updateTotal() {
    const rows = commandeBody.querySelectorAll("tr");
    let total = 0;
    rows.forEach(row => {
        const prix = parseFloat(row.querySelector(".prix-input").value) || 0;
        const quantite = parseInt(row.querySelector(".quantite-input").value) || 0;
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
        tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-gray-500">Aucune commande trouvée</td></tr>';
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
    // BLOQUER LE SCROLL DE LA PAGE
    document.body.style.overflow = 'hidden';
    
    // Calculer le total des produits
    const totalProduits = cmd.details?.reduce((sum, d) => sum + d.quantite, 0) || 0;
    
    // Vérifier si le style existe déjà
    if (!document.getElementById('commande-anim-styles')) {
        const style = document.createElement('style');
        style.id = 'commande-anim-styles';
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
    }
    
    // Créer l'overlay
    const overlay = document.createElement("div");
    overlay.id = "detailsModal";
    overlay.className = "fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 z-50";
    overlay.style.opacity = "0";
    overlay.style.transition = "opacity 0.3s ease";
    
    // Construire les lignes du tableau des produits
    let produitsRows = '';
    if (cmd.details && cmd.details.length > 0) {
        cmd.details.forEach(detail => {
            produitsRows += `
                <tr class="hover:bg-gray-50">
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 max-w-[120px] sm:max-w-none">
                        <span class="font-medium text-gray-800 block truncate" title="${detail.produit_nom || 'Produit'}">${detail.produit_nom || 'Produit'}</span>
                    </td>
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center text-gray-600">${formatNumber(detail.prix)}</td>                
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center">
                        <span class="bg-gray-100 px-1.5 py-0.5 rounded-full text-xs text-gray-700">
                            ${detail.quantite}
                        </span>
                    </td>
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center font-semibold text-blue-600">${formatNumber(detail.sous_total)}</td>
                </tr>
            `;
        });
    } else {
        produitsRows = `
            <tr>
                <td colspan="4" class="px-3 py-4 text-center text-gray-400">
                    <i class="fas fa-box-open text-lg mb-1 block"></i>
                    Aucun produit
                </td>
            </tr>
        `;
    }
    
    // Structure complète du modal
    overlay.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-full sm:max-w-2xl lg:max-w-3xl flex flex-col" style="max-height: 95vh; animation: slideIn 0.3s ease-out;">
            <!-- En-tête avec dégradé - FIXE -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 sm:p-4 rounded-t-xl flex-shrink-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-1 sm:gap-2">
                            <i class="fas fa-receipt text-sm sm:text-base"></i>
                            Détails de la commande
                        </h3>
                        <div class="mt-1">
                            <h1 class="text-sm sm:text-base font-bold text-white">GESTION COMMERCIAL</h1>
                            <p class="text-xs text-blue-100">contact@gestion.com | +221 77 000 00 00</p>
                        </div>
                        <p class="text-blue-100 text-xs mt-1">
                            Réf: <span class="font-mono font-semibold">CMD-${String(cmd.id).padStart(3, '0')}</span>
                        </p>
                    </div>
                    <button class="close-modal-btn text-white/80 hover:text-white transition p-1.5 hover:bg-white/10 rounded-lg flex-shrink-0">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Contenu scrollable -->
            <div class="overflow-y-auto p-3 sm:p-4" style="max-height: calc(95vh - 140px);">
                <!-- Grille d'informations -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 mb-3 sm:mb-4">
                    <!-- Carte client -->
                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 border border-gray-200">
                        <div class="flex items-center gap-1.5 text-blue-600 mb-1 sm:mb-2">
                            <i class="fas fa-user-circle text-xs sm:text-sm"></i>
                            <h4 class="font-medium text-gray-700 text-xs sm:text-sm">Client</h4>
                        </div>
                        <div class="space-y-0.5 sm:space-y-1 text-xs">
                            <p><span class="text-gray-500">Nom:</span> <span class="font-medium text-gray-800">${cmd.client?.nom || 'N/A'}</span></p>
                            <p><span class="text-gray-500">Tél:</span> <span class="font-medium text-gray-800">${cmd.client?.telephone || 'N/A'}</span></p>
                            <p class="truncate"><span class="text-gray-500">Adr:</span> <span class="font-medium text-gray-800">${cmd.client?.adresse || 'Non renseignée'}</span></p>
                        </div>
                    </div>
                    
                    <!-- Carte commande -->
                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 border border-gray-200">
                        <div class="flex items-center gap-1.5 text-indigo-600 mb-1 sm:mb-2">
                            <i class="fas fa-shopping-cart text-xs sm:text-sm"></i>
                            <h4 class="font-medium text-gray-700 text-xs sm:text-sm">Commande</h4>
                        </div>
                        <div class="space-y-0.5 sm:space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date:</span>
                                <span class="font-medium">${formatDate(cmd.date_commande)}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Statut:</span>
                                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-${getEtatClass(cmd.etat)}-100 text-${getEtatClass(cmd.etat)}-600">
                                    ${getEtatLabel(cmd.etat)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Articles:</span>
                                <span class="font-medium">${totalProduits}</span>
                            </div>
                            <div class="flex justify-between pt-1 mt-1 border-t border-gray-200">
                                <span class="text-gray-600 font-medium">Total:</span>
                                <span class="font-bold text-blue-600">${formatNumber(cmd.total)} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Liste des produits -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-2 sm:px-3 py-1.5 sm:py-2 border-b border-gray-200">
                        <h4 class="font-medium text-gray-700 text-xs sm:text-sm flex items-center gap-1.5">
                            <i class="fas fa-boxes text-blue-500 text-xs"></i>
                            Produits
                            <span class="ml-auto text-xs font-normal text-gray-500">${cmd.details?.length || 0} article(s)</span>
                        </h4>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-1 sm:px-3 py-1.5 text-left">Produit</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-center">Prix</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-center">Quantité</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                ${produitsRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- FOOTER BOUTONS - FIXE -->
            <div class="border-t border-gray-200 p-2 sm:p-3 bg-gray-50 rounded-b-xl flex justify-end gap-2 flex-shrink-0">
                
                <button class="close-modal-btn px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-1 shadow">
                    <i class="fas fa-check"></i>
                    Fermer
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Animation d'entrée
    setTimeout(() => overlay.style.opacity = "1", 10);
    
    // Fonction pour fermer le modal
    const closeModal = () => {
        overlay.style.opacity = "0";
        document.body.style.overflow = '';
        setTimeout(() => overlay.remove(), 300);
    };
    
    // Fermeture avec tous les boutons "close"
    overlay.querySelectorAll('.close-modal-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    // Fermeture en cliquant sur l'overlay
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
            closeModal();
        }
    });
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
    
    // BLOQUER LE SCROLL DE LA PAGE
    document.body.style.overflow = 'hidden';
    
    // Calculer le total des produits
    const totalProduits = fact.details?.reduce((sum, d) => sum + d.quantite, 0) || 0;
    
    // Vérifier si le style existe déjà
    if (!document.getElementById('facture-anim-styles')) {
        const style = document.createElement('style');
        style.id = 'facture-anim-styles';
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
    }
    
    // Créer l'overlay
    const overlay = document.createElement("div");
    overlay.id = "factureModal";
    overlay.className = "fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 z-50";
    overlay.style.opacity = "0";
    overlay.style.transition = "opacity 0.3s ease";
    
    // Construire les lignes du tableau des produits
    let produitsRows = '';
    if (fact.details && fact.details.length > 0) {
        fact.details.forEach(detail => {
            produitsRows += `
                <tr class="hover:bg-gray-50">
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 max-w-[120px] sm:max-w-none">
                        <span class="font-medium text-gray-800 block truncate" title="${detail.produit_nom || 'Produit'}">${detail.produit_nom || 'Produit'}</span>
                    </td>
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center">
                        <span class="bg-gray-100 px-1.5 py-0.5 rounded-full text-xs text-gray-700">
                            ${detail.quantite}
                        </span>
                    </td>
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center text-gray-600">${formatNumber(detail.montant)}</td>
                    <td class="px-1 sm:px-3 py-1.5 sm:py-2 text-center font-semibold text-blue-600">${formatNumber(detail.sous_total)}</td>
                </tr>
            `;
        });
    } else {
        produitsRows = `
            <tr>
                <td colspan="4" class="px-3 py-4 text-center text-gray-400">
                    <i class="fas fa-box-open text-lg mb-1 block"></i>
                    Aucun produit
                </td>
            </tr>
        `;
    }
    
    // Structure complète du modal - SANS MODE DE PAIEMENT ET SANS TVA
    overlay.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-full sm:max-w-2xl lg:max-w-3xl flex flex-col" style="max-height: 98vh; animation: slideIn 0.3s ease-out;">
            <!-- En-tête avec dégradé - FIXE -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 sm:p-5 rounded-t-xl flex-shrink-0">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg md:text-xl font-bold text-white flex items-center gap-1 sm:gap-2">
                            <i class="fas fa-file-invoice text-sm sm:text-base"></i>
                            Facture
                        </h3>
                        <div class="mt-2">
                            <h1 class="text-base sm:text-lg md:text-xl font-bold text-white">GESTION COMMERCIAL</h1>
                            <p class="text-xs sm:text-sm text-blue-100 mt-0.5">contact@gestion.com | +221 77 000 00 00</p>
                        </div>
                        <p class="text-blue-100 text-xs sm:text-sm mt-2">
                            N° Facture: <span class="font-mono font-semibold">${fact.numero_facture}</span>
                        </p>
                    </div>
                    <button class="close-facture-btn text-white/80 hover:text-white transition p-1.5 hover:bg-white/10 rounded-lg flex-shrink-0">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Contenu scrollable -->
            <div class="overflow-y-auto p-3 sm:p-4" style="max-height: calc(98vh - 180px);">
                <!-- Grille d'informations -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 mb-3 sm:mb-4">
                    <!-- Carte client -->
                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 border border-gray-200">
                        <div class="flex items-center gap-1.5 text-blue-600 mb-1 sm:mb-2">
                            <i class="fas fa-user-circle text-xs sm:text-sm"></i>
                            <h4 class="font-medium text-gray-700 text-xs sm:text-sm">Facturé à</h4>
                        </div>
                        <div class="space-y-0.5 sm:space-y-1 text-xs">
                            <p><span class="text-gray-500">Nom:</span> <span class="font-medium text-gray-800">${cmd.client?.nom || 'N/A'}</span></p>
                            <p><span class="text-gray-500">Tél:</span> <span class="font-medium text-gray-800">${cmd.client?.telephone || 'N/A'}</span></p>
                            <p class="truncate"><span class="text-gray-500">Adr:</span> <span class="font-medium text-gray-800">${cmd.client?.adresse || 'Non renseignée'}</span></p>
                        </div>
                    </div>
                    
                    <!-- Carte commande -->
                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 border border-gray-200">
                        <div class="flex items-center gap-1.5 text-indigo-600 mb-1 sm:mb-2">
                            <i class="fas fa-shopping-cart text-xs sm:text-sm"></i>
                            <h4 class="font-medium text-gray-700 text-xs sm:text-sm">Commande associée</h4>
                        </div>
                        <div class="space-y-0.5 sm:space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Réf:</span>
                                <span class="font-mono font-medium">CMD-${String(cmd.id).padStart(3, '0')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date cmd:</span>
                                <span class="font-medium">${formatDate(cmd.date_commande)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date fact:</span>
                                <span class="font-medium">${formatDate(fact.date_facture)}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Statut:</span>
                                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                    Payée
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Liste des produits -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-2 sm:px-3 py-1.5 sm:py-2 border-b border-gray-200">
                        <h4 class="font-medium text-gray-700 text-xs sm:text-sm flex items-center gap-1.5">
                            <i class="fas fa-boxes text-blue-500 text-xs"></i>
                            Produits
                            <span class="ml-auto text-xs font-normal text-gray-500">${fact.details?.length || 0} article(s)</span>
                        </h4>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-1 sm:px-3 py-1.5 text-left">Produit</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-center">Quantité</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-right">Prix unitaire</th>
                                    <th class="px-1 sm:px-3 py-1.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                ${produitsRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- TOTAUX SIMPLIFIÉS - JUSTE SOUS-TOTAL ET TOTAL -->
                <div class="mt-3 sm:mt-4">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4">
                        <div class="space-y-2 text-sm sm:text-base">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sous-total:</span>
                                <span class="font-mono font-semibold text-gray-800">${formatNumber(cmd.total)} FCFA</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                <span class="text-gray-700">Total:</span>
                                <span class="font-mono text-blue-600">${formatNumber(cmd.total)} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FOOTER BOUTONS - FIXE -->
            <div class="border-t border-gray-200 p-2 sm:p-3 bg-gray-50 rounded-b-xl flex justify-end gap-2 flex-shrink-0">
                <button onclick="window.print()" 
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition flex items-center gap-1">
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>
                <button class="close-facture-btn px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-1 shadow">
                    <i class="fas fa-check"></i>
                    Fermer
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Animation d'entrée
    setTimeout(() => overlay.style.opacity = "1", 10);
    
    // Fonction pour fermer le modal
    const closeModal = () => {
        overlay.style.opacity = "0";
        document.body.style.overflow = '';
        setTimeout(() => overlay.remove(), 300);
    };
    
    // Fermeture avec tous les boutons "close"
    overlay.querySelectorAll('.close-facture-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    // Fermeture en cliquant sur l'overlay
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
            closeModal();
        }
    });
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
