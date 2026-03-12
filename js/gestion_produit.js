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
        // Load suppliers
        await loadFournisseursForApprovisionnement();
        // Load products
        await loadProductsForApprovisionnement();
        modalAddStock.classList.add("flex");
        modalAddStock.classList.remove("hidden");
    });
    cancelAddStock.addEventListener("click", () => {
        modalAddStock.classList.add("hidden");
        modalAddStock.classList.remove("flex");
    });
}

// Load fournisseurs for approvisionnement modal
async function loadFournisseursForApprovisionnement() {
    try {
        const response = await fetch("../php/post_read_fournisseur.php");
        const data = await response.json();
        const select = document.querySelector('select[name="fournisseur"]');
        if (select && data.fournisseurs) {
            select.innerHTML = '<option value="">Choisir fournisseur</option>';
            data.fournisseurs.forEach(f => {
                const option = document.createElement("option");
                option.value = f.id;
                option.textContent = f.nom;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.log("Erreur chargement fournisseurs:", error);
    }
}

// Load products for approvisionnement modal
let approvisionnementProducts = [];
async function loadProductsForApprovisionnement() {
    try {
        const response = await fetch("../php/post_read_produit.php?page=1&search=&categorie=");
        const data = await response.json();
        if (data.produits) {
            approvisionnementProducts = data.produits;
            renderApprovisionnementProducts();
        }
    } catch (error) {
        console.log("Erreur chargement produits:", error);
    }
}

function renderApprovisionnementProducts() {
    const tbody = document.getElementById("produitBody");
    if (!tbody) return;
    
    tbody.innerHTML = approvisionnementProducts.map((p, index) => `
        <tr>
            <td class="p-2">
                <select class="produit-select w-full border rounded px-2 py-1 text-sm" data-index="${index}">
                    <option value="">Choisir</option>
                    ${approvisionnementProducts.map(prod => `
                        <option value="${prod.id}" data-prix="${prod.prix_achat}">${prod.nom}</option>
                    `).join('')}
                </select>
            </td>
            <td class="p-2">
                <input type="number" min="1" value="1" class="quantite-input w-20 border rounded px-2 py-1 text-sm" data-index="${index}">
            </td>
            <td class="p-2">
                <input type="number" min="0" value="0" class="prix-input w-24 border rounded px-2 py-1 text-sm" data-index="${index}">
            </td>
            <td class="p-2">
                <button type="button" class="text-red-500 hover:text-red-700 remove-row" data-index="${index}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    // Add event listeners for remove buttons
    tbody.querySelectorAll('.remove-row').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const row = e.target.closest('tr');
            row.remove();
        });
    });

    // Add event listeners for product selection to auto-fill prix
    tbody.querySelectorAll('.produit-select').forEach(select => {
        select.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const prixInput = e.target.closest('tr').querySelector('.prix-input');
            if (selectedOption.dataset.prix) {
                prixInput.value = selectedOption.dataset.prix;
            }
        });
    });
}

// Add new product row to approvisionnement
const btnAddRow = document.getElementById('btnAddRow');
if (btnAddRow) {
    btnAddRow.addEventListener('click', () => {
        const tbody = document.getElementById("produitBody");
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="p-2">
                <select class="produit-select w-full border rounded px-2 py-1 text-sm" name="produit[]">
                    <option value="">Choisir</option>
                    ${approvisionnementProducts.map(prod => `
                        <option value="${prod.id}" data-prix="${prod.prix_achat}">${prod.nom}</option>
                    `).join('')}
                </select>
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
        
        // Add event listeners
        tr.querySelector('.remove-row').addEventListener('click', () => tr.remove());
        tr.querySelector('.produit-select').addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const prixInput = tr.querySelector('.prix-input');
            if (selectedOption.dataset.prix) {
                prixInput.value = selectedOption.dataset.prix;
            }
        });
    });
}

// Handle approvisionnement form submission
const formAppro = document.getElementById("formAppro");
if (formAppro) {
    formAppro.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        const fournisseur = document.querySelector('select[name="fournisseur"]')?.value;
        if (!fournisseur) {
            alert("Veuillez sélectionner un fournisseur");
            return;
        }

        // Collect products data
        const produits = [];
        const tbody = document.getElementById("produitBody");
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const produitSelect = row.querySelector('.produit-select');
            const quantiteInput = row.querySelector('.quantite-input');
            const prixInput = row.querySelector('.prix-input');
            
            const produitId = produitSelect?.value;
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
                formAppro.reset();
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
                color = "bg-red-500";
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
                    <span class="msg-pct ${text} font-medium">
                        ${pourcentage}%
                    </span>
                </div>
    
                <!-- Barre de progression -->
                <div class="w-full bg-gray-500 h-4 rounded-full mt-2 mb-2 ">
                    <div class="${color} h-4 rounded-full py-1" style="width:${pourcentage}%"></div>
                </div>

                <!-- Actions -->
                <div class="produit-card-actions flex gap-2">
                    <button type="button" class="btn-edit flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                        Modifier
                    </button>
                    <button type="button" class="btn-delete flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                        Supprimer
                    </button>
                </div>
                
            </div>
    `;
            card.appendChild(produitCard);

            produitCard.querySelector(".btn-edit").addEventListener("click", () => {
                modalEditProduit.classList.remove("hidden");
                modalEditProduit.classList.add("flex");
                getDataProduit(item);
            })
            
            // Supprimer le produit
            produitCard.querySelector(".btn-delete").addEventListener("click", () => {
                if(confirm("Voulez-vous vraiment supprimer ce produit?")) {
                    deleteProduit(item.id);
                }
            })
            
            cancelEditProduit.addEventListener("click", () => {
                modalEditProduit.classList.add("hidden");
                modalEditProduit.classList.remove("flex");
            })
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
    pourcentageStock()
    
})
async function pourcentageStock(){
    try {
        const response = await fetch("../php/post_pourcentage_stock.php");
        const pourcentage = await response.json();
        return pourcentage.pourcentageStock;
    } catch (error) {
        console.log("Erreur:" + error)
    }
}
async function getTotalProduit(){
    try{
        const response = await fetch("../php/post_getTotal_produit.php");
        const total = await response.json();
        document.querySelector(".total-produit").textContent = total.totalProduit;
    }catch(error){
        console.log("Erreur:" + error)
    }
    
}
async function stockFaible(){
    try{
        const response = await fetch("../php/post_stockFail.php");
        const total = await response.json();
        document.querySelector(".stock-faible").textContent = total.stockFaible;
    }catch(error){
        console.log("Erreur:" + error)
    }
    
}
async function totalCategorie(){
    try{
        const response = await fetch("../php/post_total_cat.php");
        const total = await response.json();
        document.querySelector(".categorie").textContent = total.totalCategorie;
    }catch(error){
        console.log("Erreur:" + error);
    }
    
}
async function sommeProduit(){
    try{
        const response = await fetch("../php/post_somme_produit.php");
        const total = await response.json();
        document.querySelector(".somme-produit").textContent = total.somme_produit + " F CFA";
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
async function loadApprovisionnements() {
    const tableBody = document.getElementById("approvisionnementTable");
    if (!tableBody) return;
    
    try {
        const response = await fetch("../php/post_read_approvisionnement.php");
        const data = await response.json();
        
        if (data.success && data.approvisionnements.length > 0) {
            tableBody.innerHTML = data.approvisionnements.map(app => {
                const date = new Date(app.date_approvisionnement);
                const dateStr = date.toLocaleDateString('fr-FR');
                const etatClass = app.status === 'recu' ? 'bg-green-500' : (app.status === 'annule' ? 'bg-red-500' : 'bg-gray-500');
                const etatText = app.status === 'recu' ? 'Validé' : (app.status === 'annule' ? 'Annulé' : 'En cours');
                
                return `
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-2">
                            #${app.id}<br>
                            <span class="text-xs text-gray-400">${dateStr}</span>
                        </td>
                        <td class="px-6 py-2 font-medium text-gray-900">
                            ${app.produits || 'Aucun'}
                        </td>
                        <td class="px-6 py-2">
                            ${app.fournisseur_nom || '-'}
                        </td>
                        <td class="px-6 py-2">
                            ${app.total_quantite || 0}
                        </td>
                        <td class="px-6 py-2">
                            <button class="${etatClass} hover:opacity-80 text-white px-3 py-1 rounded-lg font-semibold transition text-xs">
                                ${etatText}
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Aucun approvisionnement récent
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.log("Erreur chargement approvisionnements:", error);
    }
}

// Charger les approvisionnements au chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    loadApprovisionnements();
});

