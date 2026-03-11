//toggle modal client
const modal = document.getElementById("modal");
const closeModal = document.getElementById("closeModal");
const addClient = document.getElementById("addClient");

if (modal && closeModal && addClient) {
    closeModal.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    });
    addClient.addEventListener("click", () => {
        modal.classList.add("flex");
        modal.classList.remove("hidden");
    });
}
