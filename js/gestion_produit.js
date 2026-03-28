//modal add categorie
const modalAddCat = document.getElementById("modalAddCat");
const cancelAddCategorie = document.getElementById("cancelAddCategorie");
const addCategorie = document.getElementById("addCategorie");

const modalAddProduit = document.getElementById("modalAddProduit");
const addProduit = document.getElementById("btnAddProduit");

const modalAddStock = document.getElementById("modalAddStock");
const addStock = document.getElementById("addStock");
const cancelAddStock = document.getElementById("cancelAddStock");

const addCategorieForm = document.getElementById("addCategorieForm");
let formAddProduit = null;
let approRowCounter = 0;
const approProduitCache = new Map();
const fournisseurCache = new Map();
let defaultApprovisionnementProduits = [];
let defaultApprovisionnementFournisseurs = [];

const card = document.getElementById("card");
const paginationProduit = document.getElementById("paginationProduit");
const produitCountInfo = document.getElementById("produitCountInfo");
const searchInputProduit = document.getElementById("search");
const filterCategorieProduit = document.getElementById("filterCategorie");
let currentProduitPage = 1;
let currentProduitSearch = "";
let currentProduitCategorie = "";
let searchProduitTimer = null;
const PRODUITS_PAR_PAGE = 8;

//edit produit
const modalEditProduit = document.getElementById("modalEditProduit");
const formEditProduit = document.getElementById("formEditProduit");
const cancelEditProduit = document.getElementById("cancelEditProduit");
if (modalAddCat && cancelAddCategorie && addCategorie) {
    addCategorie.addEventListener("click", () => {
        modalAddCat.classList.remove("hide");
    });
    cancelAddCategorie.addEventListener("click", () => {
        modalAddCat.classList.add("hide");
    });
}

function renderProduitModal(categories = []) {
    const categoryOptions = categories
        .map((cat) => `<option value="${cat.id}">${cat.nom}</option>`)
        .join("");

    modalAddProduit.innerHTML = `
        <form class="bg-white w-[520px] rounded-xl shadow-xl p-6" id="formAddProduit">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-semibold text-gray-800">Ajouter un produit</h2>
            </div>

            <div class="flex flex-col items-center mb-6">
                <label for="imageInput" class="cursor-pointer flex flex-col items-center justify-center w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition">
                    <span class="text-gray-400 text-sm">Ajouter image</span>
                    <img id="previewImage" class="w-full h-full object-cover rounded-lg hidden">
                </label>
                <input type="file" id="imageInput" name="profile_picture" accept="image/*" class="hidden">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Nom du produit</label>
                    <input type="text" placeholder="Ex: Coca Cola" name="nom" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Categorie</label>
                    <select name="categorie" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">Choisir categorie</option>
                        ${categoryOptions}
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Prix d'achat</label>
                    <input type="number" name="prix_achat" placeholder="0" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Prix de vente</label>
                    <input type="number" name="prix_vente" placeholder="0" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div class="col-span-2">
                    <label class="text-sm text-gray-600">Quantite en stock</label>
                    <input type="number" name="quantite" placeholder="0" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="cancelAddProduit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Ajouter produit</button>
            </div>
            <div class="msg-produit text-sm font-bold mt-2"></div>
        </form>
    `;

    formAddProduit = document.getElementById("formAddProduit");
    const cancelAddProduit = document.getElementById("cancelAddProduit");
    const imageInput = document.getElementById("imageInput");
    const previewImage = document.getElementById("previewImage");

    if (cancelAddProduit) {
        cancelAddProduit.addEventListener("click", () => {
            modalAddProduit.classList.add("hidden");
            modalAddProduit.classList.remove("flex");
        });
    }

    if (imageInput && previewImage) {
        imageInput.addEventListener("change", function () {
            const file = this.files?.[0];
            if (file) {
                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove("hidden");
            }
        });
    }

    if (formAddProduit) {
        formAddProduit.addEventListener("submit", (e) => {
            e.preventDefault();
            createProduit(formAddProduit);
        });
    }
}

// Modal Add Produit
if (modalAddProduit && addProduit) {
    const btnAddProduit = document.getElementById("btnAddProduit");
    if (btnAddProduit) {
        btnAddProduit.addEventListener("click", async () => {
            const categories = await getCategorie();
            renderProduitModal(categories);
            modalAddProduit.classList.add("flex");
            modalAddProduit.classList.remove("hidden");
        });
    }
}

//modal add stock
if (modalAddStock && addStock && cancelAddStock) {
    addStock.addEventListener("click", async () => {
        try {
            defaultApprovisionnementFournisseurs = await searchApprovisionnementFournisseurs("");
            defaultApprovisionnementProduits = await searchApprovisionnementProduits("");
        } catch (error) {
            console.log("Erreur préparation approvisionnement:", error);
        }

        resetApprovisionnementForm();
        updateFournisseurDatalist(defaultApprovisionnementFournisseurs);
        modalAddStock.classList.add("flex");
        modalAddStock.classList.remove("hidden");
    });
    cancelAddStock.addEventListener("click", () => {
        resetApprovisionnementForm();
        modalAddStock.classList.add("hidden");
        modalAddStock.classList.remove("flex");
    });
}

function renderApprovisionnementProducts() {
    const tbody = document.getElementById("produitBody");
    if (!tbody) return;

    tbody.innerHTML = "";
    addApprovisionnementRow();
}

async function searchApprovisionnementProduits(term = "") {
    const params = new URLSearchParams({
        page: "1",
        limit: "20",
        search: term
    });

    const response = await fetch(`../php/post_read_produit.php?${params.toString()}`);
    const data = await response.json();
    return data.produits || [];
}

async function searchApprovisionnementFournisseurs(term = "") {
    const params = new URLSearchParams({
        page: "1",
        limit: "20",
        search: term
    });

    const response = await fetch(`../php/post_read_fournisseur.php?${params.toString()}`);
    const data = await response.json();
    return data.fournisseurs || [];
}

function updateFournisseurDatalist(fournisseurs = []) {
    const datalist = document.querySelector(".fournisseur-datalist");
    if (!datalist) return;

    datalist.innerHTML = "";
    fournisseurs.forEach((fournisseur) => {
        fournisseurCache.set(String(fournisseur.id), fournisseur);
        const option = document.createElement("option");
        option.value = `${fournisseur.nom} - ${fournisseur.telephone || "Sans telephone"}`;
        option.label = fournisseur.adresse || "";
        option.dataset.id = fournisseur.id;
        option.dataset.nom = fournisseur.nom;
        option.dataset.telephone = fournisseur.telephone || "";
        option.dataset.adresse = fournisseur.adresse || "";
        datalist.appendChild(option);
    });
}

function getSelectedFournisseur() {
    const input = document.querySelector(".fournisseur-search");
    const datalist = document.querySelector(".fournisseur-datalist");
    const hiddenInput = document.querySelector(".fournisseur-id-input");
    if (!input || !datalist || !hiddenInput) return null;

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

    if (hiddenInput.value && fournisseurCache.has(hiddenInput.value)) {
        const cached = fournisseurCache.get(hiddenInput.value);
        const cachedLabel = `${cached.nom} - ${cached.telephone || "Sans telephone"}`.trim().toLowerCase();
        if (cachedLabel === input.value.trim().toLowerCase()) {
            return cached;
        }
    }

    return null;
}

function setSelectedFournisseur(fournisseur) {
    const input = document.querySelector(".fournisseur-search");
    const hiddenInput = document.querySelector(".fournisseur-id-input");
    const meta = document.querySelector(".fournisseur-meta");
    if (!input || !hiddenInput || !meta) return;

    if (!fournisseur) {
        hiddenInput.value = "";
        meta.textContent = "Aucun fournisseur valide selectionne";
        return;
    }

    fournisseurCache.set(String(fournisseur.id), fournisseur);
    input.value = `${fournisseur.nom} - ${fournisseur.telephone || "Sans telephone"}`;
    hiddenInput.value = fournisseur.id;
    meta.textContent = fournisseur.adresse
        ? `Fournisseur: ${fournisseur.nom} | ${fournisseur.adresse}`
        : `Fournisseur: ${fournisseur.nom}`;
}

async function handleFournisseurSearchInput(input) {
    const hiddenInput = document.querySelector(".fournisseur-id-input");
    const meta = document.querySelector(".fournisseur-meta");
    const matched = getSelectedFournisseur();
    if (matched) {
        setSelectedFournisseur(matched);
        return;
    }

    if (hiddenInput) {
        hiddenInput.value = "";
    }

    const term = input.value.trim();
    if (term.length < 2) {
        updateFournisseurDatalist(defaultApprovisionnementFournisseurs);
        if (meta) meta.textContent = "Saisissez au moins 2 caracteres";
        return;
    }

    if (meta) meta.textContent = "Recherche en cours...";

    clearTimeout(input._fournisseurSearchTimer);
    input._fournisseurSearchTimer = setTimeout(async () => {
        try {
            const fournisseurs = await searchApprovisionnementFournisseurs(term);
            updateFournisseurDatalist(fournisseurs);
            if (meta) {
                meta.textContent = fournisseurs.length
                    ? `${fournisseurs.length} suggestion(s) disponibles`
                    : "Aucun fournisseur correspondant";
            }
        } catch (error) {
            console.log("Erreur chargement fournisseurs:", error);
            if (meta) meta.textContent = "Erreur lors de la recherche";
        }
    }, 250);
}

function applyFournisseurSelection() {
    setSelectedFournisseur(getSelectedFournisseur());
}

function updateApprovisionnementProduitDatalist(row, produits = []) {
    const datalist = row.querySelector(".appro-produit-datalist");
    if (!datalist) return;

    datalist.innerHTML = "";
    produits.forEach((produit) => {
        approProduitCache.set(String(produit.id), produit);
        const option = document.createElement("option");
        option.value = produit.nom;
        option.label = `${produit.code_barre || "Sans code"} | ${Number(produit.prix_achat || 0)} FCFA`;
        option.dataset.id = produit.id;
        option.dataset.nom = produit.nom;
        option.dataset.prix = produit.prix_achat;
        option.dataset.codeBarre = produit.code_barre || "";
        datalist.appendChild(option);
    });
}

function getSelectedApprovisionnementProduit(row) {
    const input = row.querySelector(".appro-produit-search");
    const datalist = row.querySelector(".appro-produit-datalist");
    const hiddenInput = row.querySelector(".appro-produit-id");
    if (!input || !datalist || !hiddenInput) return null;

    const selectedOption = Array.from(datalist.options).find(
        (option) => option.value.trim().toLowerCase() === input.value.trim().toLowerCase()
    );

    if (selectedOption) {
        return {
            id: selectedOption.dataset.id,
            nom: selectedOption.dataset.nom || selectedOption.value,
            prix_achat: Number(selectedOption.dataset.prix) || 0,
            code_barre: selectedOption.dataset.codeBarre || ""
        };
    }

    if (hiddenInput.value && approProduitCache.has(hiddenInput.value)) {
        const cached = approProduitCache.get(hiddenInput.value);
        if ((cached.nom || "").trim().toLowerCase() === input.value.trim().toLowerCase()) {
            return cached;
        }
    }

    return null;
}

function setApprovisionnementProduit(row, produit) {
    const input = row.querySelector(".appro-produit-search");
    const hiddenInput = row.querySelector(".appro-produit-id");
    const prixInput = row.querySelector('.prix-input');
    const meta = row.querySelector('.appro-produit-meta');

    if (!input || !hiddenInput || !prixInput || !meta) {
        return;
    }

    if (!produit) {
        hiddenInput.value = "";
        prixInput.value = 0;
        meta.textContent = "Aucun produit valide selectionne";
        return;
    }

    approProduitCache.set(String(produit.id), produit);
    input.value = produit.nom || "";
    hiddenInput.value = produit.id;
    prixInput.value = Number(produit.prix_achat) || 0;
    meta.textContent = `Produit: ${produit.nom} | Prix achat: ${Number(produit.prix_achat || 0)} FCFA`;
}

async function handleApprovisionnementProductInput(input) {
    const row = input.closest('tr');
    if (!row) return;

    const hiddenInput = row.querySelector('.appro-produit-id');
    const meta = row.querySelector('.appro-produit-meta');
    const prixInput = row.querySelector('.prix-input');
    const matched = getSelectedApprovisionnementProduit(row);
    if (matched) {
        setApprovisionnementProduit(row, matched);
        return;
    }

    if (hiddenInput) hiddenInput.value = "";
    if (prixInput) prixInput.value = 0;

    const term = input.value.trim();
    if (term.length < 2) {
        updateApprovisionnementProduitDatalist(row, defaultApprovisionnementProduits);
        if (meta) meta.textContent = "Saisissez au moins 2 caracteres";
        return;
    }

    if (meta) meta.textContent = "Recherche en cours...";

    clearTimeout(input._approProduitTimer);
    input._approProduitTimer = setTimeout(async () => {
        try {
            const produits = await searchApprovisionnementProduits(term);
            updateApprovisionnementProduitDatalist(row, produits);
            if (meta) {
                meta.textContent = produits.length
                    ? `${produits.length} suggestion(s) disponibles`
                    : "Aucun produit correspondant";
            }
        } catch (error) {
            console.log("Erreur chargement produits:", error);
            if (meta) meta.textContent = "Erreur lors de la recherche";
        }
    }, 250);
}

function addApprovisionnementRow() {
    const tbody = document.getElementById("produitBody");
    if (!tbody) return;

    const datalistId = `appro-produit-options-${++approRowCounter}`;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="p-2">
            <div class="space-y-1">
                <input type="text" class="appro-produit-search w-full border rounded px-2 py-1 text-sm" placeholder="Rechercher un produit..." autocomplete="off" list="${datalistId}">
                <input type="hidden" class="appro-produit-id">
                <datalist id="${datalistId}" class="appro-produit-datalist"></datalist>
                <div class="text-[11px] text-gray-500 appro-produit-meta">Saisissez au moins 2 caracteres</div>
            </div>
        </td>
        <td class="p-2">
            <input type="number" min="1" value="1" name="quantite[]" class="quantite-input w-20 border rounded px-2 py-1 text-sm">
        </td>
        <td class="p-2">
            <input type="number" min="0" value="0" name="prix_achat[]" class="prix-input w-20 border rounded px-2 py-1 text-sm">
        </td>
        <td class="p-2">
            <button type="button" class="text-red-500 hover:text-red-700 remove-row">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    updateApprovisionnementProduitDatalist(tr, defaultApprovisionnementProduits);

    tr.querySelector('.remove-row').addEventListener('click', () => {
        const rows = tbody.querySelectorAll('tr');
        if (rows.length === 1) {
            tr.querySelector('.appro-produit-search').value = "";
            tr.querySelector('.appro-produit-id').value = "";
            tr.querySelector('.quantite-input').value = 1;
            tr.querySelector('.prix-input').value = 0;
            tr.querySelector('.appro-produit-meta').textContent = "Saisissez au moins 2 caracteres";
            return;
        }

        tr.remove();
    });

    tr.querySelector('.appro-produit-search').addEventListener('input', (e) => {
        handleApprovisionnementProductInput(e.target);
    });

    tr.querySelector('.appro-produit-search').addEventListener('focus', () => {
        const datalist = tr.querySelector('.appro-produit-datalist');
        if (datalist && !datalist.options.length) {
            updateApprovisionnementProduitDatalist(tr, defaultApprovisionnementProduits);
        }
    });

    tr.querySelector('.appro-produit-search').addEventListener('change', () => {
        setApprovisionnementProduit(tr, getSelectedApprovisionnementProduit(tr));
    });

    tr.querySelector('.appro-produit-search').addEventListener('blur', () => {
        setApprovisionnementProduit(tr, getSelectedApprovisionnementProduit(tr));
    });
}

function resetApprovisionnementForm() {
    if (formAppro) {
        formAppro.reset();
    }

    const fournisseurMeta = document.querySelector('.fournisseur-meta');
    if (fournisseurMeta) {
        fournisseurMeta.textContent = "Saisissez au moins 2 caracteres";
    }

    const fournisseurHidden = document.querySelector('.fournisseur-id-input');
    if (fournisseurHidden) {
        fournisseurHidden.value = "";
    }

    updateFournisseurDatalist([]);

    renderApprovisionnementProducts();
}

// Add new product row to approvisionnement
const btnAddRow = document.getElementById('btnAddRow');
if (btnAddRow) {
    btnAddRow.addEventListener('click', () => {
        addApprovisionnementRow();
    });
}

// Handle approvisionnement form submission
const formAppro = document.getElementById("formAppro");
if (formAppro) {
    const fournisseurInput = document.querySelector('.fournisseur-search');
    if (fournisseurInput) {
        fournisseurInput.addEventListener('input', (e) => {
            handleFournisseurSearchInput(e.target);
        });
        fournisseurInput.addEventListener('focus', () => {
            const datalist = document.querySelector('.fournisseur-datalist');
            if (datalist && !datalist.options.length) {
                updateFournisseurDatalist(defaultApprovisionnementFournisseurs);
            }
        });
        fournisseurInput.addEventListener('change', () => {
            applyFournisseurSelection();
        });
        fournisseurInput.addEventListener('blur', () => {
            applyFournisseurSelection();
        });
    }

    formAppro.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        const fournisseur = document.querySelector('.fournisseur-id-input')?.value;
        if (!fournisseur) {
            alert("Veuillez sélectionner un fournisseur");
            return;
        }

        // Collect products data
        const produits = [];
        const tbody = document.getElementById("produitBody");
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const produitIdInput = row.querySelector('.appro-produit-id');
            const quantiteInput = row.querySelector('.quantite-input');
            const prixInput = row.querySelector('.prix-input');
            
            const produitId = produitIdInput?.value;
            const quantite = parseInt(quantiteInput?.value || 0);
            const prixAchat = parseFloat(prixInput?.value || 0);
            
            if (produitId && quantite > 0) {
                produits.push({
                    id: produitId,
                    quantite: quantite,
                    prix_achat: prixAchat
                });
            }
        });

        if (produits.length === 0) {
            alert("Veuillez ajouter au moins un produit");
            return;
        }

        try {
            const formData = new FormData();
            formData.append('fournisseur', fournisseur);
            formData.append('produits', JSON.stringify(produits));

            const response = await fetch("../php/post_add_approvisionnement.php", {
                method: "POST",
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert("Approvisionnement ajouté avec succès!");
                resetApprovisionnementForm();
                modalAddStock.classList.add("hidden");
                modalAddStock.classList.remove("flex");
                // Reload products and stats
                loadProduit(currentProduitPage, currentProduitSearch, currentProduitCategorie);
                getTotalProduit();
                stockFaible();
                totalCategorie();
                sommeProduit();
                // Reload approvisionnements
                loadApprovisionnements();
            } else {
                alert(data.message || "Erreur lors de l'approvisionnement");
            }
        } catch (error) {
            console.log("Erreur:", error);
            alert("Erreur lors de l'approvisionnement");
        }
    });
}

if (addCategorieForm) {
    addCategorieForm.addEventListener("submit", (e) => {
        e.preventDefault();
        createCategorie();
    });
}

async function createCategorie() {
    try {
        const formData = new FormData(addCategorieForm);
        const response = await fetch("../php/post_add_cat.php", {
            method: "POST",
            body: formData
        });
        const data = await response.json();
        const msg = document.querySelector(".msg-create-cat");
        if (data.success) {
            msg.textContent = "Categorie cree avec succes";
            msg.classList.add("text-green-500");
            msg.classList.remove("text-red-500");
            addCategorieForm.reset();
        } else {
            msg.textContent = data.message || "Erreur, categorie non cree";

            msg.classList.add("text-red-500");
            msg.classList.remove("text-green-500");
        }
        setTimeout(() => {
            msg.textContent = "";
        }, 4000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}

async function getCategorie() {
    try {
        const response = await fetch("../php/post_read_cat.php");
        const data = await response.json();
        return data.categorie || [];
    } catch (error) {
        console.log("Erreur:" + error);
        return [];
    }
}

async function createProduit(form) {
    try {
        const formData = new FormData(form);
        const response = await fetch("../php/post_add_produit.php", {
            method: "POST",
            body: formData
        });
        const data = await response.json();
        const msgProduit = document.querySelector(".msg-produit");
        if (data.success) {
            msgProduit.textContent = "Produit ajoute avec succes";
            msgProduit.classList.add("text-green-500");
            msgProduit.classList.remove("text-red-500");
            form.reset();
            loadProduit(currentProduitPage, currentProduitSearch, currentProduitCategorie);
            getTotalProduit()
            stockFaible()
            totalCategorie()
            sommeProduit()
        } else {
            msgProduit.textContent = data.message || "Erreur, produit non ajoute";
            msgProduit.classList.add("text-red-500");
            msgProduit.classList.remove("text-green-500");
        }
        setTimeout(() => {
            msgProduit.textContent = "";
        }, 4000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}

function buildProfileSrc(profile) {
    const value = (profile || "").trim();
    if (!value) return "../uploads/profiles/default.png";
    if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/") || value.startsWith("../")) {
        return value;
    }
    if (value.startsWith("uploads/")) {
        return `../${value}`;
    }
    return `../uploads/profiles/${value}`;
}

function ensureProduitCardHoverStyles() {
    if (document.getElementById("produit-card-hover-styles")) return;

    const style = document.createElement("style");
    style.id = "produit-card-hover-styles";
    style.textContent = `
        .produit-card-actions {
            opacity: 0;
            transform: translateY(16px);
            max-height: 0;
            margin-top: 0;
            overflow: hidden;
            pointer-events: none;
            transition: opacity .3s ease, transform .3s ease, max-height .3s ease, margin-top .3s ease;
        }
        .produit-card:hover .produit-card-actions {
            opacity: 1;
            transform: translateY(0);
            max-height: 64px;
            margin-top: 1rem;
            pointer-events: auto;
        }
    `;
    document.head.appendChild(style);
}

function renderProduitPagination(totalPages, currentPage) {
    if (!paginationProduit) return;
    paginationProduit.innerHTML = "";
    if (!totalPages || totalPages < 2) return;

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPage ? "bg-blue-600 text-white" : "bg-gray-200 text-gray-700"}`;
        btn.addEventListener("click", () => loadProduit(i, currentProduitSearch, currentProduitCategorie));
        paginationProduit.appendChild(btn);
    }
}

async function loadProduit(page = 1, search = currentProduitSearch, categorie = currentProduitCategorie){
    if (!card) return;
    ensureProduitCardHoverStyles();
    currentProduitPage = page;
    currentProduitSearch = (search || "").trim();
    currentProduitCategorie = categorie || "";

    try {
        const response = await fetch(`../php/post_read_produit.php?page=${page}&search=${encodeURIComponent(currentProduitSearch)}&categorie=${encodeURIComponent(currentProduitCategorie)}`);
        const datas = await response.json();
        let actionHtml = "";
        if (datas.role === "admin") {
            actionHtml = `
                <div class="produit-card-actions flex gap-2">
                    <button type="button" class="btn-edit flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                        Modifier
                    </button>
                    <button type="button" class="btn-delete flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                        Supprimer
                    </button>
                </div>
            `;
        }
        card.innerHTML = "";

        if (!Array.isArray(datas.produits) || datas.produits.length === 0) {
            card.innerHTML = `<div class="col-span-full text-sm text-slate-500">Aucun produit trouve.</div>`;
            renderProduitPagination(0, 1);
            if (produitCountInfo) {
                produitCountInfo.textContent = "Affichage de 0 produit";
            }
            return;
        }
        
        for (let item of datas.produits) {
            let color , text ;
            let pourcentage = Math.min((item.quantite / item.stock_min) * 100, 100).toFixed(0);
            pourcentage = Math.round(pourcentage)
            if (pourcentage <= 15) {

                color = "bg-red-600";
                text = "text-red-500";
            } 
            else if (pourcentage <= 30) {
                color = "bg-orange-500";
                text = "text-orange-500";
            } 
            else if (pourcentage <= 60) {
                color = "bg-yellow-500";
                text = "text-yellow-600";
            } 
            else {
                color = "bg-green-500";
                text = "text-green-500";
            }
            const profileSrc = buildProfileSrc(item.image);
            const produitCard = document.createElement("div");
            produitCard.className = "produit-card bg-white rounded-2xl shadow-sm hover:shadow-md transition duration-300 overflow-hidden min-h-[320px]";
            produitCard.innerHTML = `

            <!-- Image -->
            <div class="h-40 overflow-hidden">
                <img src="${profileSrc}" 
                     alt="" 
                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
            </div>
    
            <!-- Content -->
            <div class="p-4">
    
                <!-- Nom + Prix -->
                <div class="flex justify-between items-start">
                    <h3 class="text-sm font-semibold text-gray-700 line-clamp-2">
                        ${item.nom}
                    </h3>
                    <span class="text-sm font-bold text-blue-600 whitespace-nowrap">
                        ${item.prix_vente} FCFA
                    </span>
                </div>
    
                <!-- Code -->
                <p class="text-xs text-gray-400 mt-1 truncate">
                    Code: ${item.code_barre}
                </p>
    
                <!-- Stock -->
                <div class="flex justify-between items-center mt-3 text-xs">
                    <span class="text-gray-500">
                        Stock: <span class="font-medium text-gray-700">${item.quantite} unités</span>
                    </span>
                    <span class="msg-pct ${text} font-medium text">
                        ${pourcentage}%
                    </span>
                </div>
    
                <!-- Barre de progression -->
                <div class="w-full bg-gray-500 h-4 rounded-full mt-2 mb-2 ">
                    <div class="${color} h-4 rounded-full py-1 color" style="width:${pourcentage}%"></div>
                </div>

                <!-- Actions -->
                
                ${actionHtml}
        
            </div>
    `;
            card.appendChild(produitCard);

            const btnEdit = produitCard.querySelector(".btn-edit");
            const btnDelete = produitCard.querySelector(".btn-delete");

            if (btnEdit) {
                btnEdit.addEventListener("click", () => {
                    modalEditProduit.classList.remove("hidden");
                    modalEditProduit.classList.add("flex");
                    getDataProduit(item);
                });
            }

            if (btnDelete) {
                btnDelete.addEventListener("click", () => {
                    if(confirm("Voulez-vous vraiment supprimer ce produit?")) {
                        deleteProduit(item.id);
                    }
                });
            }

            if (cancelEditProduit) {
                cancelEditProduit.addEventListener("click", () => {
                    modalEditProduit.classList.add("hidden");
                    modalEditProduit.classList.remove("flex");
                });
            }
        }

        renderProduitPagination(datas.totalPages, datas.currentPage);
        if (produitCountInfo) {
            const total = Number(datas.totalProduits || datas.produits.length);
            const from = total === 0 ? 0 : ((page - 1) * PRODUITS_PAR_PAGE) + 1;
            const to = Math.min(page * PRODUITS_PAR_PAGE, total);
            produitCountInfo.textContent = `Affichage de ${from} a ${to} sur ${total} produits`;
        }
    } catch (error) {
        console.log("Erreur" + error)
    }
}

function getDataProduit(p) {
    const id = document.getElementById("id");
    const imgProduit = document.getElementById("old_profile");
    const categorieSelect = document.getElementById("categorie_edit");
    const prix_achat = document.getElementById("prix_achat_edit");
    const nom = document.getElementById("nom_edit");
    const prix_vente = document.getElementById("prix_vente_edit");
    const quantite = document.getElementById("quantite_edit");
    const code_edit = document.getElementById("code_edit");
    const previewImageEdit = document.getElementById("previewImageEdit");
    const imageInputEdit = document.getElementById("imageInputEdit");

    if (!id || !imgProduit || !categorieSelect || !prix_achat || !prix_vente || !nom || !quantite || !code_edit) {
        console.error("Un ou plusieurs elements du formulaire sont introuvables");
        return;
    }

    // Definir les valeurs
    id.value = p.id;
    imgProduit.value = p.image;
    nom.value = p.nom;
    prix_achat.value = p.prix_achat;
    prix_vente.value = p.prix_vente;
    quantite.value = p.quantite;
    code_edit.value = p.code_barre;
    
    // Afficher l'image actuelle
    if (previewImageEdit && p.image) {
        previewImageEdit.src = buildProfileSrc(p.image);
        previewImageEdit.classList.remove("hidden");
    }

    // Gestion du changement d'image
    if (imageInputEdit && previewImageEdit) {
        // Supprimer l'ancien listener pour eviter les doublons
        imageInputEdit.removeEventListener("change", handleImageEditChange);
        imageInputEdit.addEventListener("change", handleImageEditChange);
    }

    // Charger les categories et selectionner celle du produit
    getCategorie().then(categories => {
        categorieSelect.innerHTML = '<option value="">Choisir categorie</option>';
        categories.forEach(cat => {
            const option = document.createElement("option");
            option.value = cat.id;
            option.textContent = cat.nom;
            if (cat.id == p.id_categorie) {
                option.selected = true;
            }
            categorieSelect.appendChild(option);
        });
    });
}

function handleImageEditChange() {
    const imageInputEdit = document.getElementById("imageInputEdit");
    const previewImageEdit = document.getElementById("previewImageEdit");
    if (imageInputEdit && previewImageEdit) {
        const file = imageInputEdit.files?.[0];
        if (file) {
            previewImageEdit.src = URL.createObjectURL(file);
            previewImageEdit.classList.remove("hidden");
        }
    }
}

async function loadCategorieFilter() {
    if (!filterCategorieProduit) return;
    const categories = await getCategorie();
    filterCategorieProduit.innerHTML = `<option value="">Toutes categories</option>`;
    categories.forEach((cat) => {
        const option = document.createElement("option");
        option.value = String(cat.id);
        option.textContent = cat.nom;
        filterCategorieProduit.appendChild(option);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    if (card) {
        loadProduit();
    }
    loadCategorieFilter();

    if (searchInputProduit && card) {
        searchInputProduit.addEventListener("input", (e) => {
            const value = e.target.value || "";
            clearTimeout(searchProduitTimer);
            searchProduitTimer = setTimeout(() => {
                loadProduit(1, value, currentProduitCategorie);
            }, 300);
        });
    }

    if (filterCategorieProduit && card) {
        filterCategorieProduit.addEventListener("change", (e) => {
            const selected = e.target.value || "";
            loadProduit(1, currentProduitSearch, selected);
        });
    }
    getTotalProduit()
    stockFaible()
    totalCategorie()
    sommeProduit();
    
})
const total_produit = document.querySelector(".total-produit")
async function getTotalProduit(){
    try{
        const response = await fetch("../php/post_getTotal_produit.php");
        const total = await response.json();
        if(total_produit){
            total_produit.textContent = total.totalProduit;
        }
        
    }catch(error){
        console.log("Erreur:" + error)
    }
    
}
const stock_faible = document.querySelector(".stock-faible")
async function stockFaible(){
    try{
        const response = await fetch("../php/post_stockFail.php");
        const total = await response.json();
        if(stock_faible){
            stock_faible.textContent = total.stockFaible;
        }
        
    }catch(error){
        console.log("Erreur:" + error)
    }
    
}
let total_cat = document.querySelector(".categorie")
async function totalCategorie(){
    try{
        const response = await fetch("../php/post_total_cat.php");
        const total = await response.json();
        if(total_cat){
            total_cat.textContent = total.totalCategorie;
        }
        
    }catch(error){
        console.log("Erreur:" + error);
    }
    
}
const somme_po = document.querySelector(".somme-produit")
async function sommeProduit(){
    try{
        const response = await fetch("../php/post_somme_produit.php");
        const total = await response.json();
        if(somme_po){
            somme_po.textContent = total.somme_produit + " F CFA";
        }
        
    }catch(error){
        console.log("Erreur:" + error);
    }
    
}

// Gestion du formulaire d'edition
if(formEditProduit){
    formEditProduit.addEventListener("submit", (e) =>{
        e.preventDefault();
        editProduit()
    })
}

async function editProduit(){
    try {
        const formData = new FormData(formEditProduit);
        const msg_prod = document.querySelector(".msg-produit-edit") || document.querySelector(".msg-produit");
        if(msg_prod){
            msg_prod.textContent = "Modification en cours..."
            msg_prod.classList.remove("text-green-500", "text-red-500");
        }
        const response = await fetch("../php/post_edit_produit.php",{
            method: "POST",
            body: formData
        });
        const data = await response.json();
        if(msg_prod){
            msg_prod.textContent = data.message || "Erreur lors de la modification"
            msg_prod.classList.remove("text-green-500","text-red-500")
            msg_prod.classList.add(data.success ? "text-green-500" : "text-red-500")
        }
        if(data.success){  
            await loadProduit(currentProduitPage,currentProduitSearch,currentProduitCategorie) 
            getTotalProduit()
            stockFaible()
            totalCategorie()
            sommeProduit()
            setTimeout(() =>{
                if(!modalEditProduit) return
                modalEditProduit.classList.remove("flex")
                modalEditProduit.classList.add("hidden")
            },1500)
        }
        setTimeout(() =>{
            if(msg_prod)  msg_prod.textContent = ""
        },3000)
    } catch (error) {
        console.log("Erreur:", error);
    }
}

// Fonction de suppression de produit
async function deleteProduit(id) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch("../php/post_delete_produit.php", {
            method: "POST",
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            // Recharger la liste des produits
            await loadProduit(currentProduitPage, currentProduitSearch, currentProduitCategorie);
            // Mettre à jour les statistiques
            getTotalProduit();
            stockFaible();
            totalCategorie();
            sommeProduit();
            alert(data.message || "Produit supprimé avec succès");
        } else {
            alert(data.message || "Erreur lors de la suppression");
        }
    } catch (error) {
        console.log("Erreur:", error);
        alert("Erreur lors de la suppression du produit");
    }
}

// Charger les approvisionnements récents
let currentApproPage = 1;
let currentApproSearch = '';

async function loadApprovisionnements(page = 1, search = '') {
    const tableBody = document.getElementById("approvisionnementTable");
    const paginationEl = document.getElementById("paginationApprovisionnement");
    if (!tableBody) return;
    
    currentApproPage = page;
    currentApproSearch = search || '';
    
    try {
        const params = new URLSearchParams({
            page: page,
            search_fournisseur: currentApproSearch,
            limit: 10
        });
        const response = await fetch(`../php/post_read_approvisionnement.php?${params}`);
        const data = await response.json();
        
        
        if (data.success) {
            if (data.approvisionnements && data.approvisionnements.length > 0) {
                tableBody.innerHTML = data.approvisionnements.map(app => {
                    const date = new Date(app.date_approvisionnement);
                    const dateStr = date.toLocaleDateString('fr-FR');
                    const etatClass = app.status === 'recu' ? 'bg-green-500' : (app.status === 'annule' ? 'bg-red-500' : 'bg-gray-500');
                    const etatText = app.status === 'recu' ? 'Validé' : (app.status === 'annule' ? 'Annulé' : 'En cours');
                    
                    return `
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition duration-200">
                            <td class="px-6 py-2 dark:text-slate-300">
                                #${app.id}<br>
                                <span class="text-xs text-gray-400 dark:text-slate-500">${dateStr}</span>
                            </td>
                            <td class="px-6 py-2 font-medium text-gray-900 dark:text-slate-200">
                                ${app.produits || 'Aucun'}
                            </td>
                            <td class="px-6 py-2 dark:text-slate-300">
                                ${app.fournisseur_nom || '-'}
                            </td>
                            <td class="px-6 py-2 dark:text-slate-300">
                                ${app.total_quantite || 0}
                            </td>
                            <td class="px-6 py-2">
                                <span class="${etatClass} hover:opacity-80 text-white px-3 py-1 rounded-lg font-semibold transition text-xs inline-block">
                                    ${etatText}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-slate-400">
                            Aucun approvisionnement trouvé
                        </td>
                    </tr>
                `;
            }
            
            // Render pagination
            if (paginationEl && data.total_pages > 1) {
                renderApproPagination(data.total_pages, data.current_page);
            } else if (paginationEl) {
                paginationEl.innerHTML = '';
            }
        }
    } catch (error) {
        console.log("Erreur chargement approvisionnements:", error);
        tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur de chargement</td></tr>';
    }
}

function renderApproPagination(totalPages, currentPage) {
    const paginationEl = document.getElementById("paginationApprovisionnement");
    if (!paginationEl) return;
    
    paginationEl.innerHTML = '';
    
    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.textContent = '‹';
    prevBtn.className = 'px-3 py-1 text-sm font-medium rounded dark:bg-slate-600/50 dark:text-slate-200 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
    prevBtn.disabled = currentPage <= 1;
    prevBtn.onclick = () => loadApprovisionnements(currentPage - 1, currentApproSearch);
    paginationEl.appendChild(prevBtn);
    
    // Page numbers
    const maxVisible = 5;
    const startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    const endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (startPage > 1) {
        const firstBtn = document.createElement('button');
        firstBtn.textContent = '1';
        firstBtn.className = 'px-3 py-1 text-sm font-medium rounded dark:bg-slate-600/50 dark:text-slate-200 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
        firstBtn.onclick = () => loadApprovisionnements(1, currentApproSearch);
        paginationEl.appendChild(firstBtn);
        
        if (startPage > 2) {
            paginationEl.appendChild(document.createTextNode('...'));
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `px-3 py-1 text-sm font-medium rounded ${i === currentPage ? 'bg-blue-600 text-white dark:bg-slate-500' : 'dark:bg-slate-600/50 dark:text-slate-200 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300'}`;
        btn.onclick = () => loadApprovisionnements(i, currentApproSearch);
        paginationEl.appendChild(btn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationEl.appendChild(document.createTextNode('...'));
        }
        
        const lastBtn = document.createElement('button');
        lastBtn.textContent = totalPages;
        lastBtn.className = 'px-3 py-1 text-sm font-medium rounded dark:bg-slate-600/50 dark:text-slate-200 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
        lastBtn.onclick = () => loadApprovisionnements(totalPages, currentApproSearch);
        paginationEl.appendChild(lastBtn);
    }
    
    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.textContent = '›';
    nextBtn.className = 'px-3 py-1 text-sm font-medium rounded dark:bg-slate-600/50 dark:text-slate-200 dark:hover:bg-slate-500 bg-gray-200 hover:bg-gray-300';
    nextBtn.disabled = currentPage >= totalPages;
    nextBtn.onclick = () => loadApprovisionnements(currentPage + 1, currentApproSearch);
    paginationEl.appendChild(nextBtn);
}

// Charger les approvisionnements au chargement de la page
let searchApproTimer;

document.addEventListener("DOMContentLoaded", () => {
    loadApprovisionnements();
    
    const searchInput = document.getElementById("searchApproFournisseur");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const value = e.target.value.trim();
            clearTimeout(searchApproTimer);
            searchApproTimer = setTimeout(() => {
                loadApprovisionnements(1, value);
            }, 300);
        });
    }
});
