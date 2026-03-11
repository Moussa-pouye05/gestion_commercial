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
const sidebar = document.querySelector(".sidebar");
const closeId = document.querySelector("#closeSidebar");
const section = document.querySelector("section");

if (menu && sidebar && closeId && section) {
    closeId.addEventListener("click", () => {
       sidebar.classList.add("w-0");
       sidebar.classList.add("sm:w-0");
       sidebar.classList.remove("w-[50%]");
       sidebar.classList.remove("sm:w-[40%]");
    });

    menu.addEventListener("click", () => {
       sidebar.classList.remove("w-0");
       sidebar.classList.remove("sm:w-0");
       sidebar.classList.add("w-[50%]");
       sidebar.classList.add("sm:w-[40%]");
    });

    section.addEventListener("click", (e) =>{
        if(!sidebar.contains(e.target)){
            sidebar.classList.add("w-0");
            sidebar.classList.add("sm:w-0");
            sidebar.classList.remove("w-[50%]");
            sidebar.classList.remove("sm:w-[40%]");
        }
    });
}


