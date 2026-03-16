const form = document.getElementById("form_inscription");
const form_client = document.getElementById("form_client");
const formVendeurEdit = document.getElementById("form_vendeur");
const modalEditVendeur = document.getElementById("modalEditVendeur");
const cancelEditVendeur = document.getElementById("cancelEditVendeur");
const pagination = document.getElementById("pagination");
const searchInputUser = document.getElementById("search");
const modale = document.getElementById("deleteModal");
const cancelBtn = document.getElementById("cancelDelete");
const confirmBtn = document.getElementById("confirmDelete");

const isVendeurPage = !!(form_client && formVendeurEdit && pagination && modalEditVendeur);
let currentPage = 1;
let currentSearch = "";
let searchDebounceTimer = null;

if (form) {
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        inscription();
    });
}

if (form_client) {
    form_client.addEventListener("submit", (e) => {
        e.preventDefault();
        inscription();
    });
}

async function inscription() {
    try {
        const activeForm = form_client || form;
        if (!activeForm) return;

        const formData = new FormData(activeForm);
        const succes_connect = document.querySelector(".succes_connect");
        const error_connect = document.querySelector(".error_connect");

        const response = await fetch("../php/post_inscription.php", {
            method: "POST",
            body: formData
        });
        const datas = await response.json();

        if (datas.success) {
            if (succes_connect) succes_connect.textContent = datas.message;
            if (isVendeurPage) {
                await loadVendeur(currentPage);
            }
            activeForm.reset();
        } else if (error_connect) {
            error_connect.textContent = datas.message;
        }

        setTimeout(() => {
            if (error_connect) error_connect.textContent = "";
            if (succes_connect) succes_connect.textContent = "";
        }, 5000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    if (isVendeurPage) {
        loadVendeur(currentPage);
    }

    if (isVendeurPage && searchInputUser) {
        searchInputUser.addEventListener("input", (e) => {
            const value = e.target.value || "";
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                loadVendeur(1, value);
            }, 300);
        });
    }
});

async function loadVendeur(page = 1, search = currentSearch) {
    currentPage = page;
    currentSearch = (search || "").trim();
    try {
        const response = await fetch(`../php/post_readVendeur.php?page=${page}&search=${encodeURIComponent(currentSearch)}`);
        const datas = await response.json();
        const tbody = document.querySelector("tbody");
        const noFound = document.querySelector(".no-found");
        if (!tbody) return;

        tbody.innerHTML = "";
        if (noFound) noFound.textContent = "";

        if (!Array.isArray(datas.vendeurs) || datas.vendeurs.length === 0) {
            if (noFound) noFound.textContent = "Aucun vendeur trouve.";
            generatePagination(0, 1);
            return;
        }
        for (const item of datas.vendeurs) {
            const profileSrc = buildProfileSrc(item.profile);
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="flex items-center mt-2">
                    <span><img src="${profileSrc}" class="w-10 h-10 rounded-full object-cover" alt="photo"/></span>
                    <span class="ml-2">${item.nom}</span>
                </td>
                <td class="px-6 py-2 font-medium text-gray-900">${item.email}</td>
                <td class="px-6 py-2">${item.telephone}</td>
                <td class="px-6 py-2">
                    <div class=" text-green-500 float-right text-sm">50%</div>
                    <div class="w-full bg-gray-100 h-4 rounded-full mt-6">
                        <div class="bg-green-500 h-4 rounded-full" py-1 style="width:50%"></div>
                    </div>
                </td>
                <td class="px-6 py-2 flex gap-2">
                    <button class="edit w-8 h-8 flex items-center justify-center rounded-md bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button class="delete w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);

            tr.querySelector(".edit").addEventListener("click", () => {
                if (!modalEditVendeur) return;
                modalEditVendeur.classList.remove("hidden", "hide");
                modalEditVendeur.classList.add("flex");
                recuperationVendeur(item);
            });

            tr.querySelector(".delete").addEventListener("click", () => {
                deleteVendeur(item.id);
            });
        }

        generatePagination(datas.totalPages, datas.currentPage);
    } catch (error) {
        console.log(error);
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

function generatePagination(totalPages, currentPageValue) {
    const container = document.getElementById("pagination");
    if (!container) return;

    container.innerHTML = "";
    if (!totalPages || totalPages < 0) return;
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPageValue ? "bg-blue-600 text-white" : "bg-gray-200"}`;
        btn.addEventListener("click", () => loadVendeur(i, currentSearch));
        container.appendChild(btn);
    }
}

function recuperationVendeur(v) {
    const id = document.getElementById("id");
    const oldProfile = document.getElementById("old_profile");
    const email = document.getElementById("email");
    const nom = document.getElementById("nom");
    const telephone = document.getElementById("telephone");

    if (!id || !oldProfile || !email || !nom || !telephone) return;

    id.value = v.id;
    oldProfile.value = v.profile;
    email.value = v.email;
    nom.value = v.nom;
    telephone.value = v.telephone;
}

if (formVendeurEdit) {
    formVendeurEdit.addEventListener("submit", (e) => {
        e.preventDefault();
        editVendeur();
    });
}

async function editVendeur() {
    try {
        if (!formVendeurEdit) return;

        const formData = new FormData(formVendeurEdit);
        const explicationEdit = document.querySelector(".succes_edit");

        if (explicationEdit) {
            explicationEdit.textContent = "Modification en cours...";
        }

        const response = await fetch("../php/post_edit_user.php", {
            method: "POST",
            body: formData
        });
        const data = await response.json();

        if (explicationEdit) {
            explicationEdit.textContent = data.message;
            explicationEdit.classList.remove("text-red-500", "text-green-500");
            explicationEdit.classList.add(data.success ? "text-green-500" : "text-red-500");
        }

        if (data.success) {
            await loadVendeur(currentPage, currentSearch);
            setTimeout(() => {
                if (!modalEditVendeur) return;
                modalEditVendeur.classList.add("hidden");
                modalEditVendeur.classList.remove("flex", "hide");
            }, 5000);
        }

        setTimeout(() => {
            if (explicationEdit) explicationEdit.textContent = "";
        }, 5000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}

if (cancelEditVendeur && modalEditVendeur) {
    cancelEditVendeur.addEventListener("click", () => {
        modalEditVendeur.classList.add("hidden");
        modalEditVendeur.classList.remove("flex", "hide");
    });
}

let vendeurToDelete = null;

function deleteVendeur(id) {
    vendeurToDelete = id;
    const modal = document.getElementById("deleteModal");
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

if (modale && cancelBtn && confirmBtn) {
    cancelBtn.addEventListener("click", () => {
        modale.classList.add("hidden");
        modale.classList.remove("flex");
        vendeurToDelete = null;
    });

    confirmBtn.addEventListener("click", async () => {
        if (!vendeurToDelete) return;

        try {
            const formData = new FormData();
            formData.append("id", vendeurToDelete);

            const response = await fetch("../php/post_delete_vendeur.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            const delete_vendeur = document.querySelector(".delete-vendeur");

            if (data.success) {
                await loadVendeur(currentPage, currentSearch);
                if (delete_vendeur) delete_vendeur.classList.remove("hidden");
            }

            setTimeout(() => {
                if (delete_vendeur) delete_vendeur.classList.add("hidden");
            }, 5000);
        } catch (error) {
            console.log("Erreur:", error);
        }

        modale.classList.add("hidden");
        modale.classList.remove("flex");
        vendeurToDelete = null;
    });
}
