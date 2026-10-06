import {castVote, getIdeas, logout} from "./api.js";

window.onload = () => {
    const accountToggle = document.getElementById("accountToggle");
    const accountMenu = document.getElementById("accountMenu");

    accountToggle.addEventListener("click", () => {
        accountToggle.ariaExpanded = String(!accountToggle.ariaExpanded == "true");
        accountMenu.classList.toggle("open");
    });

    document.getElementById("logout").addEventListener("click", () => {
        logout().then((res) => {
            if (res.status === "yay") {
                location.href = "/login.html";
            } else if (res.status === "login") {
                alert("musisz byc zalogowany zeby sie wylogowac!! duh");
            }
        });
    });
}