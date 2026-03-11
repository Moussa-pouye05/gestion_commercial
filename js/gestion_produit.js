//modal add categorie
const modalAddCat = document.getElementById("modalAddCat");
const cancelAddCategorie = document.getElementById("cancelAddCategorie");
const addCategorie = document.getElementById("addCategorie");

const modalAddProduit = document.getElementById("modalAddProduit");
const addProduit = document.getElementById("addProduit");

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

if (modalAddProduit && addProduit) {
    addProduit.addEventListener("click", async () => {
        const categories = await getCategorie();
        renderProduitModal(categories);
        modalAddProduit.classList.add("flex");
        modalAddProduit.classList.remove("hidden");
    });
}

//modal add stock
if (modalAddStock && addStock && cancelAddStock) {
    addStock.addEventListener("click", () => {
        modalAddStock.classList.add("flex");
        modalAddStock.classList.remove("hidden");
    });
    cancelAddStock.addEventListener("click", () => {
        modalAddStock.classList.add("hidden");
        modalAddStock.classList.remove("flex");
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
                        Stock: <span class="font-medium text-gray-700">${item.quantite} unites</span>
                    </span>
                    <span class="text-green-600 font-medium">
                        50%
                    </span>
                </div>
    
                <!-- Barre de progression -->
                <div class="w-full bg-gray-100 h-[20px] rounded-full mt-2">
                    <div class="bg-green-500 h-[20px] rounded-full" style="width:50%"></div>
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
    sommeProduit()
    
})

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
        console.log(data)
        if(msg_prod){
            msg_prod.textContent = data.message || "Erreur lors de la modification"
            msg_prod.classList.remove("text-green-500","text-red-500")
            msg_prod.classList.add(data.success ? "text-green-500" : "text-red-500")
        }
        if(data.success){  
            await loadProduit(currentProduitPage,currentProduitSearch,currentProduitCategorie) 
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

